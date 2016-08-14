<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use Application\Model\UserModel;
use Application\Model\UserModelTable;

use Application\Model\CodeModel;
use Application\Model\CodeModelTable;

use Application\Model\ChatTextModel;
use Application\Model\ChatTextModelTable;

use Application\Model\RoomModel;
use Application\Model\RoomModelTable;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
{
  public function onBootstrap(MvcEvent $e)
  {
    $eventManager        = $e->getApplication()->getEventManager();
    $moduleRouteListener = new ModuleRouteListener();
    $moduleRouteListener->attach($eventManager);
  }

  public function getConfig()
  {
    return include __DIR__ . '/config/module.config.php';
  }

  public function getAutoloaderConfig()
  {
    return array(
      'Zend\Loader\StandardAutoloader' => array(
        'namespaces' => array(
          __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
        ),
      ),
    );
  }

  public function getServiceConfig(){
    return array(
      'factories' => array(
        'Application\Model\UserModelTable' => function($sm){
          $tableGateway = $sm->get('UserModelTableGateway');
          $table = new UserModelTable($tableGateway);
          return $table;
        },
        'UserModelTableGateway' => function($sm){
          $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
          $resultSetPrototype = new ResultSet();
          $resultSetPrototype->setArrayObjectPrototype(new UserModel());
          return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
        },

        'Application\Model\CodeModelTable' => function($sm){
          $tableGateway = $sm->get('CodeModelTableGateway');
          $table = new CodeModelTable($tableGateway);
          return $table;
        },
        'CodeModelTableGateway' => function($sm){
          $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
          $resultSetPrototype = new ResultSet();
          $resultSetPrototype->setArrayObjectPrototype(new CodeModel());
          return new TableGateway('codes', $dbAdapter, null, $resultSetPrototype);
        },

        'Application\Model\ChatTextModelTable' => function($sm){
          $tableGateway = $sm->get('ChatTextModelTableGateway');
          $table = new ChatTextModelTable($tableGateway);
          return $table;
        },
        'ChatTextModelTableGateway' => function($sm){
          $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
          $resultSetPrototype = new ResultSet();
          $resultSetPrototype->setArrayObjectPrototype(new ChatTextModel());
          return new TableGateway('chat_texts', $dbAdapter, null, $resultSetPrototype);
        },

        'Application\Model\RoomModelTable' => function($sm){
          $tableGateway = $sm->get('RoomModelTableGateway');
          $table = new RoomModelTable($tableGateway);
          return $table;
        },
        'RoomModelTableGateway' => function($sm){
          $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
          $resultSetPrototype = new ResultSet();
          $resultSetPrototype->setArrayObjectPrototype(new RoomModel());
          return new TableGateway('rooms', $dbAdapter, null, $resultSetPrototype);
        },
      ),
    );
  }
}
