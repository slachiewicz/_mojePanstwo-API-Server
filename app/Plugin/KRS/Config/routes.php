<?
	Router::connect('/krs/search', array('plugin' => 'KRS', 'controller' => 'KrsApp', 'action' => 'search'));
	Router::connect('/krs/:controller/:action', array('plugin' => 'KRS'));