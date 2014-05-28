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

    public function find($type = 'whatever')
    {
        $data = $this->request->query;
        if ($type !== 'first' || !isset($data['conditions']['oauth_token'])) {
            throw new BadRequestException();
        }

        $params = array();
        if (isset($data['recursive'])) {
            $params['recursive'] = $data['recursive'];
        }
        $params['conditions'] = array('oauth_token' => $data['conditions']['oauth_token']);

        $access_token = $this->AccessToken->find('first', $params);

        $this->set(array(
            'access_token' => $access_token,
            '_serialize' => 'access_token',
        ));
    }
} 