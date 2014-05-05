<?
Router::connect('/powiadomienia/phrases', array('plugin' => 'Powiadomienia', 'controller' => 'userPhrases', 'action' => 'index', '[method]' => 'GET'));
Router::connect('/powiadomienia/phrases', array('plugin' => 'Powiadomienia', 'controller' => 'userPhrases', 'action' => 'add', '[method]' => 'POST'));
Router::connect('/powiadomienia/phrases/:phrase_id', array('plugin' => 'Powiadomienia', 'controller' => 'userPhrases', 'action' => 'remove', '[method]' => 'DELETE'));


Router::connect('/powiadomienia/groups', array('plugin' => 'Powiadomienia', 'controller' => 'PowiadomieniaGroups', 'action' => 'index', '[method]' => 'GET'));
Router::connect('/powiadomienia/groups', array('plugin' => 'Powiadomienia', 'controller' => 'PowiadomieniaGroups', 'action' => 'add', '[method]' => 'POST'));
Router::connect('/powiadomienia/groups/:group_id', array('plugin' => 'Powiadomienia', 'controller' => 'PowiadomieniaGroups', 'action' => 'view', '[method]' => 'GET'));
Router::connect('/powiadomienia/groups/:group_id', array('plugin' => 'Powiadomienia', 'controller' => 'PowiadomieniaGroups', 'action' => 'remove', '[method]' => 'DELETE'));
Router::connect('/powiadomienia/groups/:group_id/:action', array('plugin' => 'Powiadomienia', 'controller' => 'PowiadomieniaGroups',));


Router::connect('/powiadomienia/objects', array('plugin' => 'Powiadomienia', 'controller' => 'alertobjects', 'action' => 'index'));
Router::connect('/powiadomienia/objects', array('plugin' => 'Powiadomienia', 'controller' => 'alertobjects', 'action' => 'flag_objects', '[method]' => 'PUT'));
Router::connect('/powiadomienia/objects/:object_id', array('plugin' => 'Powiadomienia', 'controller' => 'alertobjects', 'action' => 'flag_object', '[method]' => 'PUT'));
Router::connect('/powiadomienia/objects/:object_id/:action', array('plugin' => 'Powiadomienia', 'controller' => 'alertobjects',));

Router::connect('/powiadomienia/_objects', array('plugin' => 'Powiadomienia', 'controller' => 'newalertobjects', 'action' => 'index'));
Router::connect('/powiadomienia/_objects', array('plugin' => 'Powiadomienia', 'controller' => 'newalertobjects', 'action' => 'flag_objects', '[method]' => 'PUT'));
Router::connect('/powiadomienia/_objects/flag', array('plugin' => 'Powiadomienia', 'controller' => 'newalertobjects', 'action' => 'flagAll'));
Router::connect('/powiadomienia/_objects/:object_id', array('plugin' => 'Powiadomienia', 'controller' => 'newalertobjects', 'action' => 'flag_object', '[method]' => 'PUT'));
Router::connect('/powiadomienia/_objects/:object_id/:action', array('plugin' => 'Powiadomienia', 'controller' => 'newalertobjects',));

Router::connect('/powiadomienia/apps', array('plugin' => 'Powiadomienia', 'controller' => 'PowiadomieniaApps', 'action' => 'index', '[method]' => 'GET'));
