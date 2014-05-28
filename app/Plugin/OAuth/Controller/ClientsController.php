<?php

class ClientsController extends OAuthAppController
{
    public function find($client_id)
    {
        $clients = $this->Client->find('first', array(
            'conditions' => array('Client.client_id' => $client_id),
            'fields' => array('Client.client_id', 'Client.redirect_uri'),
            'recursive' => -1
        ));
        $this->set(array(
            'clients' => $clients,
            '_serialize' => 'clients',
        ));
    }
} 