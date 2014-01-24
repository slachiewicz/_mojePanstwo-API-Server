<?

Router::connect('/krs/:controller', array('plugin' => 'KRS'));
Router::connect('/krs/:controller/:id', array('plugin' => 'KRS', 'action' => 'view'));


