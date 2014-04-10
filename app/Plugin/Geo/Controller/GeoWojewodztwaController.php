<?php

class GeoWojewodztwaController extends AppController
{

	public $uses = array('Model', 'Geo.GeoWojewodztwo');
	
    public function index()
    {
        $wojewodztwa = $this->GeoWojewodztwo->find('all', array(
            'fields' => array(
                'id',
                'nazwa',
                'enspat',
                'spat',
                'typ',
            ),
            'order' => array(
                'nazwa' => 'ASC',
            ),
        ));

        $this->set(array(
            'wojewodztwa' => $wojewodztwa,
            '_serialize' => array('wojewodztwa'),
        ));
    }
} 