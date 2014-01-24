<?

Router::connect('/kodyPocztowe/codes/:id', array('plugin' => 'KodyPocztowe', 'controller' => 'Codes', 'action' => 'view'));
Router::connect('/kodyPocztowe/cities/:city_id/addresses', array('plugin' => 'KodyPocztowe', 'controller' => 'Address', 'action' => 'index'));


