<?php

class GminyController extends AppController
{
    public $uses = array('Geo.Gmina');

    public function index($id)
    {
        //SELECT id, typ_id, nazwa, en_spat0, AsText( centroid( spat0 ) ), '', id FROM pl_gminy WHERE pl_powiat_id=$w_id ORDER BY nazwa ASC
        $gminy = $this->Gmina->find('all', array(
            'conditions' => array(
                'Gmina.pl_powiat_id' => $id,
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