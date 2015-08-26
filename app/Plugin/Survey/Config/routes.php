<?php

Router::connect('/survey/save', array(
    'plugin' => 'Survey',
    'controller' => 'Survey',
    'action' => 'save',
    '[method]' => 'POST'
));
