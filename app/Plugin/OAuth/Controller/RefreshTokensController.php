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

    public function find($type = 'whatever')
    {
        $data = $this->request->query;
        if ($type !== 'first' || !isset($data['conditions']['refresh_token'])) {
            throw new BadRequestException();
        }

        $params = array();
        if (isset($data['recursive'])) {
            $params['recursive'] = $data['recursive'];
        }
        $params['conditions'] = array('refresh_token' => $data['conditions']['refresh_token']);

        $refresh_token = $this->RefreshToken->find('first', $params);

        $this->set(array(
            'refresh_token' => $refresh_token,
            '_serialize' => 'refresh_token',
        ));
    }
} 