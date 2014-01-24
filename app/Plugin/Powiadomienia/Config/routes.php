<?

Router::connect('/powiadomienia/phrases', array('plugin' => 'Powiadomienia', 'controller' => 'userPhrases', 'action' => 'index', '[method]' => 'GET'));
Router::connect('/powiadomienia/phrases', array('plugin' => 'Powiadomienia', 'controller' => 'userPhrases', 'action' => 'add', '[method]' => 'POST'));
Router::connect('/powiadomienia/phrases/:phrase_id', array('plugin' => 'Powiadomienia', 'controller' => 'userPhrases', 'action' => 'remove', '[method]' => 'DELETE'));

Router::connect('/powiadomienia/objects', array('plugin' => 'Powiadomienia', 'controller' => 'alertobjects', 'action' => 'index', '[method]' => 'POST'));
Router::connect('/powiadomienia/objects', array('plugin' => 'Powiadomienia', 'controller' => 'alertobjects', 'action' => 'flag_objects', '[method]' => 'PUT'));
Router::connect('/powiadomienia/objects/:object_id', array('plugin' => 'Powiadomienia', 'controller' => 'alertobjects', 'action' => 'flag_object', '[method]' => 'PUT'));