<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class AdminController extends AbstractRPController
{
  public function getList()
  {
    return new JsonModel();
  }

  public function get($room_id)
  {
    return new JsonModel(array(
      'room_id' => $room_id,
    ));
  }

  public function create($data)
  {
    return new JsonModel();
  }


  public function indexAction()
  {
    $viewAliases = array(
      'code_view',
      'chat_view',
      'members_view',
      'user_input'
    );

    $view = $this->createViewWithParts($viewAliases);

    return $view;
  }
}
