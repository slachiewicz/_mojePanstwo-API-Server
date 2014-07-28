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

    /**
     * Example of validation error
     * @throws ValidationException
     */
    public function validationException422() {
        throw new ValidationException(array("fld1" => array('error1', 'error2')));
    }

    /**
     * Example of custom API error
     * @throws ApiException
     */
    public function apiException418() {
        throw new ApiException("Missing-Query", array("q"), "Missing parameter: q");
    }
}
