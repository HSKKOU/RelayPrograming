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
                  ->greaterThan('id', $_currentTurnUid);
    $select->limit(1);
    $nextUser = $this->tableGateway->selectWith($select)->current();
    if ($nextUser) { return $nextUser; }

    $select = $this->tableGateway->getSql()->select();
    $select->where->equalTo('room_id', $_room_id);
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
    $user->modified = $_ver;
    $this->saveUser($user);
    return $this->getUser($_user_id);
  }

  public function deleteUser($id)
  {
    return $this->deleteModel($id);
  }
}
