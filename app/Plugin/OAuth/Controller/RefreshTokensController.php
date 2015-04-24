<?php

class RefreshTokensController extends OAuthAppController
{
    public function save()
    {
        $inputData = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $this->request->data : $this->request->query;
        if (!empty($inputData)) {
            $ret = $this->RefreshToken->save($inputData);
            $this->set(array(
                'return' => $ret,
                '_serialize' => array('return'),
            ));
        }
    }

    public function find($type = 'whatever')
    {
	    	    
        $data = $this->request->data;
        
        if (!isset($data['conditions']['refresh_token'])) {
            throw new BadRequestException();
        }

        $params = array();
        if (isset($data['recursive'])) {
            $params['recursive'] = $data['recursive'];
        }
        $params['conditions'] = array('RefreshToken.refresh_token' => $data['conditions']['refresh_token']);
		
		var_export( $params ); die();
		
        $refresh_token = $this->RefreshToken->find('first', $params);

        $this->set(array(
            'refresh_token' => $refresh_token,
            '_serialize' => 'refresh_token',
        ));
    }
} 