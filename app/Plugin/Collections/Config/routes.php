<?php

Router::connect('/collections/collections/:id', array(
	'plugin' => 'Collections', 
	'controller' => 'Collections',
	'action' => 'view',
), array(
	'id' => '[0-9]+',
	'pass' => array('id'),
));