<?php

App::uses('AppController', 'Controller');

/*
 * Serves /swagger - Swagger specification of mojepanstwo API
 */

class SwaggerController extends AppController
{
    const SWAGGER_VERSION = "1.2";

    public $uses = array('Api');

    public function beforeFilter()
    {
        parent::beforeFilter();

        $this->root_url = Router::url('/', true);
    }

    /**
     * Serves swagger Resource-Listing for all mP APIs
     */
    public function api_docs()
    {
        $basePath = Router::url(array('action' => 'resource'), true);
        $basePath = substr($basePath, 0, strrpos($basePath, '/'));
        $swaggerVersion = SwaggerController::SWAGGER_VERSION;

        $apis = array();
        foreach ($this->Api->find('all') as $api) {
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
    public function resource_api_docs($slug)
    {
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
    public function resource($slug)
    {
        $api = $this->Api->find('first', array(
            'conditions' => array('Api.slug' => $slug)
        ));

        if (empty($api)) {
            throw new NotFoundException();
        }

        // we assume that slug is connected 1:1 to Cake Plugin
        $swagger_spec = ROOT . DS . implode(DS, array('app', 'Plugin', $api['Api']['plugin'], 'Config', 'swagger.php'));
        if (!file_exists($swagger_spec))
            throw new NotFoundException();

        require_once($swagger_spec);

        // $api should be loaded
        if (!isset($api)) {
            throw new InternalErrorException('$api definition is missing in ' . $swagger_spec);
        }

        // process resource description
        $api['basePath'] = Router::fullBaseUrl();
        foreach ($api['apis'] as &$a) {
            // reverse-map urls
            if (preg_match('/^\[(.+)\]/', $a['path'], $matches) !== false) {
                $parts = preg_split('~\\\\.(*SKIP)(*FAIL)|/~s', $matches[1]);

                $routeElements = array();
                if (isset($parts[3])) {
                    foreach (preg_split('~\\\\.(*SKIP)(*FAIL)|,~s', $parts[3]) as $routeEl) {
                        list($k, $v) = preg_split('~\\\\.(*SKIP)(*FAIL)|:~s', $routeEl);
                        $routeElements[$k] = $v;
                    }
                }

                $url_prefix = Router::url(array_merge(array(
                    'plugin' => $parts[0],
                    'controller' => $parts[1],
                    'action' => $parts[2],
                    'skipPatterns' => true
                ), $routeElements));

                $a['path'] = preg_replace('/\[.+\]/', $url_prefix, $a['path']);
            }
        }

        if (isset($api['_search_endpoints'])) {
            foreach ($api['_search_endpoints'] as $e) {
                // add search endpoint
                $this->add_search_endpoint($api, $slug, $e);
            }
            unset($api['_search_endpoints']);
        }

        $this->setSerialized('api', $api);
    }

    private function add_search_endpoint(&$api, $slug, $res) {
        if (isset($res['_search_baseurl'])) {
            $base_url = $res['_search_baseurl'];
        } else {
            $base_url = "/$slug";
        }
        $base_url = $base_url . $res['_search_subpath'];

        // TODO if search
        // add search endpoint
        $api['apis'][] = array(
            'path' => $base_url,
            'operations' => array(array(
                'method' => 'GET',
                'summary' => 'Wyszukuj obiekty',
                'nickname' => 'search',
                'parameters' => array(
//                    array(
//                        "name" => "conditions",
//                        "description" => "Filtrowanie po wartościach. Dostępne filtry można obejrzeć w /sortings i /switchers",
//                        "paramType" => "query",
//                        "type" => "array",
//                        "items" => array('type' => 'string')
//                    ),
//                    array(
//                        "name" => "fields",
//                        "description" => "Lista pól, która ma być zwrócona",
//                        "paramType" => "query",
//                        "type" => "array",
//                        "items" => array('type' => 'string')
//                    ),
//                    array(
//                        "name" => "offset",
//                        "paramType" => "query",
//                        "type" => "integer",
//                    ),
//                    array(
//                        "name" => "limit",
//                        "paramType" => "query",
//                        "type" => "integer"
//                    ),
//                    array(
//                        "name" => "order",
//                        "paramType" => "query",
//                        "type" => "string",
//                    )
                ),
                'type' => 'array'
            , 'items' => array('$ref' => $res['_search_model'])
            )
            )
        );

        // TODO we need fields, filters are just a subset of it
        // add sortings, filters, switchers
        $api['apis'][] = array(
            'path' => "$base_url/sortings",
            'operations' => array(array(
                'method' => 'GET',
                'summary' => 'Sortowania jakich można użyć podczas wyszukiwania',
                'nickname' => 'sortings',
                'parameters' => array(),
                'type' => 'array'
            , 'items' => array('$ref' => 'Sorting')
            )
            )
        );
        $api['apis'][] = array(
            'path' => "$base_url/filters",
            'operations' => array(array(
                'method' => 'GET',
                'summary' => 'Filtry, jakich można użyć podczas wyszukiwania',
                'nickname' => 'filters',
                'parameters' => array(),
                'type' => 'array'
            , 'items' => array('$ref' => 'Filter')
            )
            )
        );
        $api['apis'][] = array(
            'path' => "$base_url/switchers",
            'operations' => array(array(
                'method' => 'GET',
                'summary' => 'Zagregowane filtry',
                'nickname' => 'switchers',
                'parameters' => array(),
                'type' => 'array'
            , 'items' => array('$ref' => 'Switcher')
            )
            )
        );

        if (!isset($api['models']))
            $api['models'] = array();

        $api['models']['Sorting'] = array(
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
        $api['models']['Filter'] = array(
            'id' => 'Filter',
            'description' => 'Filtry, wyświetlające się na stronie MP',
            'properties' => array(
                "field" => array(
                    'type' => 'string',
                    'desciption' => 'Pole filtru'
                ),
                'label' => array(
                    'type' => 'string',
                    'description' => 'Etykieta filtru'
                ),
                'typ_id' => array(
                    'type' => 'string',
                    'description' => 'TODO'
                ),
                'parent_field' => array(
                    'type' => 'string',
                    'description' => 'TODO',
                ),
                'desc' => array(
                    'type' => 'string',
                    'description' => 'Opis filtru',
                )
            )
        );
        $api['models']['Switcher'] = array(
            'id' => 'Switcher',
            'description' => 'Zagregowane filtry w postaci flag',
            'properties' => array(
                "name" => array(
                    'type' => 'string',
                    'desciption' => 'Nazwa'
                ),
                'label' => array(
                    'type' => 'string',
                    'description' => 'Etykieta filtru'
                ),
                'dataset_search_default' => array(
                    'type' => 'string',
                    'description' => 'Domyślna wartość',
                    'enum' => array('0', '1')
                )
            )
        );
    }
}
