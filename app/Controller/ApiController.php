<?php

App::uses('AppController', 'Controller');

/*
 * Information about deployed apis
 */
class ApiController extends AppController
{
    public $uses = array();

    public function index() {
        // TODO db
        $apis = array(array(
            'name' => 'Kody Pocztowe',
            'version' => '1.0',
            'slug' => 'kodyPocztowe',
            'oneliner' => 'Mapuj kody pocztowe na adresy',
            'description' => 'Ustal kod pocztowy adresu lub adresy jakie obejmuje kod pocztowy',
            'swagger_url' => Router::url(array('controller' => 'swagger', 'action' => 'resource_api_docs', 'slug' => 'kodyPocztowe'), true)
        ));

        $this->set(array(
            'apis' => $apis,
            '_serialize' => 'apis'
        ));
    }
}
