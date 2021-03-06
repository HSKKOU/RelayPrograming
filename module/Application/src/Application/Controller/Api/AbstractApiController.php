<?php
namespace Application\Controller\Api;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class AbstractApiController extends AbstractRestfulController
{
  protected function makeSuccessJson($_data)
  {
    return new JsonModel(array(
      'result' => true,
      'data' => $_data,
    ));
  }

  protected function makeFailedJson($_data, $_opt = '')
  {
    return new JsonModel(array(
      'result' => false,
      'data' => $_data,
      'opt' => $_opt,
    ));
  }

  protected function makeJson($_res, $_data)
  {
    return new JsonModel(array(
      'result' => $_res,
      'data' => $_data,
    ));
  }

  protected function getSql()
  {
    $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    return new \Zend\Db\Sql\Sql($adapter);
  }
}
