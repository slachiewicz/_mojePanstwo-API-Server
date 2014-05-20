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

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package        app.Controller
 * @link        http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $uses = array('Paszport.User');

    protected $user_id = null;
    protected $user = null;
    protected $devaccess = false;
    
    public $components = array('RequestHandler', 'Auth');

    protected function actAsUser($userArray) {
        if ($userArray && isset($userArray['User']) && !empty($userArray['User'])) {
            $this->user = $userArray['User'];
            $this->user_id = $this->user['id'];

            Configure::write('User.id', $this->user_id);

            $this->Auth->login(array(
                'id' => $this->user_id,
            ));

        } else {
            $this->user = null;
            $this->user_id = null;

            Configure::write('User.id', null);
        }
    }

    public function  beforeFilter()
    {
    	// AuthComponent::$sessionKey = false;
        parent::beforeFilter();
		
		if (env('HTTP_X_DEVKEY') && env('HTTP_X_DEVKEY') == MPAPI_DEV_KEY) {
            $this->devaccess = true;
            Configure::write('devaccess', true);
        }

        $this->loadModel('Paszport.UserAdditionalData');
             
        $remote_address_parts = explode('.', $_SERVER['REMOTE_ADDR']);
        $mp_portal_ip_parts = explode('.', MP_PORTAL_IP);
        
        
        if( $mp_portal_ip_parts[3]=='*' ) {
	        array_pop($remote_address_parts);
	        array_pop($mp_portal_ip_parts);
        }
        
        if (($remote_address_parts == $mp_portal_ip_parts) || $this->devaccess) {
            // we trust this client            
			
            if (env('HTTP_X_USER_ID')) {

                $user_id = Sanitize::paranoid(env('HTTP_X_USER_ID'));
                $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id)));
                $this->actAsUser($user);
                
            }

        } else {
            // @TU BEDZIE PELNA AUTORYZACJA (OAuth) DLA IP INNYCH NIZ KLIENCKIE
            // throw new ForbiddenException();
        }
        
        $this->Auth->allow();
                
        header('Access-Control-Allow-Origin: ' . $this->request->header('Origin'));
        header('Access-Control-Allow-Credentials: true');
    }
}
