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

    public $components = array('RequestHandler');
    protected $user_id = null;
    protected $stream_id = 1;
    protected $devaccess = false;

    public function  beforeFilter()
    {
        $this->loadModel('Paszport.UserAdditionalData');

//        if(env('REMOTE_ADDR') == CLIENT_IP) {
        
        
        if (env('HTTP_X_DEVKEY') && env('HTTP_X_DEVKEY') == MPAPI_DEV_KEY)
        {
            $this->devaccess = true;
            Configure::write('devaccess', true);
        }      
        
        if (env('HTTP_X_USER-ID'))
        {
            $this->user_id = Sanitize::paranoid(env('HTTP_X_USER-ID'));
            Configure::write('User.id', $this->user_id);
        }

        if (env('HTTP_X_USER-ID') && env('HTTP_X_STREAM-ID'))
        {
            
            $this->stream_id = Sanitize::paranoid(env('HTTP_X_STREAM-ID'));
            if (!$this->UserAdditionalData->hasPermissionToStream($this->stream_id))
            {
                Configure::write('Stream.id', 1);
                $this->stream_id = 1;
            }
            else
            {
                Configure::write('Stream.id', $this->stream_id);
            }

        }
        else
        {
            Configure::write('Stream.id', 1);
            $this->stream_id = 1;
        }


        


        parent::beforeFilter();
//        } else { // @TU BEDZIE PELNA AUTORYZACJA DLA IP INNYCH NIZ KLIENCKIE
//            throw new ForbiddenException();
//            exit();
//        }

    }
}
