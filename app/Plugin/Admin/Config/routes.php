<?php

Router::connect('/admin/model/call', array(
    'plugin' => 'Admin',
    'controller' => 'Admin',
    'action' => 'modelCall'
));


Router::connect("/admin/analyzers/id::name", array('plugin' => 'Admin', 'controller' => 'Analyzer', 'action' => 'view', '[method]' => 'GET'), array('pass' => array('name')));