<?php
App::uses('HttpSocket', 'Network/Http');

/**
 * Class UsersController
 *
 *
 */
class UsersController extends PaszportAppController
{
    public $uses = array('Paszport.User', 'Paszport.UserAdditionalData');
    public $components = array('Session', 'Paszport.Image2');
	public $userFields = array('User.email', 'User.created', 'User.photo', 'User.photo_small', 'User.group_id', 'User.username');
	
	
	
	
	
	
	
    /**
     * Sets permissions
     */
    public function beforeFilter()
    {
	   	    
        parent::beforeFilter();

//        $this->Auth->allow(array('login', 'add', 'gate', 'api_ping', 'forgot', 'reset', 'fblogin', 'api_gate', 'import','api_fblogin', 'twitterlogin', 'twitter','failed','client'));
//        $this->Auth->deny(array('index'));
//        $this->OAuth->allow();
//        $this->OAuth->deny('me');

        /*if ($this->params->action == 'login' && $this->Auth->loggedIn()) {
            $this->redirect(array('action' => 'index'));
        }*/


    }

    public function avatar($id = null)
    {
        $id = $this->user_id;
        if ($this->data) {
            $allowed_types = array(
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/gif' => 'gif',
                'image/png' => 'png',
            );
            if (!in_array(strtolower($this->data['User']['photo']['type']), array_keys($allowed_types))) {
                exit();
            } else {
                $filename = md5(time() . rand(0, 999)) . '.' . $allowed_types[$this->data['User']['photo']['type']];
                $filename_small = md5(time() . rand(0, 999)) . '_small.' . $allowed_types[$this->data['User']['photo']['type']];
                $path = ROOT . DS . 'app' . DS . 'Plugin' . DS . 'Paszport' . DS . 'webroot' . DS . 'uploads' . DS . 'avatars' . DS;

                file_put_contents($path . $filename, base64_decode($this->data['User']['photo']['binary']));
                file_put_contents($path . $filename_small, base64_decode($this->data['User']['photo']['binary']));
                $filename = $this->Image2->source('uploads/avatars/' . $filename)->crop(190, 190)->imagePath();
                $filename_small = $this->Image2->source('uploads/avatars/' . $filename_small)->crop(27, 27)->imagePath();
                $this->User->id = $id;
                $this->User->saveField('photo', $filename);
                $this->User->saveField('photo_small', $filename_small);
                $this->User->read();
                $this->info($id);
            }
        }
    }

    public function avatarinline($id)
    {
        $user = $this->User->find('first', array('conditions' => array('User.id' => $id), 'recursive' => -2));
        if ($user['User']['photo_small']) {
            echo json_encode(array($this->Image2->source(str_replace(FULL_BASE_URL . '/paszport/', '', $user['User']['photo_small']))->crop(27, 27)->inlineImage()));
        } else {
            echo json_encode(array($this->Image2->source(str_replace(FULL_BASE_URL . '/paszport/', '', 'default.jpg'))->crop(27, 27)->inlineImage()));
        }

        exit();
    }

    public function info()
    {
    	$user = $this->user;
			
        if( $user )
        {
            $this->UserAdditionalData->id = $user['id'];
            $data = $this->UserAdditionalData->read(null, $user['id']);
            
            if( !empty($data) )
            { 
	            $user['unread_count'] = $data['UserAdditionalData']['alerts_unread_count'];
	            $user['group'] = $data['UserAdditionalData']['group'];
            }
        }
        
        $this->set('user', $user);
        $this->set('_serialize', array('user', 'applications', 'streams'));
    }

    public function index($id = null)
    {
        $id = $this->user_id;
        $this->data = $this->User->find('first', array(
            'conditions' => array('User.id' => $id),
            'contain' => array('Language', 'UserExpand', 'Group'),
        ));
        $this->set(array(
            'user' => $this->data,
            '_serialize' => array('user', 'info'),
        ));
    }

