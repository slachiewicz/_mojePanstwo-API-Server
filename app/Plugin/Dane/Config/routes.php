<?

// TODO document subscriptions
Router::mapResources('Dane.subscriptions', array('prefix' => '/dane/'));
Router::connect('/dane/subscriptions/transfer_anonymous', array(
    'plugin' => 'Dane',
    'controller' => 'Subscriptions',
    'action' => 'transfer_anonymous'
));

Router::connect('/dane/:dataset', array(
    'plugin' => 'Dane',
    'controller' => 'Dataobjects',
    'action' => 'index'
), array(
    'dataset' => '[a-zA-Z]+',
    'pass' => array('dataset'),
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
    'action' => '(feed|subscribe|unsubscribe)',
    'pass' => array('dataset', 'id'),
));

Router::connect('/dane/:dataset/:id/:layer', array(
    'plugin' => 'Dane',
    'controller' => 'Dataobjects',
    'action' => 'view_layer'
), array(
    'id' => '[0-9]+',
    'pass' => array('dataset', 'id', 'layer'),
));
