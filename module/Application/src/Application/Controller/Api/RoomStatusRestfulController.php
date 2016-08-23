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
    $localVer = $_data['ver'];
    $room = $this->getRoomTable()->getRoom($room_id);
    $roomInfo = array();
    if (strtotime($room->modified) > strtotime($localVer)) {
      $roomInfo = $this->makeLatestVerJson($localVer, $room->modified, $room_id);
    } else {
      $roomInfo = $this->makeNoUpdatedVerJson($localVer);
    }
    if (isset($_data['user_id']) && +$_data['user_id'] > 0) { $this->getUserTable()->updateLastHB($_data['user_id']); }
    return $this->makeSuccessJson($roomInfo);
  }

  private function makeLatestVerJson($_local_ver, $_server_ver, $_room_id)
  {
    return array(
      'updated' => true,
      'room_ver' => $_server_ver,
      'codes' => $this->getCodeTable()->getUpdatedCodesList($_local_ver, $_room_id),
      'chat' => $this->getUpdatedCTexts($_local_ver, $_room_id),
      'members' => $this->getUpdatedMembers($_local_ver, $_room_id),
    );
  }

  private function getUpdatedCTexts($_local_ver, $_room_id)
  {
    $sql = $this->getSql();
    $select = $sql->select();
    $select->from('chat_texts')
           ->join('users', 'chat_texts.user_id = users.id', array('name', 'color'), $select::JOIN_LEFT)
           ->where->equalTo('chat_texts.room_id', $_room_id)
                  ->greaterThan('chat_texts.created_at', $_local_ver);
    $statement = $sql->prepareStatementForSqlObject($select);
    $resultSet = $statement->execute();

    $chatTexts = array();
    foreach ($resultSet as $row) {
      $chatTexts[] = array(
        'id' => $row['id'],
        'room_id' => $row['room_id'],
        'user_id' => $row['user_id'],
        'user_name' => $row['name'],
        'user_color' => $row['color'],
        'text' => $row['text'],
      );
    }

    return $chatTexts;
  }

  private function getUpdatedMembers($_local_ver, $_room_id)
  {
    // $uMembers = $this->getUserTable()->getUpdatedMembersList($_local_ver, $_room_id);
    $uMembers = $this->getUserTable()->getUsersInRoom($_room_id);
    $room = $this->getRoomTable()->getRoom($_room_id);
    return array(
      "members" => $uMembers,
      "turn_user_id" => $room->turn_user_id,
    );
  }

  private function makeNoUpdatedVerJson($_server_ver)
  {
    return array(
      'updated' => false,
      'room_ver' => $_server_ver,
    );
  }
}
