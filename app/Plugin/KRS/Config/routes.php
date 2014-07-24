<?
    foreach(array('osoby' => 'krs_osoby', 'podmioty' => 'krs_podmioty') as $slug => $ds) {
        Router::connect("/krs/$slug/:object_id", array('plugin' => 'Dane', 'controller' => 'dataobjects', 'action' => 'view', 'alias' => $ds), array('object_id' => '[0-9]+'));
        Router::connect("/krs/$slug", array('plugin' => 'Dane', 'controller' => 'datasets', 'alias' => $ds, 'action' => 'search'));
        Router::connect("/krs/$slug/:action", array('plugin' => 'Dane', 'controller' => 'datasets', 'alias' => $ds), array('action' => 'filters|switchers|sortings'));
    }

	Router::connect('/krs/search', array('plugin' => 'KRS', 'controller' => 'KrsApp', 'action' => 'search'));
	Router::connect('/krs/:controller/:action', array('plugin' => 'KRS'));