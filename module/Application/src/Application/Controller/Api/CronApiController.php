<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\UserModel;

class CronApiController extends AbstractApiController
{
  protected $userTable;

  public function getUserTable() { if(!$this->userTable) { $this->userTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); } return $this->userTable; }

  public function getList()
  {
    // inactivate users not sign in recently.
    $this->getUserTable()->inactivateUsers(10);
    return $this->makeSuccessJson("cron updated");
  }
}
