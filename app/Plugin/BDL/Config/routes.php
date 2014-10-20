<?

// COMPABILITY

Router::connect("/bdl/chartDataForDimmesions", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'chartDataForDimmesions'));
Router::connect("/bdl/dataForDimmesions", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'dataForDimmesions'));

Router::connect("/bdl/dataForDimmesion/:dim_id", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'dataForDimmesion'), array('dim_id' => '[0-9]+'));
Router::connect("/bdl/localDataForDimension/:dim_id", array('plugin' => 'BDL', 'controller' => 'BDLLegacy', 'action' => 'localDataForDimension'), array('dim_id' => '[0-9]+'));




$slug = 'bdl';
foreach(array('', '_v0') as $version) {
    Router::connect("/$slug$version/metrics", array('plugin' => 'Dane', 'controller' => 'datasets', 'action' => 'search', 'alias' => 'bdl_wskazniki'));
    Router::connect("/$slug$version/metrics/:action", array('plugin' => 'Dane', 'controller' => 'datasets', 'alias' => 'bdl_wskazniki'), array('action' => 'fields|switchers|sortings'));
    Router::connect("/$slug$version/metrica/:id", array('plugin' => 'Dane', 'controller' => 'datasets', 'action' => 'view', 'alias' => 'bdl_wskazniki'), array('id' => '[0-9]{2}-?[0-9]{3}'));

    // tree, data
    Router::connect("/$slug$version/:action", array('plugin' => 'BDL', 'controller' => 'BDL'));
}


