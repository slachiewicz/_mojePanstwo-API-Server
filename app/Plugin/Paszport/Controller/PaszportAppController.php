<?php

class PaszportAppController extends AppController
{
    public $components = array(
        /*'Auth' => array(
            'loginAction' => array(
                'controller' => 'users',
                'action' => 'login',
                'plugin' => 'paszport'
            ),
            'authenticate' => array(
                'Form' => array(
                    'fields' => array('username' => 'email', 'password' => 'password'),
                    'passwordHasher' => array(
                        'className' => 'Simple',
                        'hashType' => 'sha256'
                    ),
                    'userModel' => 'Paszport.User',
                    'contain' => array('Language', 'Group', 'UserExpand'),
                )
            )
        ),*/
        'RequestHandler',
//        'Facebook.Connect',
    );

    public function beforeFilter()
    {
        parent::beforeFilter();

        if( $this->request->query['apiKey'] !== ROOT_API_KEY ) {
            // deny access to Paszport from untrusted clients
            throw new ForbiddenException();
        }

        $this->Auth->allow();
        if ($this->params->query && !$this->request->isPost()) {
            $this->data = $this->params->query;
        }

    }

    public function find($type = 'first')
    {
        if (!$this->data) {
            $this->data = array();
        }
        $data = $this->{$this->modelClass}->find($type, $this->data);
        $this->set(array(
            strtolower($this->modelClass) => $data,
            '_serialize' => array(strtolower($this->modelClass)),
        ));

    }

    public function as_list()
    {
        if (!$this->data) {
            $this->data = array();
        }
        $data = $this->{$this->modelClass}->find('list', $this->data);
        $this->set(array(
            strtolower($this->modelClass) => $data,
            '_serialize' => array(strtolower($this->modelClass)),
        ));
    }

    public function field($id)
    {
        if ($this->data) {
            $this->{$this->modelClass}->id = $id;
            list($field, $value) = $this->data;
            $this->{$this->modelClass}->saveField($field, $value);
        }
    }

    public function deletefield($id, $field)
    {
        $this->{$this->modelClass}->id = $id;
        $this->{$this->modelClass}->saveField($field, null);
        echo json_encode(array('status' => 'ok'));
        exit();
    }

    public function delete($id)
    {
        $this->{$this->modelClass}->id = $id;
        $this->{$this->modelClass}->delete();
        echo json_encode(array('status' => 'ok'));
    }

    /**
     * Log sink
     *
     * @param array $log array('msg','ip','user_id')
     * @return bool
     */
    protected function _log($log = array())
    {
        $this->loadModel('Paszport.Log');
        $log['user_id'] = $this->user_id;

        if ($log['user_agent'] == null) {
            $log['user_agent'] = '';
        }
        $to_save = array();
        $to_save['Log'] = $log;

        if (is_array($to_save['Log']['msg'])) {
            $to_save['Log']['msg'] = json_encode($to_save['Log']['msg']);
        }
        if ($this->Log->save($to_save)) {
            return true;
        } else {
            return false;
        }
    }

} 
