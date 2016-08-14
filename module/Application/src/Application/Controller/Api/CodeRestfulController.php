<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\CodeModel;

class CodeRestfulController extends AbstractApiController
{
  protected $codeTable;

  public function getCodeTable()
  {
    if(!$this->codeTable)
    {
      $sm = $this->getServiceLocator();
      $this->codeTable = $sm->get('Application\Model\CodeModelTable');
    }

    return $this->codeTable;
  }

  public function getList()
  {
    $room_id = 0;
    if (isset($_GET['room_id']) && preg_match('/[0-9]+/', $_GET['room_id'])) {
      $room_id = $_GET['room_id'];
    }
    $codes = $this->getCodeTable()->getCodeListByRoomId($room_id);
    return $this->makeSuccessJson($codes);
  }

  public function get($id)
  {
    $gotModel = $this->getCodeTable()->getCode($id);
    return $this->makeSuccessJson($gotModel->exchangeToArray());
  }

  public function create($data)
  {
    $codeModel = new CodeModel();
    $codeModel->exchangeArray($data);

    $result = $this->getCodeTable()->saveCode($codeModel);
    if ($result == 1) {
      $savedData = $this->getCodeTable()->getLastCode();
      return $this->makeSuccessJson($savedData);
    }

    return $this->makeFailedJson(array());
  }







  /* Utilities */
  public function getListRaw()
  {
    $data = array();

    $rowSet = $this->getCodeTable()->fetchAll();
    foreach ($rowSet as $row) {
      $data[] = $row->exchangeToArray();
    }

    return $data;
  }
}
