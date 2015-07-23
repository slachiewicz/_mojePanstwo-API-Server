<?

// COMPABILITY

Router::connect("/bdl/localDataForDimension/:dim_id", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'localDataForDimension'), array('dim_id' => '[0-9]+'));

Router::connect("/bdl/getLocalDataForDimension", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'getLocalDataForDimension'));
/*
Router::connect("/bdl/chartDataForDimmesions", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'chartDataForDimmesions'));
Router::connect("/bdl/getLocalDataForDimension", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'getLocalDataForDimension'));

Router::connect("/bdl/dataForDimmesions", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'dataForDimmesions'));

Router::connect("/bdl/dataForDimmesion/:dim_id", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'dataForDimmesion'), array('dim_id' => '[0-9]+'));
*/



$slug = 'bdl';
foreach(array('', '_v0') as $version) {
    Router::connect("/$slug$version/metrics", array('plugin' => 'Dane', 'controller' => 'datasets', 'action' => 'search', 'alias' => 'bdl_wskazniki'));
    Router::connect("/$slug$version/metrics/:action", array('plugin' => 'Dane', 'controller' => 'datasets', 'alias' => 'bdl_wskazniki'), array('action' => 'fields|switchers|sortings'));
    Router::connect("/$slug$version/metrics/:id", array('plugin' => 'Dane', 'controller' => 'datasets', 'action' => 'view', 'alias' => 'bdl_wskazniki'), array('id' => '[0-9]{2}-?[0-9]{3}'));

    // tree, data
    Router::connect("/$slug$version/:action", array('plugin' => 'BDL', 'controller' => 'BDL'));
}


Router::connect("/BDL/user_items/", array('plugin' => 'BDL', 'controller' => 'BDLTempItems', 'action' => 'index', '[method]' => 'GET'));
Router::connect("/BDL/user_items/", array('plugin' => 'BDL', 'controller' => 'BDLTempItems', 'action' => 'save', '[method]' => 'POST'));

Router::connect("/BDL/user_items/:id", array('plugin' => 'BDL', 'controller' => 'BDLTempItems', 'action' => 'view', '[method]' => 'GET'), array('id' => '[0-9]*'));
Router::connect("/BDL/user_items/:id", array('plugin' => 'BDL', 'controller' => 'BDLTempItems', 'action' => 'delete', '[method]' => 'DELETE'), array('id' => '[0-9]*', 'pass' => array('id')));

Router::connect("/BDL/user_items/list", array('plugin' => 'BDL', 'controller' => 'BDLTempItems', 'action' => 'listall', '[method]' => 'GET'));
Router::connect("/BDL/user_items/list", array('plugin' => 'BDL', 'controller' => 'BDLTempItems', 'action' => 'listall', '[method]' => 'POST'));