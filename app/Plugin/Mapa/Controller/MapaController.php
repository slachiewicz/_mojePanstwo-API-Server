<?php

class MapaController extends AppController
{

    public function geocode()
    {
		
		$q = @$this->request->query['q'];
		
        $data = $this->Mapa->geocode($q);

        $this->set('data', $data);
        $this->set('_serialize', 'data');

    }

}