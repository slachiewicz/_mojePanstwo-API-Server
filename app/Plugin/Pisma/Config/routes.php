<?

$base_slug = "/pisma";
foreach(array('', '_v0') as $version) {

    Router::connect("$base_slug$version/contacts", array('plugin' => 'Pisma', 'controller' => 'contacts', 'action' => 'search'));

    Router::connect("$base_slug$version/templates", array('plugin' => 'Pisma', 'controller' => 'templates', 'action' => 'index'));
    Router::connect("$base_slug$version/templates/:id", array('plugin' => 'Pisma', 'controller' => 'templates', 'action' => 'view'), array('id' => '[0-9]+'));

    Router::connect("$base_slug$version", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'index', '[method]' => 'GET'));
    Router::connect("$base_slug$version/index", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'index', '[method]' => 'GET'));
    Router::connect("$base_slug$version", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'save', '[method]' => 'POST'));
    Router::connect("$base_slug$version/index", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'save', '[method]' => 'POST'));
    Router::connect("$base_slug$version/:id", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'view', '[method]' => 'GET'), array('id' => '[A-Za-z0-9]+'));
    Router::connect("$base_slug$version/:id", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'save', '[method]' => 'POST'), array('id' => '[0-9]+'));
    Router::connect("$base_slug$version/:id/send", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'send'), array('id' => '[0-9]+'));
    Router::connect("$base_slug$version/:id/delete", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'delete'), array('id' => '[0-9]+'));
}

