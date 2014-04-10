<?php

class GeoGminyController extends AppController
{
    public $uses = array('Geo.GeoGmina');

    public function index()
    {
		
		$id = $this->request->params['id'];
		
        $gminy = $this->GeoGmina->find('all', array(
            'conditions' => array(
                'GeoGmina.pl_powiat_id' => $id,
            ),
            'fields' => array(
                'id', 'typ_id', 'nazwa', 'en_spat0', 'spat0', 'typ'
            ),
        ));

        $this->set(array(
            'gminy' => $gminy,
            '_serialize' => array('gminy'),
        ));
    }
} 