<?php

/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 16/09/15
 * Time: 15:19
 */
class MapaController extends AppController
{
    public $uses = array('MapaKrakow.Dzielnice');

    public function dzielnice()
    {
        $data = $this->Dzielnice->find('all', array(
            'conditions' => array(
                'layer_id' => 6,
                //'or'=>array(array('numer'=>'X'),array('numer'=>'XIII'))
            ),
            'fields' => array(
                'nazwa', 'numer', 'spat'
            )
        ));
        $dane = array(
            'type'=>'poly',
            'layer'=>'dzielnice',
            'dane'=>array()
        );
        foreach ($data as $row) {
            $ret=array(
                'id'=>$row['Dzielnice']['numer'],
                'spat'=>$row['Dzielnice']['spat']
            );
            $dane['dane'][]=$ret;

        }
        $this->setSerialized('response', $dane);
    }
}