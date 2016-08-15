<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class ChatTextModelTable extends AModelTable
{
  public function __construct(TableGateway $tableGateway)
  {
    $this->tableGateway = $tableGateway;
  }

  public function getChatText($id)
  {
    return $this->getModel($id);
  }

  public function getLastChatText()
  {
    return $this->getLastModel();
  }

  public function getChatTextListByRoomId($_room_id)
  {
    $room_id = (int)$_room_id;
    $rowSet = $this->tableGateway->select(array('room_id' => $room_id));
    $chatTexts = array();
    foreach ($rowSet as $row) {
      if(!$row){
        $row = new ChatTextModel();
      }
      $chatTexts[] = $row->exchangeToArray();
    }

    return $chatTexts;
  }

  public function getUpdatedCTextsList($_ver, $_room_id)
  {
    return $this->getUpdatedModelList($_ver, 'created_at', $_room_id, 'room_id');
  }

  public function saveChatText(ChatTextModel $chatTextModel)
  {
    try{
      $this->saveData($chatTextModel->exchangeToArrayWithoutCreatedAt());
    } catch(Exception $e) {
      return 0;
    }

    return 1;
  }

  public function deleteChatText($id)
  {
    return $this->deleteModel($id);
  }
}
