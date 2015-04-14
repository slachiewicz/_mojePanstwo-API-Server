<?

Router::mapResources('users');
Router::connect('/paszport/info/*', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'info'));
Router::connect('/paszport/login', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'login'));
Router::connect('/paszport/register', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'register'));

Router::connect('/paszport/user/setUserName', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'setUserName'));
Router::connect('/paszport/user/setEmail', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'setEmail'));
Router::connect('/paszport/user/setPassword', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'setUserPassword'));
Router::connect('/paszport/user/deletePaszport', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'deletePaszport'));
Router::connect('/paszport/user/registerFromFacebook', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'registerFromFacebook'));
Router::connect('/paszport/user/forgot', array('plugin' => 'Paszport', 'controller' => 'Users', 'action' => 'forgot'));