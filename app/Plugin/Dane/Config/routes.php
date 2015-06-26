<?

Router::mapResources('Dane.subscriptions', array('prefix' => '/dane/'));
Router::connect('/dane/subscriptions/transfer_anonymous', array(
	'plugin' => 'Dane', 
	'controller' => 'Subscriptions',
	'action' => 'transfer_anonymous'
));

Router::connect('/dane/index', array(
	'plugin' => 'Dane', 
	'controller' => 'Dataobjects',
	'action' => 'index'
));

Router::connect('/dane/suggest', array(
	'plugin' => 'Dane', 
	'controller' => 'Dataobjects',
	'action' => 'suggest',
));

Router::connect('/dane/:alias', array(
	'plugin' => 'Dane', 
	'controller' => 'Datasets', 
	'action' => 'view'
), array(
	'pass' => array('alias'),
));

Router::connect('/dane/:dataset/:id', array(
	'plugin' => 'Dane', 
	'controller' => 'Dataobjects',
	'action' => 'view'
), array(
	'id' => '[0-9]+',
	'pass' => array('dataset', 'id'),
));

Router::connect('/dane/:dataset/:id/:action', array(
	'plugin' => 'Dane', 
	'controller' => 'Dataobjects',
), array(
	'id' => '[0-9]+',
	'pass' => array('dataset', 'id'),
));

Router::connect('/dane/:dataset/:action', array(
	'plugin' => 'Dane', 
	'controller' => 'Dataobjects',
), array(
	'id' => '![0-9]+',
	'pass' => array('dataset'),
));

# ObjectUsersManagement
Router::connect('/dane/:dataset/:object_id/users/index', array('plugin' => 'Dane', 'controller' => 'ObjectUsersManagement', 'action' => 'index', '[method]' => 'GET'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+'));
Router::connect('/dane/:dataset/:object_id/users/index', array('plugin' => 'Dane', 'controller' => 'ObjectUsersManagement', 'action' => 'add', '[method]' => 'POST'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+'));
Router::connect('/dane/:dataset/:object_id/users/:user_id', array('plugin' => 'Dane', 'controller' => 'ObjectUsersManagement', 'action' => 'edit', '[method]' => 'PUT'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+', 'user_id' => '[0-9]+'));
Router::connect('/dane/:dataset/:object_id/users/:user_id', array('plugin' => 'Dane', 'controller' => 'ObjectUsersManagement', 'action' => 'delete', '[method]' => 'DELETE'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+', 'user_id' => '[0-9]+'));

# ObjectPagesManagement
Router::connect('/dane/:dataset/:object_id/pages/isEditable', array('plugin' => 'Dane', 'controller' => 'ObjectPagesManagement', 'action' => 'isEditable', '[method]' => 'POST'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+'));
Router::connect('/dane/:dataset/:object_id/pages/setLogo', array('plugin' => 'Dane', 'controller' => 'ObjectPagesManagement', 'action' => 'setLogo', '[method]' => 'POST'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+'));
Router::connect('/dane/:dataset/:object_id/pages/setCover', array('plugin' => 'Dane', 'controller' => 'ObjectPagesManagement', 'action' => 'setCover', '[method]' => 'POST'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+'));
Router::connect('/dane/:dataset/:object_id/pages/deleteLogo', array('plugin' => 'Dane', 'controller' => 'ObjectPagesManagement', 'action' => 'deleteLogo', '[method]' => 'DELETE'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+'));
Router::connect('/dane/:dataset/:object_id/pages/deleteCover', array('plugin' => 'Dane', 'controller' => 'ObjectPagesManagement', 'action' => 'deleteCover', '[method]' => 'DELETE'), array('dataset' => '([a-zA-Z\_]+)', 'object_id' => '[0-9]+'));