<?php

class AuthCodesController extends OAuthAppController
{
    public function save()
    {
        if ($this->data) {
            $ret = $this->AuthCode->save($this->data);
            $this->set(array(
                'return' => $ret,
                '_serialize' => array('return'),
            ));
        }
    }

    public function find($type = 'all')
    {
        $auth_codes = $this->AuthCode->find($type, $this->data);
        $this->set(array(
            'auth_codes' => $auth_codes,
            '_serialize' => array('auth_codes'),
        ));
    }
} 