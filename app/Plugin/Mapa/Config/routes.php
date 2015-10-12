<?
Router::connect('/Mapa/:action', array('plugin' => 'Mapa', 'controller' => 'Mapa'));
Router::connect('/Mapa/kody/:code', array(
	'plugin' => 'Mapa', 
	'controller' => 'Mapa', 
	'action' => 'getCode'
), array(
	'code' => '([0-9]{2}\-[0-9]{3})',
	'pass' => array('code'),
));