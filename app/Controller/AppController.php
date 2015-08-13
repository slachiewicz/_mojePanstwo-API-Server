<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::import('Vendor', 'functions');
App::uses('Sanitize', 'Utility');
App::uses('ApiException', 'Error');
App::uses('ValidationException', 'Error');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    // serve only json
    public $viewClass = 'Json';

    protected $isPortalCalling = false;

    public $components = array('RequestHandler',
        'Auth' => array(
            'sessionKey' => false, // don't use sessions
            'unauthorizedRedirect' => false, // don't redirect, throw ForbiddenException
        ));
    public $uses = array('Paszport.User', 'Paszport.ApiApp');

    public function  beforeFilter() {
        parent::beforeFilter();

        $origin = $this->request->header('Origin');

        if (isset($this->request->query['apiKey'])) {
            if ($this->request->query['apiKey'] == ROOT_API_KEY) {
                // MP portal
                $this->isPortalCalling = true;

                if (isset($this->request->query['user_id'])) {
                    $this->Auth->login(array(
                        'type' => 'account',
                        'id' => $this->request->query['user_id'],
                    ));
                } elseif (isset($this->request->query['temp_user_id'])) {
                    $this->Auth->login(array(
                        'type' => 'anonymous',
                        'id' => $this->request->query['temp_user_id'],
                    ));
                }

            } else {
                // non-portal app
                $apps = $this->ApiApp->find('first', array(
                    'conditions' => array('api_key' => $this->request->query['apiKey'])
                ));

                if (!$apps) {
                    throw new ForbiddenException('Unknown API key');
                }

                $app = $apps['ApiApp'];
                if ($app['type'] == 'backend') {
                    // no headers needed

                } else if ($app['type'] == 'web') {
                    $domains = preg_split('/,/', $app['domains']);
                    $found_match = false;

                    if (!$origin) {
                        throw new BadRequestException('Origin header is not specified.');
                    }

                    foreach ($domains as $domain) {
                        $is_wildcarded = preg_match('/^\*(\\.[a-zA-Z0-9][a-zA-Z0-9]*){2}$/', $domain);
                        $pattern = '/^http(s)?\:\/\/' . str_replace('*', '.*', str_replace('.', '\.', $domain)) . '$/';
                        $pattern_wc = '/^http(s)?\:\/\/' . str_replace('*', '.*', str_replace('.', '\.', substr($domain, 2))) . '$/';

                        if (preg_match($pattern, $origin)
                            or ($is_wildcarded and preg_match($pattern_wc, $origin))
                        ) {
                            header('Access-Control-Allow-Origin: ' . $origin);
                            $found_match = true;
                            break;
                        }
                    }

                    if (!$found_match) {
                        throw new ForbiddenException('Domain ' . $origin . ' is not allowed.');
                    }
                } else {
                    throw new Exception("Unsupported type");
                }
            }
        } else {
            // missing API key
            if (!preg_match('/^http(s)?\:\/\/mojepanstwo\.pl$/', $origin) && !Configure::read('debug')) {
                // now there is no excuse!
                throw new ForbiddenException('Please register your application on https://mojepanstwo.pl/paszport/apps and specify apiKey');
            }
        }

        // force all requests to be perceived as ajax (no template rendering)
        $this->request->addDetector('ajax', array(
            'callback' => function () {
                return true; // results in $this->request->is('ajax') == true
            }
        ));

        $this->Auth->allow();

        header('Access-Control-Allow-Credentials: true');
    }

    protected function flatResponseArray($objects, $model_name) {
        $ret = array();
        foreach ((array)$objects as $obj) {
            $ret[] = $obj[$model_name];
        }
        return $ret;
    }

    public function setSerialized($data, $val = null) {
        if (is_array($data)) {
            $this->set($data);
            $this->set('_serialize', array_keys($data));

        } else {
            // one value
            $this->set($data, $val);
            $this->set('_serialize', $data);
        }
    }
}
