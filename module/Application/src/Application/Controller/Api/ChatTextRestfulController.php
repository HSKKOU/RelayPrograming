<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\ChatTextModel;
use Application\Model\UserModel;

class ChatTextRestfulController extends AbstractApiController
{
  protected $chatTextTable;
  protected $userTextTable;

  public function getChatTextTable()
  {
    if(!$this->chatTextTable) { $this->chatTextTable = $this->getServiceLocator()->get('Application\Model\ChatTextModelTable'); }
    return $this->chatTextTable;
  }
  public function getUserTextTable()
  {
    if(!$this->userTextTable) { $this->userTextTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); }
    return $this->userTextTable;
  }

  public function getList()
  {
    $room_id = 0;
    if (isset($_GET['room_id']) && preg_match('/[0-9]+/', $_GET['room_id'])) {
      $room_id = $_GET['room_id'];
    }

    $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    $sql = new \Zend\Db\Sql\Sql($adapter);
    $select = $sql->select();
    $select->from('chat_texts')
           ->join('users', 'chat_texts.user_id = users.id', array('name', 'color'), $select::JOIN_LEFT)
           ->where(array('chat_texts.room_id' => $room_id));
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
      $userModel = $this->getUserTextTable()->getUser($savedData->user_id);
      $cTextData = $savedData->exchangeToArray();
      $cTextData['user_name'] = $userModel->name;
      $cTextData['user_color'] = $userModel->color;
      return $this->makeSuccessJson($cTextData);
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
