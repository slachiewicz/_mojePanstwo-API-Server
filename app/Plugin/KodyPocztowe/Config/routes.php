<?

Router::connect('/kodyPocztowe/:id', array('plugin' => 'KodyPocztowe', 'controller' => 'KodyPocztowe', 'action' => 'view'), array('id' => '[0-9]{2}-?[0-9]{3}'));

Router::connect('/kodyPocztowe/codes/:id', array('plugin' => 'KodyPocztowe', 'controller' => 'Codes', 'action' => 'view'));
Router::connect('/kodyPocztowe/cities/:city_id/addresses', array('plugin' => 'KodyPocztowe', 'controller' => 'Address', 'action' => 'index'));


