<?php
Router::connect('/oauth/clients/:action', array('plugin' => 'OAuth', 'controller' => 'clients'));
Router::connect('/oauth/clients/:action/:type', array('plugin' => 'OAuth', 'controller' => 'clients'));

Router::connect('/oauth/auth_codes/:action', array('plugin' => 'OAuth', 'controller' => 'auth_codes'));
Router::connect('/oauth/auth_codes/:action/:type', array('plugin' => 'OAuth', 'controller' => 'auth_codes'));

Router::connect('/oauth/access_tokens/:action', array('plugin' => 'OAuth', 'controller' => 'access_tokens'));
Router::connect('/oauth/access_tokens/:action/:type', array('plugin' => 'OAuth', 'controller' => 'access_tokens'));

Router::connect('/oauth/refresh_tokens/:action', array('plugin' => 'OAuth', 'controller' => 'refresh_tokens'));
Router::connect('/oauth/refresh_tokens/:action/:type', array('plugin' => 'OAuth', 'controller' => 'refresh_tokens'));

//Router::connect('/oauth/:action/*', array('plugin' => 'OAuth', 'controller' => 'o_auth'));

