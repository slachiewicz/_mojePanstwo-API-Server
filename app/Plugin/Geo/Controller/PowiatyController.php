<?php

class PowiatyController extends AppController
{
    public $uses = array('Geo.Powiat');

    public function index($id)
    {
        //SELECT pl_powiaty.id, pl_powiaty.typ_id, pl_powiaty.nazwa, pl_powiaty.en_spat0, AsText( centroid( pl_powiaty.spat0 ) ), pl_powiaty.en_hspat, pl_powiaty_grodzkie_gminy.gmina_id FROM pl_powiaty LEFT JOIN pl_powiaty_grodzkie_gminy ON pl_powiaty.id=pl_powiaty_grodzkie_gminy.powiat_id WHERE pl_powiaty.w_id=$w_id ORDER BY pl_powiaty.nazwa ASC
        $powiaty = $this->Powiat->find('all', array(
            'conditions' => array(
                'Powiat.w_id' => $id,
            ),
            'order' => array(
                'Powiat.nazwa' => 'asc'
            ),
        ));
        $this->set(array(
            'powiaty' => $powiaty,
            '_serialize' => array('powiaty'),
        ));
    }
} 