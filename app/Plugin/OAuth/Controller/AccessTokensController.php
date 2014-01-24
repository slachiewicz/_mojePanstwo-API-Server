<?php

class AccessTokensController extends OAuthAppController
{
    public function save()
    {
        if ($this->data) {
            $ret = $this->AccessToken->save($this->data);
            $this->set(array(
                'return' => $ret,
                '_serialize' => array('return'),
            ));
        }
    }

    public function find($type = 'all')
    {
        $access_tokens = $this->AccessToken->find($type, $this->data);
        $this->set(array(
            'access_tokens' => $access_tokens,
            '_serialize' => array('access_tokens'),
        ));
    }
} 