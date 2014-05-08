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

    protected $user_id = null;
    protected $devaccess = false;
    
    public $components = array('RequestHandler', 'Auth');

    public function  beforeFilter()
    {
    	
    	$this->Auth->allow();
    	
        $this->loadModel('Paszport.UserAdditionalData');

		// (env('REMOTE_ADDR') == CLIENT_IP)
        
        if (env('HTTP_X_DEVKEY') && env('HTTP_X_DEVKEY') == MPAPI_DEV_KEY)
        {
            $this->devaccess = true;
            Configure::write('devaccess', true);
        }      
        
        if (env('HTTP_X_USER_ID'))
        {
            $this->user_id = Sanitize::paranoid(env('HTTP_X_USER_ID'));
            Configure::write('User.id', $this->user_id);
            
            $this->Auth->login(array(
            	'id' => $this->user_id,
            ));
        }
				

        header('Access-Control-Allow-Origin: ' . $this->request->header('Origin'));
        header('Access-Control-Allow-Credentials: true');

        parent::beforeFilter();

    }
}
