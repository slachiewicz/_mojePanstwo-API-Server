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