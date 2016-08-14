<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\ChatTextModel;

class ChatTextRestfulController extends AbstractApiController
{
  protected $chatTextTable;

  public function getChatTextTable()
  {
    if(!$this->chatTextTable)
    {
      $sm = $this->getServiceLocator();
      $this->chatTextTable = $sm->get('Application\Model\ChatTextModelTable');
    }

    return $this->chatTextTable;
  }

  public function getList()
  {
    $room_id = 0;
    if (isset($_GET['room_id']) && preg_match('/[0-9]+/', $_GET['room_id'])) {
      $room_id = $_GET['room_id'];
    }
    $chatTexts = $this->getChatTextTable()->getChatTextListByRoomId($room_id);
    return $this->makeSuccessJson($chatTexts);
  }

  public function get($id)
  {
    $gotModel = $this->getChatTextTable()->getChatText($id);
    return $this->makeSuccessJson($gotModel->exchangeToArray());
  }

  public function create($data)
  {
    $chatTextModel = new ChatTextModel();
    $chatTextModel->exchangeArray($data);

    $result = $this->getChatTextTable()->saveChatText($chatTextModel);
    if ($result == 1) {
      $savedData = $this->getChatTextTable()->getLastChatText();
      return $this->makeSuccessJson($savedData);
    }

    return $this->makeFailedJson(array());
  }







  /* Utilities */
  public function getListRaw()
  {
    $data = array();

    $rowSet = $this->getChatTextTable()->fetchAll();
    foreach ($rowSet as $row) {
      $data[] = $row->exchangeToArray();
    }

    return $data;
  }
}