    public function registerFromFacebook() {
        if($this->data && isset($this->data['id']) && isset($this->data['email']))
        {
            $errors = array();
            $user = $this->User->find('first', array(
                'conditions' => array("User.email" => $this->data['email']))
            );

            if(!$user)
            {
                $password = md5($this->data['id'] . $this->data['email']);
                $this->User->set(array(
                    'User' => array(
                        'email'         => $this->data['email'],
                        'username'      => $this->data['first_name'] . '' . $this->data['last_name'] . rand(0, 9999),
                        'password'      => $password,
                        'repassword'    => $password,
                        'facebook_id'   => $this->data['id']
                    )
                ));

                if($this->User->validates())
                {
                    $this->User->data['User']['password'] = $this->Auth->password($this->User->data['User']['password']);
                    $this->User->data['User']['group_id'] = 1;

                    $this->User->getDataSource()->begin();

                    $saved = $this->User->save($this->User->data, false, array(
                        'id', 'email', 'password', 'username', 'group_id', 'facebook_id'
                    ));

                    if($saved) {

                        try {
                            $this->UserAdditionalData->save(array('id' => $this->User->id));
                            $this->User->getDataSource()->commit();

                        } catch (Exception $e) {
                            $this->User->getDataSource()->rollback();
                            throw $e;
                        }

                        if($user = $this->User->find('first', array(
                            'fields' => $this->userFields,
                            'conditions' => array(
                                'User.id' =>  $this->User->id,
                            ),
                        ))) {
                            $user = $user['User'];
                            $this->Auth->login(array(
                                'type' => 'account',
                                'id' => $this->User->id,
                            ));
                        } else
                            $errors = array('Internal error');
                    } else
                        $errors = $this->User->validationErrors;
                } else
                    $errors = $this->User->validationErrors;
            } elseif($user['User']['facebook_id'] != $this->data['id']) {
                $this->User->id = $user['User']['id'];
                $this->User->set(array('User' => array(
                    'facebook_id' => $this->data['id'],
                )));
                $this->User->save(array('facebook_id' => $this->data['id']));
                $user['User']['facebook_id'] = $this->data['id'];
                $user = $user['User'];
                $this->Auth->login(array(
                    'type' => 'account',
                    'id' => $user['id'],
                ));
            } else {
                $user = $user['User'];
                $this->Auth->login(array(
                    'type' => 'account',
                    'id' => $user['id'],
                ));
            }

            $this->set(array(
                'errors' => $errors,
                'user' => $user,
                '_serialize' => array('errors', 'user'),
            ));

        } else {
            throw new BadRequestException();
        }
    }

    public function findFacebook()
    {
        if($this->data && isset($this->data['facebook_id']) && isset($this->data['email'])) {
            $user = $this->User->find('first', array(
                'conditions' => array(
                    'User.facebook_id' => $this->data['facebook_id'],
                    'User.email' => $this->data['email']
                ))
            );
            $this->set(array(
                'user' => $this->data,
                '_serialize' => array('user'),
            ));
        } else {
            throw new BadRequestException();
        }
    }

    public function register()
    {
        if(
        	isset($this->data) && 
        	is_array($this->data) && 
        	(
	        	(
	        		isset( $this->data['email'] ) && 
		        	$this->data['email']
		        ) || 
		        (
			        isset( $this->data['User']['email'] ) && 
		        	$this->data['User']['email']
		        )
		    )
        ) {
            
            $errors = array();
            $user = false;
            $this->User->set($this->data);
            
            if($this->User->validates()) {
            
            	$this->User->data['User']['password'] = $this->Auth->password( $this->User->data['User']['password'] );
	            $this->User->data['User']['group_id'] = 1;			
				
	            $this->User->getDataSource()->begin();
	            
	            $saved = $this->User->save($this->User->data, false, array(
		            'id', 'email', 'password', 'username', 'group_id', 'language_id'
	            ));
	            	            
	            if ($saved) {
	                
	                try {
	                    $this->UserAdditionalData->save(array('id' => $this->User->id));
	                    $this->User->getDataSource()->commit();
	
	                } catch (Exception $e) {
	                    $this->User->getDataSource()->rollback();
	                    throw $e;
	                }
					
					if( $user = $this->User->find('first', array(
						'fields' => $this->userFields,
						'conditions' => array(
							'User.id' =>  $this->User->id,
						),
					)) ) {
						
						$user = $user['User'];
						$this->Auth->login(array(
			                'type' => 'account',
			                'id' => $this->User->id,
		                ));
						
						
					} else $errors = array('Internal error');
	            } else $errors = $this->User->validationErrors; // email verification
            } else $errors = $this->User->validationErrors; // basic validation

            $this->set(array(
                'errors' => $errors,
                'user' => $user,
                '_serialize' => array('errors', 'user'),
            ));
            
        } else {
            throw new BadRequestException();
        }
    }

