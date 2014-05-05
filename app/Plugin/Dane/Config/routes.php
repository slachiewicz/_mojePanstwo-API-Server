<?

Router::connect('/dane/dataset/:alias/:object_id/layers/:layer', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'layer'));

Router::connect('/powiadomienia/alertsQueries/:id', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'alertsQueries'));

Router::connect('/dane/dataset/:alias', array('plugin' => 'Dane', 'controller' => 'datasets', 'action' => 'info'));
Router::connect('/dane/dataset/:alias/:action', array('plugin' => 'Dane', 'controller' => 'datasets'));

Router::connect('/dane/datachannel/:alias', array('plugin' => 'Dane', 'controller' => 'datachannels', 'action' => 'info'));
Router::connect('/dane/datachannel/:alias/:action', array('plugin' => 'Dane', 'controller' => 'datachannels'));

Router::connect('/dane/:alias/:object_id', array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'view'));
