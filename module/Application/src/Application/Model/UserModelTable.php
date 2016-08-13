<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class UserModelTable
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

  public function getUser($id)
  {
    $id = (int)$id;
    $rowSet = $this->tableGateway->select(array('id' => $id));
    $row = $rowSet->current();
    if(!$row) {
      throw new \Exception("Could not find roe $id");
    }

    return $row;
  }

  public function getLastUser()
  {
    $id = $this->getLastId();
    $rowSet = $this->tableGateway->select(array('id' => $id));
    $row = $rowSet->current();
    if(!$row) {
      throw new \Exception("Could not find roe $id");
    }

    return $row;
  }

  public function saveUser(UserModel $userModel)
  {
    $data = array(
      'name'  => $userModel->name,
      'color' => $userModel->color,
    );

    $id = (int)$userModel->id;
    if ($id == 0) {
      return $this->tableGateway->insert($data);
    } else {
      if ($this->getUser($id)) {
        $this->tableGateway->update($data, array('id' => $id));
      } else {
        return new \Exception("UserModel id dose not exist");
      }
    }
  }

  public function deleteUser($id)
  {
    return $this->tableGateway->delete(array('id' => (int)$id));
  }


  public function getLastId()
  {
    return $this->tableGateway->lastInsertValue;
  }
}
