<?php

return array(
  'db' => array(
    'driver' => 'Pdo',
    'dsn' => 'mysql:host=localhost;dbname=relay_programing;charset=utf8;',
    'username' => 'root',
    'password' => 'hskk1231',
  ),
  'service_manager' => array(
    'factories' => array(
      'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
    ),
  ),
);
