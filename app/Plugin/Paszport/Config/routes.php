<?

Router::mapResources('users');
Router::connect('/paszport/info/*', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'info'));