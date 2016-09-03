<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\CodeModel;
use Application\Model\RoomModel;
use Application\Model\UserModel;

class CodeRestfulController extends AbstractApiController
{
  protected $codeTable;
  protected $roomTable;
  protected $userTable;

  public function getCodeTable() { if(!$this->codeTable) { $this->codeTable = $this->getServiceLocator()->get('Application\Model\CodeModelTable'); } return $this->codeTable; }
  public function getRoomTable() { if(!$this->roomTable) { $this->roomTable = $this->getServiceLocator()->get('Application\Model\RoomModelTable'); } return $this->roomTable; }
  public function getUserTable() { if(!$this->userTable) { $this->userTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); } return $this->userTable; }

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
    if (strlen($data['code']) > 100) {
      return $this->makeFailedJson("This code is more than 100 characters");
    }

    $codeModel = new CodeModel();
    $codeModel->exchangeArray($data);
    $nowStr = date("Y-m-d H:i:s");
    $codeModel->created_at = $nowStr;
    $codeModel->updated_at = $nowStr;

    $result = $this->getCodeTable()->saveCode($codeModel);
    if ($result == 1) {
      $savedData = $this->getCodeTable()->getLastCode();
      $this->getRoomTable()->updateRoomVer($savedData->room_id, $savedData->created_at);
      $this->changeTurnUser($savedData->room_id);
      return $this->makeSuccessJson($savedData);
    }

    return $this->makeFailedJson("Failed post code");
  }
  private function changeTurnUser($_room_id)
  {
    $room = $this->getRoomTable()->getRoom($_room_id);
    $currentTurnUid = $room->turn_user_id;
    if ($currentTurnUid == 0) { return; }

    $nextUser = $this->getUserTable()->getNextTurnUserInRoom($_room_id, $currentTurnUid);
    $this->getRoomTable()->updateRoomTurnUid($_room_id, $nextUser->id);
  }

  public function update($id, $data)
  {
    $nowStr = date("Y-m-d H:i:s");
    try{
      $existCode = $this->getCodeTable()->getCode($id);
      $existCode->code = $data['code'];
      $existCode->updated_at = $nowStr;
      $this->getCodeTable()->saveModel($existCode);
    } catch(Exception $e) {
      return $this->makeFailedJson(array());
    }

    $this->getRoomTable()->updateRoomVer($data['room_id'], $nowStr);
    return $this->makeSuccessJson($existCode);
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
