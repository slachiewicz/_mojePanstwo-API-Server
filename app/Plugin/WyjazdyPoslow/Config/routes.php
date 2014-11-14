<?
Router::connect('/wyjazdyposlow/countryDetails/:countrycode', array('plugin' => 'WyjazdyPoslow', 'controller' => 'Wyjazdyposlow',
    'action' => 'countryDetails'), array('countrycode' => '[a-z]{2}'));
Router::connect('/wyjazdyposlow/:action', array('plugin' => 'WyjazdyPoslow', 'controller' => 'Wyjazdyposlow'));