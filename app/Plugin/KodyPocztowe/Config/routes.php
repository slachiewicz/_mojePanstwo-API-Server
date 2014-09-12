<?
foreach(array('_v0', '') as $version) {
    Router::connect("/kody_pocztowe$version", array('plugin' => 'Dane', 'controller' => 'datasets', 'action' => 'search', 'alias' => 'kody_pocztowe'));
    Router::connect("/kody_pocztowe$version/codes/:id", array('plugin' => 'KodyPocztowe', 'controller' => 'KodyPocztowe', 'action' => 'view'));
    Router::connect("/kody_pocztowe$version/:action", array('plugin' => 'Dane', 'controller' => 'datasets', 'alias' => 'kody_pocztowe'), array('action' => 'fields|switchers|sortings'));
    Router::connect("/kody_pocztowe$version/:action", array('plugin' => 'KodyPocztowe', 'controller' => 'KodyPocztowe'));
}

