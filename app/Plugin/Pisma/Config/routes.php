<?

$base_slug = "/pisma";
foreach(array('', '_v0') as $version) {

    Router::connect("$base_slug$version/contacts", array('plugin' => 'Pisma', 'controller' => 'contacts', 'action' => 'search'));

    Router::connect("$base_slug$version/templates", array('plugin' => 'Pisma', 'controller' => 'templates', 'action' => 'index'));
    Router::connect("$base_slug$version/templates/:id", array('plugin' => 'Pisma', 'controller' => 'templates', 'action' => 'view'), array('id' => '[0-9]+'));

    Router::connect("$base_slug$version/documents", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'index', '[method]' => 'GET'));
    Router::connect("$base_slug$version/documents", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'save', '[method]' => 'POST'));
    Router::connect("$base_slug$version/documents/:id", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'view'), array('id' => '[0-9]+'));
    Router::connect("$base_slug$version/documents/:id/delete", array('plugin' => 'Pisma', 'controller' => 'documents', 'action' => 'delete'), array('id' => '[0-9]+'));
}

