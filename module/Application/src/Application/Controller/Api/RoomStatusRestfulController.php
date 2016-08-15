<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\RoomModel;
use Application\Model\UserModel;
use Application\Model\CodeModel;
use Application\Model\ChatTextModel;

class RoomStatusRestfulController extends AbstractApiController
{
  protected $roomTable;
  protected $userTable;
  protected $codeTable;
  protected $chatTextTable;

  public function getRoomTable() {if(!$this->roomTable) { $this->roomTable = $this->getServiceLocator()->get('Application\Model\RoomModelTable'); } return $this->roomTable; }
  public function getUserTable() {if(!$this->userTable) { $this->userTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); } return $this->userTable; }
  public function getCodeTable() {if(!$this->codeTable) { $this->codeTable = $this->getServiceLocator()->get('Application\Model\CodeModelTable'); } return $this->codeTable; }
  public function getChatTextTable() {if(!$this->chatTextTable) { $this->chatTextTable = $this->getServiceLocator()->get('Application\Model\ChatTextModelTable'); } return $this->chatTextTable; }


  // post
  public function create($data)
  {
    if (!isset($data['type'])) { return $this->makeFailedJson(array()); }
    $type = $data['type'];
    switch ($type) {
      case 'hb': return $this->heartBeat($data);
      default: break;
    }
    return $this->makeFailedJson(array());
  }

  private function heartBeat($_data)
  {
    $room_id = $_data['room_id'];
    $room = $this->getRoomTable()->getRoom($room_id);
    $roomInfo = array();
    if (strtotime($room->modified) > strtotime($_data['ver'])) {
      $roomInfo = $this->makeLatestVerJson($room->modified, $room_id);
    } else {
      $roomInfo = $this->makeNoUpdatedVerJson($room->modified);
    }
    return $this->makeSuccessJson($roomInfo);
  }

  private function makeLatestVerJson($_ver, $_room_id)
  {
    return array(
      'updated' => true,
      'room_ver' => $_ver,
      'codes' => $this->getCodeTable()->getUpdatedCodesList($_ver, $_room_id),
      'chat' => $this->getChatTextTable()->getUpdatedCTextsList($_ver, $_room_id),
      'members' => $this->getUpdatedMembers($_ver, $_room_id),
    );
  }

  private function getUpdatedMembers($_ver, $_room_id)
  {
    $uMembers = $this->getUserTable()->getUpdatedMembersList($_ver, $_room_id);
    $room = $this->getRoomTable()->getRoom($_room_id);
    return array(
      "members" => $uMembers,
      "turn_uid" => $room->turn_user_id,
    );
  }

  private function makeNoUpdatedVerJson($_ver)
  {
    return array(
      'updated' => false,
      'room_ver' => $_ver,
    );
  }
}
