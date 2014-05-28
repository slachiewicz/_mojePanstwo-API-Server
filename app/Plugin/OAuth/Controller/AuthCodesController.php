<?php

class AuthCodesController extends OAuthAppController
{
    public function save()
    {
        if (!empty($this->request->query)) {
            $ret = $this->AuthCode->save($this->request->query);
            $this->set(array(
                'return' => $ret,
                '_serialize' => array('return'),
            ));
        } else {
            throw new BadRequestException();
        }
    }

    public function find($code)
    {
        $auth_code = $this->AuthCode->find('first', array(
            'conditions' => array('AuthCode.code' => $code),
            'fields' => array('AuthCode.code','AuthCode.client_id','AuthCode.redirect_uri','AuthCode.expires','AuthCode.scope'),
        ));
        $this->set(array(
            'auth_code' => $auth_code,
            '_serialize' => 'auth_code',
        ));
    }
} 