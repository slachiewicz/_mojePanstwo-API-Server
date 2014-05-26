<?
Router::connect('/Media/:action', array('plugin' => 'Media', 'controller' => 'PanstwoInternet'));
Router::connect('/Media/:action/:id', array('plugin' => 'Media', 'controller' => 'PanstwoInternet'));