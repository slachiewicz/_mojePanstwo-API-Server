<?php

class RefreshTokensController extends OAuthAppController
{
    public function save()
    {
        if ($this->data) {
            $ret = $this->RefreshToken->save($this->data);
            $this->set(array(
                'return' => $ret,
                '_serialize' => array('return'),
            ));
        }
    }

    public function find($type = 'all')
    {
        $refresh_tokens = $this->RefreshToken->find($type, $this->data);
        $this->set(array(
            'refresh_tokens' => $refresh_tokens,
            '_serialize' => array('refresh_tokens'),
        ));
    }
} 