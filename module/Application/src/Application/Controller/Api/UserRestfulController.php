<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\UserModel;

class UserRestfulController extends AbstractApiController
{
  protected $userTable;

  public function getUserTable()
  {
    if(!$this->userTable)
    {
      $sm = $this->getServiceLocator();
      $this->userTable = $sm->get('Application\Model\UserModelTable');
    }

    return $this->userTable;
  }

  public function getList()
  {
    return $this->makeSuccessJson($this->getListRaw());
  }

  public function get($id)
  {
    $gotModel = $this->getUserTable()->getUser($id);
    return $this->makeSuccessJson(array(
      'id' => $gotModel->id,
      'name' => $gotModel->name,
      'color' => $gotModel->color,
    ));
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
        if(!isset($data['id'])){ return $this->makeFailedJson("no user id for sign in"); }
        return $this->signIn(+$data['id'], +$data['room_id']);
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
    $existUser = $this->get($_user_id);
    if ($existUser->room_id != $_room_id) {
      return $this->makeFailedJson(array());
    }

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
