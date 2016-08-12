<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class AbstractRPController extends AbstractRestfulController
{

  // create view model from aliases
  protected function createViewWithParts($_partsAliases)
  {
    $view = new ViewModel();

    foreach ($_partsAliases as $p)
    {
      $pView = $this->createViewModel('parts/' . $p);
      $view->addChild($pView, $p);
    }

    return $view;
  }

  // create each views
  // private function createCodeView() { return $this->createViewModel('parts/chat_view'); }
  // private function createChatView() { return $this->createViewModel('parts/code_view'); }
  // private function createMembersView() { return $this->createViewModel('parts/members_view'); }
  // private function createLoginLB(){ return $this->createViewModel('parts/login_lightbox'); }
  // private function createUserInput() { return $this->createViewModel('parts/user_input'); }

  // create view base
  protected function createViewModel($_alias)
  {
    $view = new ViewModel();
    $view->setTemplate($_alias);

    return $view;
  }
}
