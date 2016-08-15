<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class CodeModelTable extends AModelTable
{
  public function __construct(TableGateway $tableGateway)
  {
    $this->tableGateway = $tableGateway;
  }

  public function getCode($id)
  {
    return $this->getModel($id);
  }

  public function getLastCode()
  {
    return $this->getLastModel();
  }

  public function getCodeListByRoomId($_room_id)
  {
    $room_id = (int)$_room_id;
    $rowSet = $this->tableGateway->select(array('room_id' => $room_id));
    $codes = array();
    foreach ($rowSet as $row) {
      if(!$row){
        $row = new CodeModel();
      }
      $codes[] = $row->exchangeToArray();
    }

    return $codes;
  }

  public function getUpdatedCodesList($_ver, $_room_id)
  {
    return $this->getUpdatedModelList($_ver, 'created_at', $_room_id, 'room_id');
  }

  public function saveCode(CodeModel $codeModel)
  {
    try{
      $this->saveData($codeModel->exchangeToArrayWithoutCreatedAt());
    } catch(Exception $e) {
      return 0;
    }

    return 1;
  }

  public function deleteCode($id)
  {
    return $this->deleteModel($id);
  }
}
