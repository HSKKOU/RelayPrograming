<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;

class UserModelTable extends AModelTable
{
  public function __construct(TableGateway $tableGateway)
  {
    $this->tableGateway = $tableGateway;
  }

  public function getUser($id)
  {
    return $this->getModel($id);
  }

  public function getLastUser()
  {
    return $this->getLastModel();
  }

  public function saveUser(UserModel $userModel)
  {
    $data = $this->exchangeToArrayWithoutId();
    $this->saveModel($userModel->id, $data);
  }

  public function deleteUser($id)
  {
    return $this->deleteModel($id);
  }
}
