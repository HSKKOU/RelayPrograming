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
    $newModel = new UserModel();
    $newModel->exchangeArray($data);
    $result = $this->getUserTable()->saveUser($newModel);
    $savedData = array();
    if ($result == 1) {
      $fetchList = $this->getListRaw();
      $savedData = $fetchList[count($fetchList)-1];
    }

    return $this->makeSuccessJson($savedData);
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
