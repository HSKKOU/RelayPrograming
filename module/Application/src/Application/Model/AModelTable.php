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

  public function saveModel(AModel $_model)
  {
    $id = (int)$_model->id;
    $data = $_model->exchangeToArrayWithoutId();

    if ($id == 0) {
      $this->tableGateway->insert($data);
    } else {
      if ($this->get($id)) {
        $this->tableGateway->update($data, array('id' => $id));
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
