<?php

App::uses('AppController', 'Controller');

/*
 * Serves /swagger - Swagger specification of mojepanstwo API
 */
class SwaggerController extends AppController
{
    const SWAGGER_VERSION = "1.2";

    public $uses = array();

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

        // TODO foreach() {
        array_push($apis, array(
            "path" => "/kodyPocztowe.{format}",
            "description" => "Mapowanie kodów pocztowych na adresy"
        ));
        // }

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

        $apis = array(array(
            // TODO
            "path" => "/kodyPocztowe.{format}",
            "description" => "Mapowanie kodów pocztowych na adresy"
        ));

        $_serialize = array('basePath', 'swaggerVersion', 'apis');
        $this->set(compact($_serialize, '_serialize'));
    }

    /**
     * Servers swagger API-Declaration for specific API
     */
    public function resource($slug) {
        $filename = WWW_ROOT . "swagger" . DS . $slug . ".json";
        $api = file_get_contents($filename);

        if ($api == false) {
            throw new NotFoundException();
        }

        $this->set('api', $api);
    }
}
