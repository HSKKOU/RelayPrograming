<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\RoomModel;

class RoomRestfulController extends AbstractApiController
{
  protected $roomTable;

  public function getRoomTable()
  {
    if(!$this->roomTable)
    {
      $sm = $this->getServiceLocator();
      $this->roomTable = $sm->get('Application\Model\RoomModelTable');
    }

    return $this->roomTable;
  }

  public function getList()
  {
    return $this->makeSuccessJson($this->getListRaw());
  }

  public function get($id)
  {
    $gotModel = $this->getRoomTable()->getRoom($id);
    return $this->makeSuccessJson($gotModel->exchangeToArray());
  }

  public function create($data)
  {
    $roomModel = new RoomModel();
    $roomModel->exchangeArray($data);

    $result = $this->getRoomTable()->saveCode($roomModel);
    if ($result == 1) {
      $savedData = $this->getRoomTable()->getLastRoom();
      return $this->makeSuccessJson($savedData);
    }

    return $this->makeFailedJson(array());
  }






  /* Utilities */
  public function getListRaw()
  {
    $data = array();

    $rowSet = $this->getRoomTable()->fetchAll();
    foreach ($rowSet as $row) {
      $data[] = $row->exchangeToArray();
    }

    return $data;
  }
}
