<?php

class MapaController extends AppController
{

    public function geodecode()
    {
				
        $data = $this->Mapa->geodecode($this->request->query['lat'], $this->request->query['lon']);

        $this->set('data', $data);
        $this->set('_serialize', 'data');

    }

}