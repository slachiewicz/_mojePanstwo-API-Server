<?php

App::uses('AppController', 'Controller');

/*
 * Serves /swagger - Swagger specification of mojepanstwo API
 */
class SwaggerController extends AppController
{
    const SWAGGER_VERSION = "1.2";

    public $uses = array('Api');

    public function beforeFilter() {
        parent::beforeFilter();

        $this->root_url = Router::url('/', true);
    }

    /**
     * Serves swagger Resource-Listing for all mP APIs
     */
    public function api_docs() {
        $basePath = Router::url(array('action' => 'resource'), true);
        $basePath = substr($basePath, 0, strrpos($basePath, '/'));
        $swaggerVersion = SwaggerController::SWAGGER_VERSION;

        $apis = array();
        foreach($this->Api->find('all') as $api) {
            array_push($apis, array(
                "path" => "/" . $api['Api']['slug'], //.".{format}",
                "description" => $api['Api']['oneliner']
            ));
        }

        $_serialize = array('basePath', 'swaggerVersion', 'apis');
        $this->set(compact($_serialize, '_serialize'));
    }

    /**
     * Serves swagger Resource-Listing for specific API
     */
    public function resource_api_docs($slug) {
        $basePath = Router::url(array('action' => 'resource'), true);
        $basePath = substr($basePath, 0, strrpos($basePath, '/'));
        $swaggerVersion = SwaggerController::SWAGGER_VERSION;

        $api = $this->Api->find('first', array(
            'conditions' => array('Api.slug' => $slug)
        ));

        if (empty($api)) {
            throw new NotFoundException();
        }

        $apis = array(array(
            "path" => "/" . $api['Api']['slug'], //.".{format}",
            "description" => $api['Api']['oneliner']
        ));

        $this->setSerialized(compact('basePath', 'swaggerVersion', 'apis'));
    }

    /**
     * Servers swagger API-Declaration for specific API
     */
    public function resource($slug) {
        $filename = WWW_ROOT . "swagger-docs" . DS . $slug . ".json";
        $api = file_get_contents($filename);

        if ($api == false) {
            throw new NotFoundException();
        }
        $api = json_decode($api);
        $api->basePath = API_DOMAIN;

        $this->setSerialized('api', $api);
    }
}
