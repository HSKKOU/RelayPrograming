<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class RoomRestfulController extends AbstractApiController
{
  protected $roomModelTable;

  public function indexAction()
  {
    return new ViewModel();
  }

  public function getList()
  {
    return new JsonModel(array(
      "id" => 11111,
      "text" => 'getList',
    ));
  }

  public function get($id)
  {
//    $gotModel = $this->getRoomModelTable()->getRoomModel($id);
    return new JsonModel(array(
      "id" => $id,
      "text" => 'get',
    ));
  }
}
