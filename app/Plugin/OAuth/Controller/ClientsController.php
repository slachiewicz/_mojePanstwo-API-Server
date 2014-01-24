<?php

class ClientsController extends OAuthAppController
{
    public function find($type = 'all')
    {
        $clients = $this->Client->find($type, $this->data);
        $this->set(array(
            'clients' => $clients,
            '_serialize' => array('clients'),
        ));
    }
} 