<?
Router::connect('/PanstwoInternet/:action', array('plugin' => 'PanstwoInternet', 'controller' => 'PanstwoInternet'));
Router::connect('/PanstwoInternet/:action/:id', array('plugin' => 'PanstwoInternet', 'controller' => 'PanstwoInternet'));