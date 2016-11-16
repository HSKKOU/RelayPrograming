<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class LobbyController extends AbstractRPController
{
  public function indexAction()
  {
    return new ViewModel();
  }
}
