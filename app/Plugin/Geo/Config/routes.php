<?

Router::connect('/geo/wojewodztwa', array('plugin' => 'Geo', 'controller' => 'GeoWojewodztwa'));
Router::connect('/geo/powiaty/:id', array('plugin' => 'Geo', 'controller' => 'GeoPowiaty'));
Router::connect('/geo/gminy/:id', array('plugin' => 'Geo', 'controller' => 'GeoGminy'));