    public function login()
    {
        if ($this->data && isset($this->data['password']) && isset($this->data['email'])) {
            $data = $this->data;
            $user = $this->User->find('first', array('conditions' => array('User.email' => $data['email'])));

            if ($user) {
                if($user['User']['password'] == $this->Auth->password($data['password'])) {
                    unset($user['User']['password']);
                    unset($user['User']['repassword']);
                    $this->set(array(
                        'user' => $user,
                        '_serialize' => 'user',
                    ));
                }
                else {
                    throw new ForbiddenException();
                }

            } else {
                throw new NotFoundException();
            }

        } else {
            throw new BadRequestException();
        }
    }

    public function setUserName() {
        $this->Auth->deny();
        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException();

        $response = false;
        $id = (int) $this->Auth->user('id');
        if($this->request->isPost() && isset($this->data['value'])) {
            $this->User->id = $id;
            $this->User->set(array('User' => array(
                'username' => $this->data['value'],
            )));
            if($this->User->validates(array('fieldList' => array('username')))) {
                $this->User->save(array('username' => $this->data['value']));
                $response = true;
            }
        }

        $this->set(array(
            'response' => $response,
            '_serialize' => 'response',
        ));
    }

    public function setEmail() {
        $this->Auth->deny();
        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException();

        $response = false;
        $id = (int) $this->Auth->user('id');
        if($this->request->isPost() && isset($this->data['value'])) {
            $this->User->id = $id;
            $this->User->set(array('User' => array(
                'email' => $this->data['value'],
            )));
            if($this->User->validates(array('fieldList' => array('email')))) {
                $this->User->save(array('email' => $this->data['value']));
                $response = true;
            }
        }

        $this->set(array(
            'response' => $response,
            '_serialize' => 'response',
        ));
    }

    public function setUserPassword() {
        $this->Auth->deny();
        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException();

        $response = false;
        $id = (int) $this->Auth->user('id');
        if($this->request->isPost() && isset($this->data['old_password']) && isset($this->data['new_password'])) {
            $user = $this->User->find('first', array('conditions' => array('User.id' => $id)));
            if(($user['User']['password'] == $this->Auth->password($this->data['old_password'])) || true) {
                $this->User->id = $id;
                $this->User->set(array('User' => array(
                    'password' => $this->data['new_password'],
                )));
                if($this->User->validates(array('fieldList' => array('password')))) {
                    $this->User->save(array('password' => $this->Auth->password($this->data['new_password'])));
                    $response = true;
                }
            }
        }

        $this->set(array(
            'response' => $response,
            '_serialize' => 'response',
        ));
    }

    public function deletePaszport()
    {
        $this->Auth->deny();
        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException();

        $response = false;
        $id = (int) $this->Auth->user('id');

        if($this->request->isPost() && isset($this->data['password'])) {
            $exists = $this->User->find('count', array('conditions' => array('User.id' => $id, 'User.password' => $this->Auth->password($this->data['password']))));
            if($exists > 0) {
                $this->User->delete($id);
                $response = true;
            }
        }

        $this->set(array(
            'response' => $response,
            '_serialize' => 'response',
        ));
    }

    /**
     * forces password for just registered FB users
     */
    public function setpassword()
    {
        $id = $this->authorizeUser();
        $user = $this->User->find('first', array('recursive' => -2, 'conditions' => array('User.id' => $id)));
        if($user['User']['password_set']) {
            $this->set('_serialize', '');
        }

        $this->set(array(
            'status' => 400,
            'errors' => 'bad request',
            '_serialize' => array('errors', 'status'),
        ));

        CakeLog::debug(print_r($this->data, true));
        if ($this->request->isPost() && isset($this->data['User']['password'])) {
            $this->User->id = $id;

            // verify password
            $this->User->set(array('User' => array(
                'password' => $this->data['User']['password'],
            )));

            if ($this->User->validates(array('fieldList' => array('password')))) {
                if ($this->User->save(array('password' => $this->Auth->password($this->data['User']['password']), 'password_set' => 1))) {
                    $this->set('_serialize', '');

                } else {
                    $this->set(array(
                        'status' => 422,
                        'errors' => $this->User->validationErrors,
                        '_serialize' => array('errors', 'status'),
                    ));
                }

            } else {
                $this->set(array(
                    'status' => 422,
                    'errors' => $this->User->validationErrors,
                    '_serialize' => array('errors', 'status'),
                ));
            }
        }
    }

