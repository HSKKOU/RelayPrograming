<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class WatchController extends AbstractRPController
{
  public function getList()
  {
    return new ViewModel();
  }

  public function get($room_id)
  {
    return new JsonModel(array(
      'room_id' => $room_id,
    ));
  }

  public function create($data)
  {
    return new ViewModel();
  }


  public function indexAction()
  {
    $viewAliases = array(
      'code_view',
      'chat_view',
      'members_view',
    );

    $view = $this->createViewWithParts($viewAliases);

    return $view;
  }
}
