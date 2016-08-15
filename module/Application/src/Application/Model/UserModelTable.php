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
    $rowSet = $this->tableGateway->select(array('room_id' => $room_id));
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

  public function saveUser(UserModel $userModel)
  {
    try{
      $this->saveModel($userModel);
    } catch(Exception $e) {
      return 0;
    }

    return 1;
  }

  public function deleteUser($id)
  {
    return $this->deleteModel($id);
  }
}
