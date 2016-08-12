<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AdminController extends AbstractRPController
{
  public function getList()
  {
    return new ViewModel();
  }

  public function get($room_id)
  {
    return new ViewModel();
  }

  public function create($data)
  {
    return new ViewModel();
  }


  public function adminAction()
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
