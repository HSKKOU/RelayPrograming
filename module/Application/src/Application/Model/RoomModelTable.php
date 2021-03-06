<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class RoomModelTable extends AModelTable
{
  public function __construct(TableGateway $tableGateway)
  {
    $this->tableGateway = $tableGateway;
  }

  public function getRoom($id)
  {
    return $this->getModel($id);
  }

  public function getLastRoom()
  {
    return $this->getLastModel();
  }

  public function updateRoomVer($_room_id, $_ver)
  {
    $room = $this->getRoom($_room_id);
    $room->modified = $_ver;
    $this->saveRoom($room);
    return $this->getRoom($_room_id);
  }

  public function updateRoomsVer($_room_ids, $_ver)
  {
    $sql = $this->tableGateway->getSql();
    $update = $sql->update();
    $update->set(array('modified' => date("Y-m-d H:i:s", $_ver)));
    $update->where->in('id', $_room_ids);
    $statement = $sql->prepareStatementForSqlObject($update);
    $updateRes = $statement->execute();
    return 1;
  }

  public function updateRoomTurnUid($_room_id, $_uid)
  {
    $room = $this->getRoom($_room_id);
    $room->turn_user_id = $_uid;
    $this->saveRoom($room);
    return $this->getRoom($_room_id);
  }

  public function saveRoom(RoomModel $roomModel)
  {
    try{
      $this->saveModel($roomModel);
    } catch(Exception $e) {
      return 0;
    }

    return 1;
  }

  public function deleteRoom($id)
  {
    return $this->deleteModel($id);
  }
}
