<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\UserModel;

class ResetRoomApiController extends AbstractApiController
{
  protected $roomTable;
  protected $codeTable;
  protected $chatTable;
  protected $userTable;

  public function getRoomTable() {if(!$this->roomTable) { $this->roomTable = $this->getServiceLocator()->get('Application\Model\RoomModelTable'); } return $this->roomTable; }
  public function getCodeTable() {if(!$this->codeTable) { $this->codeTable = $this->getServiceLocator()->get('Application\Model\CodeModelTable'); } return $this->codeTable; }
  public function getChatTable() {if(!$this->chatTable) { $this->chatTable = $this->getServiceLocator()->get('Application\Model\ChatTextModelTable'); } return $this->chatTable; }
  public function getUserTable() { if(!$this->userTable) { $this->userTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); } return $this->userTable; }

  public function getList()
  {
    return $this->makeSuccessJson("no action in reset room");
  }

  public function get($id)
  {
    $roomTable = $this->getRoomTable();
    $codeTable = $this->getCodeTable();
    $chatTable = $this->getChatTable();
    $userTable = $this->getUserTable();

    $codeTable->deleteCodeByRid($id);
    $chatTable->deleteChatTextByRid($id);
    $userTable->inactivateUsersByRid($id);

    return $this->makeSuccessJson("reset room " . $id);
  }
}
