<?

Router::connect('/geo/wojewodztwa', array('plugin' => 'Geo', 'controller' => 'GeoWojewodztwa'));
Router::connect('/geo/powiaty', array('plugin' => 'Geo', 'controller' => 'GeoPowiaty'));
Router::connect('/geo/gminy', array('plugin' => 'Geo', 'controller' => 'GeoGminy'));