    /**
     * Switcher to attach profiles
     * @param string|null $profile
     */
    public function attachprofile($profile = null)
    {
        if (is_null($profile)) {
            exit();
        }

        switch ($profile) {
            case "facebook":
                $this->__attachFacebook();
                break;
            case "gplus":
                //@TODO add gplus
                break;

        }
        exit();
    }

    /**
     * Switcher to attach profiles
     * @param string|null $profile
     */
    public function deattachprofile($profile = null)
    {
        if (is_null($profile)) {
            exit();
        }

        switch ($profile) {
            case "facebook":
                $this->__attachFacebook(true);
                break;
            case "gplus":
                //@TODO add gplus
                break;

        }
        exit();
    }


    /**
     * @param bool $deattach - on true deletes the relation
     * @return bool
     */
    public function __attachFacebook($deattach = false, $redirect = null)
    {
        if ($deattach) {
            $this->User->id = $this->Auth->user('id');
            if ($this->User->saveField('facebook_id', null)) {
                $this->_log(array('msg' => 'LC_PASZPORT_LOG_FB_DEATTACHED', 'ip' => $this->request->clientIp(), 'user_agent' => env('HTTP_USER_AGENT')));
            }
            return true;
        }
        # check if user has already given permissions to the app
        $user_data = $this->Connect->FB->api('/me/?fields=id,first_name,last_name,email,gender,picture.type(square).width(200),birthday,locale');
        if ($user_data['id']) { # merge, save, inform
            $this->User->id = $this->Auth->user('id');
            $to_save = array(
                'User' => array(
                    'facebook_id' => $user_data['id'],
                ),
            );
            $user_photo = $this->User->find('first', array('conditions' => array('User.id' => $this->Auth->user('id')), 'recursive' => -2, 'fields' => array('User.photo')));
            if (!$user_photo['User']['photo']) {
                $this->User->Behaviors->load('Upload.Upload', array('photo' => array('path' => '{ROOT}webroot{DS}uploads{DS}{model}{DS}{field}{DS}')));
                $to_save['User']['photo'] = preg_replace('/https/', 'http', $user_data['picture']['data']['url']);
            }
            if ($this->User->save($to_save)) {
                $this->_log(array('msg' => 'LC_PASZPORT_LOG_FB_ACCOUNT_MERGED', 'ip' => $this->request->clientIp(), 'user_agent' => env('HTTP_USER_AGENT')));
            } else {
                exit();
            }
        } else {
            exit();
        }
    }


    /**
     *
     * Converts FB male|female to our int representatives
     * @param string $gender
     * @return int
     */
    public function __fbGender($gender)
    {
        switch ($gender) {
            case "male":
                return 1;
                break;
            case "female":
                return 2;
                break;
            default:
                return 0;
                break;
        }
    }

    /**
     * Converts rfc language definitions country_LOCALE into our models
     *
     * @param string $rfc_lang
     * @return int
     */
    public function __fbLanguage($rfc_lang)
    {
        $lang = $this->User->Language->find('first', array('recursive' => -2, 'conditions' => array('rfc_code' => $rfc_lang)));
        if ($lang) {
            return $lang['Language']['id'];
        } else {
            return 2; # english
        }
    }

    /**
     *
     * Generates token, sends mail, validates token and redirect to password changing method
     * @return bool
     */
    public function forgot()
    {
        App::uses('CakeEmail', 'Network/Email');
        if ($this->request->isPost()) { # if post then someone sent form, we should find user with given e-mail
            $user = $this->User->find('first', array('conditions' => array('User.email' => $this->data['User']['email']), 'recursive' => -2));
            if ($user) { # if user exists send email
                $Email = new CakeEmail();
                $Email->config('smtp');

                $Email->to($user['User']['email']);
                $Email->subject(__('LC_PASZPORT_MAIL_RESET_PASS_SUBJECT', true));
                $e = new Encryption(MCRYPT_BlOWFISH, MCRYPT_MODE_CBC);
                $data = json_encode(array('email' => $user['User']['email'], 'expires' => strtotime('+24 hours')));
                $hash = base64_encode($e->encrypt($data, Configure::read('Security.salt')));
                $Email->viewVars(array('hash' => urlencode($hash)));
                if ($Email->send()) {
                    $this->_log(array('msg' => 'LC_PASZPORT_LOG_MAIL_RESET_PASS_SENT', 'ip' => $this->request->clientIp(), 'user_agent' => env('HTTP_USER_AGENT')));
                    $this->User->id = $user['User']['id'];
                    $this->User->saveField('reset_hash', urlencode($hash));
                }
            } else { # if not display error
                exit();
            }
        } else { # if the request was not post
            if (isset($this->request->query['token'])) { # but it has $hash sent, we are going to change user's password
                $hash = $this->request->query['token'];
                $hash = str_replace(' ', '+', urldecode($hash));
                $e = new Encryption(MCRYPT_BlOWFISH, MCRYPT_MODE_CBC);
                $token_data = json_decode($e->decrypt(base64_decode($hash), Configure::read('Security.salt')), true);
                $user_email = $token_data['email'];
                $expires = $token_data['expires'];

                if (time() > $expires) {
                    return false;
                } else {
                    $user = $this->User->find('first', array('recursive' => -2, 'conditions' => array('User.email' => $user_email, 'User.reset_hash' => urlencode($this->request->query['token']))));
                    if (!$user) {
                        exit();
                    }
                }

            }
        }
    }

