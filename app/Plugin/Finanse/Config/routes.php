<?

Router::connect('/finanse/:action', array('plugin' => 'Finanse', 'controller' => 'Finanse'));

Router::connect('/finanse/gminy/:commune_id/budzet/:type/dzialy/:id', array(
    'plugin' => 'Finanse',
    'controller' => 'Communes',
    'action' => 'section'
), array(
    'id' => '([0-9]+)',
    'commune_id' => '([0-9]+)',
    'type' => '(wydatki|dochody)',
    'pass' => array('id', 'commune_id', 'type')
));

Router::connect('/finanse/gminy/:commune_id/budzet/:type/dzialy', array(
    'plugin' => 'Finanse',
    'controller' => 'Communes',
    'action' => 'sections'
), array(
    'commune_id' => '([0-9]+)',
    'type' => '(wydatki|dochody)',
    'pass' => array('commune_id', 'type')
));