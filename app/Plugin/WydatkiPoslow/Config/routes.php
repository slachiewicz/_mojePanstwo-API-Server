<?
Router::connect('/wydatkiposlow/:action', array('plugin' => 'WydatkiPoslow', 'controller' => 'Wydatkiposlow'));

Router::connect('/wydatkiposlow/kategorie/:id', array(
	'plugin' => 'WydatkiPoslow', 
	'controller' => 'Wydatkiposlow', 
	'action' => 'category'
), array(
	'pass' => array('id'),
	'id' => '[0-9]+',
));