    /**
     *
     * Sets password given by user
     * clears the reset_hash field in DB
     */
    public function reset()
    {
        $this->set(array(
            'status' => 400,
            'errors' => 'bad request',
            '_serialize' => array('errors', 'status'),
        ));

        if ($this->data['User']) {
            // verify password
            $to_save = array('User' => array(
                'password' => $this->data['User']['password'],
                'repassword' => $this->data['User']['repassword'],
                'reset_hash' => '',
                'id' => $this->data['User']['id']
            ));
            $this->actAsUser($this->data);

            $this->User->set($to_save);
            if ($this->User->validates(array('fieldList' => array('repassword', 'password', 'reset_hash')))) {
                $to_save['User']['password'] = $to_save['User']['repassword'] = $this->Auth->password($this->data['User']['password']);

                if ($this->User->save($to_save)) {
                    $this->set('_serialize', '');
                    $this->_log(array('msg' => 'LC_PASZPORT_LOG_PASSWORD_RESET_SUCCESS', 'ip' => $this->request->clientIp(), 'user_agent' => env('HTTP_USER_AGENT')));

                } else {
                    $this->set(array(
                        'status' => 422,
                        'errors' => $this->User->validationErrors,
                        '_serialize' => array('errors', 'status'),
                    ));
                }
            } else {
                $this->set(array(
                    'status' => 422,
                    'errors' => $this->User->validationErrors,
                    '_serialize' => array('errors', 'status'),
                ));
            }
        }
    }


    /**
     * Logout
     */
    public function logout()
    {
        if ($this->request->isAjax()) {
            $this->requestAction($this->Auth->logout());
            echo json_encode(array('error' => '', 'status' => 200, 'msg' => __('LC_PASZPORT_LOGOUT', true)));
            die();
        }
        $this->_log(array('msg' => 'LC_PASZPORT_LOG_LOGOUT', 'ip' => $this->request->clientIp(), 'user_agent' => env('HTTP_USER_AGENT')));
    }

    /**
     * Register
     */
    public function add()
    {
        $to_save = $this->data;
        if ($to_save && array_key_exists('User', $to_save)) {
            $this->User->set($to_save);

            if (!$this->User->validates()) {
                $this->set(array(
                    'errors' => $this->User->validationErrors,
                    '_serialize' => array('errors'),
                ));

                return;
            }

            $to_save['User']['password'] = $this->Auth->password($this->data['User']['password']);
            $to_save['User']['repassword'] = $this->Auth->password($this->data['User']['repassword']);

            $this->User->getDataSource()->begin();
            $saved = $this->User->save($to_save);
            if ($saved) {
                try {
                    $this->UserAdditionalData->save(array('id' => $this->User->id));
                    $this->User->getDataSource()->commit();

                } catch (Exception $e) {
                    $this->User->getDataSource()->rollback();
                    throw $e;
                }

                $this->actAsUser($saved);
                $this->info();

            } else {
                $this->set(array(
                    'errors' => $this->User->validationErrors,
                    '_serialize' => array('errors'),
                ));
            }

        } else {
            // invalid request
            throw new BadRequestException('Missing User data');
        }
    }

    private function authorizeUser() {
        if (isset($this->user_id)) {
            return $this->user_id;
        }

        if (!empty($this->passedArgs) && intval($this->passedArgs[0]) > 0) {
            return $this->passedArgs[0];
        }

        throw new UnauthorizedException();
    }

