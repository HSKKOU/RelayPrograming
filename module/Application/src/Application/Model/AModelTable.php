<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class AModelTable
{
  protected $tableGateway;

  public function __construct(TableGateway $tableGateway)
  {
    $this->tableGateway = $tableGateway;
  }

  public function fetchAll()
  {
    $resultSet = $this->tableGateway->select();
    return $resultSet;
  }

  public function getModel($_id)
  {
    $id = (int)$_id;
    $rowSet = $this->tableGateway->select(array('id' => $id));
    $row = $rowSet->current();
    if(!$row) {
      return array();
    }

    return $row;
  }

  public function getLastModel()
  {
    $id = $this->getLastId();
    $rowSet = $this->tableGateway->select(array('id' => $id));
    $row = $rowSet->current();
    if(!$row) {
      throw new \Exception("Could not find $id");
    }

    return $row;
  }

  public function getUpdatedModelList($_ver, $_ver_c, $_room_id, $_room_id_c)
  {
    $room_id = (int)$_room_id;
    $select = $this->tableGateway->getSql()->select();
    $select->where->equalTo($_room_id_c, $_room_id)
                  ->greaterThan($_ver_c, $_ver);
    $rowSet = $this->tableGateway->selectWith($select);
    $models = array();
    foreach ($rowSet as $row) {
      if(!$row){ continue; }
      $models[] = $row->exchangeToArray();
    }

    return $models;
  }

  public function saveModel(AModel $_model)
  {
    $this->saveData($_model->exchangeToArray());
  }

  public function saveData($_data)
  {
    $id = (int)$_data['id'];

    if ($id == 0) {
      $this->tableGateway->insert($_data);
    } else {
      if ($this->get($id)) {
        $this->tableGateway->update($_data, array('id' => $id));
      } else {
        throw new \Exception("This Model's id dose not exist");
      }
    }
  }

  public function deleteModel($_id)
  {
    return $this->tableGateway->delete(array('id' => (int)$_id));
  }


  public function getLastId()
  {
    return $this->tableGateway->lastInsertValue;
  }
}
