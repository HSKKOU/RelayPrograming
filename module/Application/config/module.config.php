<?php
namespace Application;

return array(
  'router' => array(
    'routes' => array(
      'home' => array(
        'type' => 'Zend\Mvc\Router\Http\Literal',
        'options' => array(
          'route'    => '/',
          'defaults' => array(
            '__NAMESPACE__' => 'Application\Controller',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
          'default' => array(
            'type'    => 'Segment',
            'options' => array(
              'route'    => ':room_id[/:controller][/]',
              'constraints' => array(
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => array(
                'controller' => 'Index',
                'action'     => 'index',
              ),
            ),
          ),
        ),
      ),
      'api' => array(
        'type'    => 'Literal',
        'options' => array(
          'route'    => '/api',
          'defaults' => array(
            '__NAMESPACE__' => 'Application\Controller\Api',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => array(
          'default' => array(
            'type'    => 'Segment',
            'options' => array(
              'route'    => '/:controller[/:id][/]',
              'constraints' => array(
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'id' => '[0-9]+',
              ),
              'defaults' => array(
                'action' => null,
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'service_manager' => array(
    'abstract_factories' => array(
      'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
      'Zend\Log\LoggerAbstractServiceFactory',
    ),
    'factories' => array(
      'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
    ),
  ),
  'translator' => array(
    'locale' => 'en_US',
    'translation_file_patterns' => array(
      array(
        'type'     => 'gettext',
        'base_dir' => __DIR__ . '/../language',
        'pattern'  => '%s.mo',
      ),
    ),
  ),
  'controllers' => array(
    'invokables' => array(
      'Application\Controller\Index' => Controller\IndexController::class,
      'Application\Controller\Watch' => Controller\WatchController::class,
      'Application\Controller\Admin' => Controller\AdminController::class,

      'Application\Controller\Api\User' => Controller\Api\UserRestfulController::class,
      'Application\Controller\Api\Code' => Controller\Api\CodeRestfulController::class,
    ),
  ),
  'view_manager' => array(
    'display_not_found_reason' => true,
    'display_exceptions'       => true,
    'doctype'                  => 'HTML5',
    'not_found_template'       => 'error/404',
    'exception_template'       => 'error/index',
    'template_map' => array(
      'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',

      // each page
      'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
      'application/index/watch' => __DIR__ . '/../view/application/index/watch.phtml',
      'application/index/admin' => __DIR__ . '/../view/application/index/admin.phtml',

      // error page
      'error/404'               => __DIR__ . '/../view/error/404.phtml',
      'error/index'             => __DIR__ . '/../view/error/index.phtml',

      // view parts
      'parts/loginlb'           => __DIR__ . '/../view/parts/login_lightbox.phtml',
      'parts/code_view'         => __DIR__ . '/../view/parts/code_view.phtml',
      'parts/chat_view'         => __DIR__ . '/../view/parts/chat_view.phtml',
      'parts/user_input'        => __DIR__ . '/../view/parts/user_input.phtml',
      'parts/members_view'      => __DIR__ . '/../view/parts/members_view.phtml',
    ),
    'template_path_stack' => array(
      __DIR__ . '/../view',
    ),
    'strategies' => array(
      'ViewJsonStrategy',
    ),
  ),
  // Placeholder for console routes
  'console' => array(
    'router' => array(
      'routes' => array(
      ),
    ),
  ),
);
