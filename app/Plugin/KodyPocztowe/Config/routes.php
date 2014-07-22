<?

Router::connect('/kodyPocztowe/:id', array('plugin' => 'KodyPocztowe', 'controller' => 'KodyPocztowe', 'action' => 'view'), array('id' => '[0-9]{2}-?[0-9]{3}'));
Router::connect('/kodyPocztowe/:action', array('plugin' => 'KodyPocztowe', 'controller' => 'KodyPocztowe'));


