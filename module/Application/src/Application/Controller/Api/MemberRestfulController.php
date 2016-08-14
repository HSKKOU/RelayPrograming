<?php
namespace Application\Controller\Api;

use Zend\View\Model\JsonModel;

use Application\Model\UserModel;

class MemberRestfulController extends AbstractApiController
{
  protected $userTable;
  protected $roomTable;

  public function getUserTable()
  {
    if(!$this->userTable) { $this->userTable = $this->getServiceLocator()->get('Application\Model\UserModelTable'); }
    return $this->userTable;
  }
  public function getRoomTable()
  {
    if(!$this->roomTable) { $this->roomTable = $this->getServiceLocator()->get('Application\Model\RoomModelTable'); }
    return $this->roomTable;
  }

  public function getList()
  {
    $room_id = 0;
    if (isset($_GET['room_id']) && preg_match('/[0-9]+/', $_GET['room_id'])) {
      $room_id = $_GET['room_id'];
    }

    $users = $this->getUserTable()->getUsersInRoom($room_id);
    $room = $this->getRoomTable()->getRoom($room_id);
    $members = array(
      'members' => $users,
      'turn_user_id' => $room->turn_user_id,
    );

    return $this->makeSuccessJson($members);
  }
}
