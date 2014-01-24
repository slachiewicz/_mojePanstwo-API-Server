<?
Router::connect('/prawo/:controller/:action', array('plugin' => 'prawo'));
Router::connect('/prawo/acts/download/:id', array('plugin' => 'prawo', 'controller' => 'acts', 'action' => 'download'));
Router::connect('/prawo/:controller/:id', array('plugin' => 'prawo', 'action' => 'view'));


