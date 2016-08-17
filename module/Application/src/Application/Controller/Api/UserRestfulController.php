<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\UserModel;
use Application\Model\RoomModel;

class UserRestfulController extends AbstractApiController
{
  protected $userTable;
  protected $roomTable;

  public function getUserTable() { if(!$this->userTable) { $this->userTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); } return $this->userTable; }
  public function getRoomTable() { if(!$this->roomTable) { $this->roomTable = $this->getServiceLocator()->get('Application\Model\RoomModelTable'); } return $this->roomTable; }

  public function getList()
  {
    return $this->makeSuccessJson($this->getListRaw());
  }

  public function get($id)
  {
    $gotModel = $this->getUserTable()->getUser($id);
    return $this->makeSuccessJson($gotModel->exchangeToArray());
  }

  public function create($data)
  {
    switch($data['type']){
      case 'signUp':
        $newModel = new UserModel();
        $newModel->exchangeArray($data);
        return $this->signUp($newModel);
      case 'signIn':
        if(!isset($data['room_id'])){ return $this->makeFailedJson("no room_id for sign in"); }
        if(!isset($data['user_id'])){ return $this->makeFailedJson("no user id for sign in"); }
        return $this->signIn(+$data['user_id'], +$data['room_id']);
      case 'signOut':
        return $this->signOut(+$data['user_id']);
      default:
        break;
    }
    return $this->makeFailedJson(array());
  }

  // register User
  private function signUp($_user_model)
  {
    $result = $this->getUserTable()->saveUser($_user_model);
    if ($result == 1) {
      $savedData = $this->getUserTable()->getLastUser();
      return $this->makeSuccessJson($savedData);
    }

    return $this->makeFailedJson(array());
  }

  // login User
  private function signIn($_user_id, $_room_id)
  {
    $existUser = $this->getUserTable()->getUser($_user_id);
    if ($existUser->room_id != $_room_id) {
      return $this->makeFailedJson(array());
    }

    $room = $this->getRoomTable()->getRoom($_room_id);
    if ($room->turn_user_id == 0) {
      $room->turn_user_id = $_user_id;
      $this->getRoomTable()->saveRoom($room);
    }

    $this->getRoomTable()->updateRoomVer($_room_id, date("Y-m-d H:i:s", time()));

    return $this->makeSuccessJson($existUser);
  }

  // sign out user
  private function signOut($_user_id)
  {
    $existUser = $this->getUserTable()->signOutUser($_user_id);
    return $this->makeSuccessJson($existUser);
  }







  /* Utilities */
  public function getListRaw()
  {
    $data = array();

    $rowSet = $this->getUserTable()->fetchAll();
    foreach ($rowSet as $row) {
      $data[] = array(
        'id' => $row->id,
        'name' => $row->name,
        'color' => $row->color,
      );
    }

    return $data;
  }
}
