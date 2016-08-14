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
