<?

Router::connect('/dane/dataset/:alias/:object_id/layers/:layer', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'layer'));

Router::connect('/powiadomienia/alertsQueries/:id', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'alertsQueries'));

Router::connect('/dane/search', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'search'));

Router::connect('/dane/dataset/:alias', array('plugin' => 'Dane', 'controller' => 'datasets', 'action' => 'info'));
Router::connect('/dane/dataset/:alias/:action', array('plugin' => 'Dane', 'controller' => 'datasets'));

Router::connect('/dane/datachannel/:alias', array('plugin' => 'Dane', 'controller' => 'datachannels', 'action' => 'info'));
Router::connect('/dane/datachannel/:alias/:action', array('plugin' => 'Dane', 'controller' => 'datachannels'));

Router::connect('/dane/:alias/:object_id', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'view'), array('object_id' => '[0-9]+'));

Router::connect('/dane/:alias/:object_id/feed', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'feed'), array('object_id' => '[0-9]+'));


Router::connect('/dane/:alias/:object_id/:layer', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'view_layer'), array('object_id' => '[0-9]+'));
