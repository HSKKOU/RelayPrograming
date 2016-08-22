<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\UserModel;

class CronApiController extends AbstractApiController
{
  protected $userTable;
  protected $roomTable;

  public function getUserTable() { if(!$this->userTable) { $this->userTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); } return $this->userTable; }
  public function getRoomTable() {if(!$this->roomTable) { $this->roomTable = $this->getServiceLocator()->get('Application\Model\RoomModelTable'); } return $this->roomTable; }

  public function getList()
  {
    // inactivate users not sign in recently.
    $inactiveRoomsInfo = $this->getUserTable()->inactivateUsers(strtotime('-1 min'));
    if (!$inactiveRoomsInfo['res']) { return $this->makeSuccessJson("no cron updated"); }
    $this->getRoomTable()->updateRoomsVer($inactiveRoomsInfo['rids'], $inactiveRoomsInfo['now']);
    return $this->makeSuccessJson("cron updated");
  }
}
