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

        // process resource description
        $api->basePath = Router::fullBaseUrl();
        foreach($api->apis as $a) {

            // reverse-map urls
            if (preg_match('/^\[(.+)\]/', $a->path, $matches) !== false) {
                $parts = preg_split('~\\\\.(*SKIP)(*FAIL)|/~s', $matches[1]);

                $routeElements = array();
                if (isset($parts[3])) {
                    foreach(preg_split('~\\\\.(*SKIP)(*FAIL)|,~s', $parts[3]) as $routeEl) {
                        list($k,$v) = preg_split('~\\\\.(*SKIP)(*FAIL)|:~s', $routeEl);
                        $routeElements[$k] = $v;
                    }
                }

                $url_prefix = Router::url(array_merge(array(
                    'plugin' => $parts[0],
                    'controller' => $parts[1],
                    'action' => $parts[2],
                    'skipPatterns' => true
                ), $routeElements));

                $a->path = preg_replace('/\[.+\]/', $url_prefix, $a->path);
            }
        }

        // TODO if search
        // add sortings, filters, switchers
        $api->apis[] = array(
            'path' => "/$slug/sortings",
            'operations' => array(array(
                'method' => 'GET',
                'summary' => 'Sortowania jakich można użyć podczas wyszukiwania',
                'nickname' => 'sortings',
                'parameters' => array(),
                'type' => 'array'
                ,'items' => array('$ref' => 'Sorting')
            )
            )
        );

        $api->apis[] = array(
            'path' => "/$slug",
            'operations' => array(array(
                'method' => 'GET',
                'summary' => 'Wyszukuj obiekty',
                'nickname' => 'search',
                'parameters' => array(
                    array(
                        "name" => "conditions",
                        "paramType" => "query",
                        "description" => "Filtrowanie po wartościach. Dostępne filtry można obejrzeć w /sortings i /switchers",
                        "type" => "array",
                        "items" => array('type' => 'string')
                    ),
                    array(
                        "name" => "fields",
                        "paramType" => "query",
                        "type" => "array",
                        "items" => array('type' => 'string')
                    ),
                    array(
                        "name" => "offset",
                        "paramType" => "query",
                        "type" => "integer",
                    ),
                    array(
                        "name" => "limit",
                        "paramType" => "query",
                        "type" => "integer"
                    ),
                    array(
                        "name" => "order",
                        "paramType" => "query",
                        "type" => "string",
                    )
                ),
                'type' => 'array'
            , 'items' => array('$ref' => 'PostalCode')
            )
            )
        );

        if (!isset($api->models))
            $api->models = array();

        $api->models['Sorting'] = array(
            'id' => 'Sorting',
            'description' => 'Sortowanie',
            'properties' => array(
                "field" => array(
                    'type' => 'string',
                    'desciption' => 'Klucz sortowania'
                ),
                'label' => array(
                    'type' => 'string',
                    'description' => 'Etykieta sortowania'
                ),
                'direction' => array(
                    'type' => 'string',
                    'description' => 'Kierunek sortowania',
                    'enum' => array('asc', 'desc')
                )
            )
        );

        $this->setSerialized('api', $api);
    }
}
