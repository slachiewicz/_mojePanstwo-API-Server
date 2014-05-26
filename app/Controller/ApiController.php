<?php

App::uses('AppController', 'Controller');

/*
 * Information about deployed apis
 */
class ApiController extends AppController
{
    public $uses = array('Api');

    public function index() {
        $apis = array();
        foreach($this->Api->find('all') as $api) {
            $api['Api']['swagger_url'] = Router::url(array('controller' => 'swagger', 'action' => 'resource_api_docs', 'slug' => $api['Api']['slug']), true);
            array_push($apis, $api['Api']);
        }

        $this->set(array(
            'apis' => $apis,
            '_serialize' => 'apis'
        ));
    }
}
