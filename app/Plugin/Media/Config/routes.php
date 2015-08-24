<?
Router::connect('/media/twitter/:action', array('plugin' => 'Media', 'controller' => 'Twitter'));
Router::connect('/Media/:action', array('plugin' => 'Media', 'controller' => 'PanstwoInternet'));
Router::connect('/Media/:action/:id', array('plugin' => 'Media', 'controller' => 'PanstwoInternet'));