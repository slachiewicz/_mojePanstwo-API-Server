<?php

class MapaController extends AppController
{

    public function geodecode()
    {
				
        $data = $this->Mapa->geodecode($this->request->query['lat'], $this->request->query['lon']);

        $this->set('data', $data);
        $this->set('_serialize', 'data');

    }
    
    public function obwody()
    {
	    
	    $data = $this->Mapa->obwody($this->request->query['id']);

        $this->set('data', $data);
        $this->set('_serialize', 'data');
	    
    }
    
    public function getCode($code)
    {
	    
	    $data = $this->Mapa->getCode($code);

        $this->set('data', $data);
        $this->set('_serialize', 'data');
	    
    }

}