    /**
     * Saves changes to one field in model
     * return json response about success or failure
     */
    public function field()
    {
        $id = $this->authorizeUser();

        $forbiddenFields = array('id', 'password', 'pass', 'newpass', 'confirmnewpass');
        CakeLog::debug(print_r($this->data, true));

        if ($this->data && isset($this->data['User'])) {
            $to_save = array();
            $err_msg = null;
            $this->User->id = $id;

            if (isset($this->data['User']['pass']) && isset($this->data['User']['newpass']) && isset($this->data['User']['confirmnewpass'])) {
                // authorize password again
                $user = $this->User->find('first', array('conditions' => array('User.id' => $id)));

                if ($user['User']['password'] != $this->Auth->password($this->data['User']['pass'])) {
                    $err_msg = __('LC_PASZPORT_OLD_PASSWORD_INVALID', true);

                } else {
                    // verify password
                    $this->User->set(array('User' => array(
                        'password' => $this->data['User']['newpass'],
                        'repassword' => $this->data['User']['confirmnewpass'],
                    )));

                    if ($this->User->validates(array('fieldList' => array('repassword', 'password')))) {
                        $to_save['User']['password'] = $this->Auth->password($this->data['User']['newpass']);

                    } else {
                        $this->set(array(
                            'status' => 422,
                            'errors' => $this->User->validationErrors,
                            '_serialize' => array('errors', 'status'),
                        ));
                        return;
                    }
                }
            } else {
                foreach ($this->data['User'] as $field => $value) {
                    if (!in_array($field, $forbiddenFields)) {
                        $to_save['User'][$field] = $value;
                    }
                }
            }

            if (!empty($to_save)) {
                if ($this->User->save($to_save)) {
                    $this->set(array(
                        'status' => 200,
                        'alerts' => array(
                            'success' => array(__('LC_PASZPORT_SAVED', true)),
                        ),
                        '_serialize' => array('status', 'alerts'),
                    ));

                } else {
                    $this->set(array(
                        'status' => 422,
                        'errors' => $this->User->validationErrors,
                        '_serialize' => array('errors'),
                    ));
                }
            } else {
                if (is_null($err_msg)) {
                    $this->set(array(
                        'status' => 200,
                        'alerts' => array(
                            'success' => array(__('LC_PASZPORT_SAVED', true)),
                        ),
                        '_serialize' => array('status', 'alerts'),
                    ));

                } else {
                    $this->set(array(
                        'status' => 500,
                        'alerts' => array(
                            'error' => array($err_msg),
                        ),
                        '_serialize' => array('status', 'alerts'),
                    ));
                }
            }

        } else {
            throw new BadRequestException(__('LC_PASZPORT_NO_DATA', true));
        }
    }

    /**
     * deletes currently logged acount
     * verifies users based on password he has retyped
     */
    public function delete()
    {
        $id = $this->authorizeUser();

        if (!isset($this->data['User']['password'])) {
            throw new BadRequestException();
        }

        $exists = $this->User->find('count', array('conditions' => array('User.id' => $id, 'User.password' => $this->Auth->password($this->data['User']['password']))));
        if ($exists > 0) {
            $this->User->delete($id);
            $this->set(array(
                '_serialize' => ''
            ));

        } else {
            $this->set(array(
                'errors' => array('errors' => array('Password is invalid')),
                '_serialize' => 'errors'
            ));
        }
    }

    public function bar($logged = false)
    {
        $this->autoRender = false;
        $this->layout = 'plain';
        if ($this->data) {
            if ($logged) {
                echo json_encode(array('topbar' => file_get_contents(ROOT . DS . 'app' . DS . 'Plugin' . DS . 'Paszport' . DS . 'webroot' . DS . 'bar_logged.ctp')));
                exit();

            } else {
                if ($this->data['Topbar']['md5'] == md5(file_get_contents(ROOT . DS . 'app' . DS . 'Plugin' . DS . 'Paszport' . DS . 'webroot' . DS . 'bar.ctp'))) {
                    echo json_encode(array('status' => 'nochange'));
                    die();
                } else {
                    echo json_encode(array('topbar' => file_get_contents(ROOT . DS . 'app' . DS . 'Plugin' . DS . 'Paszport' . DS . 'webroot' . DS . 'bar.ctp')));
                    exit();
                }
            }
        }
        exit();
    }

}
