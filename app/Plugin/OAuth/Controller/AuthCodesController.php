<?php

class AuthCodesController extends OAuthAppController
{
    public function save()
    {
        $inputData = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $this->request->data : $this->request->query;
        if (!empty($inputData)) {
            $ret = $this->AuthCode->save($inputData);
            $this->set(array(
                'return' => $ret,
                '_serialize' => 'return',
            ));
        } else {
            throw new BadRequestException();
        }
    }

    public function find($code)
    {
        $auth_code = $this->AuthCode->find('first', array(
            'conditions' => array('AuthCode.code' => $code),
            'fields' => array('AuthCode.code','AuthCode.client_id','AuthCode.redirect_uri','AuthCode.expires','AuthCode.scope', 'AuthCode.user_id'),
        ));
        $this->set(array(
            'auth_code' => $auth_code,
            '_serialize' => 'auth_code',
        ));
    }
} 