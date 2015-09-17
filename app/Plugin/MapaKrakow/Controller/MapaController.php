<?php

/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 16/09/15
 * Time: 15:19
 */
class MapaController extends AppController
{
    public $uses=array('MapaKrakow.Dzielnice');

    public function dzielnice()
    {
       $data=$this->Dzielnice->find('all', array(
        'fields'=>array(
            'nazwa','numer','spat'
        )
    ));

        $this->setSerialized('response', $data);
    }
}