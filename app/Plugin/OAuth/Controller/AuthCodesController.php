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

    public function find($type = 'all')
    {
        $auth_codes = $this->AuthCode->find($type, $this->request->query);
        $this->set(array(
            'auth_codes' => $auth_codes,
            '_serialize' => array('auth_codes'),
        ));
    }
} 