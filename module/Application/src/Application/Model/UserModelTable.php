<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class UserModelTable extends AModelTable
{
  public function __construct(TableGateway $tableGateway)
  {
    $this->tableGateway = $tableGateway;
  }

  public function getUser($id)
  {
    return $this->getModel($id);
  }

  public function getUsersInRoom($_room_id)
  {
    $room_id = (int)$_room_id;
    $select = $this->tableGateway->getSql()->select();
    $select->where->equalTo('room_id', $_room_id)
                  ->equalTo('is_active', 1);
    $rowSet = $this->tableGateway->selectWith($select);
    $users = array();
    foreach ($rowSet as $row) {
      if(!$row) { continue; }
      $users[] = $row;
    }

    return $users;
  }

  public function getUpdatedMembersList($_ver, $_room_id)
  {
    return $this->getUpdatedModelList($_ver, 'created_at', $_room_id, 'room_id');
  }

  public function getLastUser()
  {
    return $this->getLastModel();
  }

  public function getNextTurnUserInRoom($_room_id, $_currentTurnUid)
  {
    $select = $this->tableGateway->getSql()->select();
    $select->where->equalTo('room_id', $_room_id)
                  ->greaterThan('id', $_currentTurnUid)
                  ->equalTo('is_active', 1);
    $select->limit(1);
    $nextUser = $this->tableGateway->selectWith($select)->current();
    if ($nextUser) { return $nextUser; }

    $select = $this->tableGateway->getSql()->select();
    $select->where->equalTo('room_id', $_room_id)
                  ->equalTo('is_active', 1);
    $select->limit(1);
    $nextUser = $this->tableGateway->selectWith($select)->current();
    return $nextUser;
  }

  public function signOutUser($_user_id)
  {
    $user = $this->getUser($_user_id);
    $user->is_active = 0;
    $this->saveUser($user);
    return $this->getUser($_user_id);
  }

  public function saveUser(UserModel $userModel)
  {
    try{
      $this->saveModel($userModel);
    } catch(Exception $e) {
      return 0;
    }

    return 1;
  }

  public function updateVer($_user_id, $_ver)
  {
    $user = $this->getUser($_user_id);
    $user->last_hb = $_ver;
    $this->saveUser($user);
    return $this->getUser($_user_id);
  }

  public function updateLastHB($_user_id)
  {
    return $this->updateVer($_user_id, date("Y-m-d H:i:s"));
  }

  public function deleteUser($id)
  {
    return $this->deleteModel($id);
  }



  public function inactivateUsers($_min_before)
  {
    $now = time();
    $minBefore = $_min_before;
    $minBeforeStr = date("Y-m-d H:i:s", $minBefore);
    $sql = $this->tableGateway->getSql();
    $select = $sql->select();
    $select->where->lessThan('last_hb', $minBeforeStr)
                  ->equalTo('is_active', '1');
    $selectRes = $this->tableGateway->selectWith($select);

    if (!$selectRes || count($selectRes) == 0) { return array('res' => false); }
    $updateRoomIds = array();
    foreach ($selectRes as $k => $r) {
      $updateRoomIds[] = $r->room_id;
    }

    $update = $sql->update();
    $update->set(array('is_active' => '0'));
    $update->where->lessThan('last_hb', $minBeforeStr)
                  ->equalTo('is_active', '1');
    $statement = $sql->prepareStatementForSqlObject($update);
    $updateRes = $statement->execute();

    return array('res' => true, 'now' => $now, 'rids' => $updateRoomIds);
  }
}
