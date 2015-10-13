<?php

/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 16/09/15
 * Time: 15:19
 */
class MapaController extends AppController
{
    public $uses = array('MapaKrakow.Layers', 'MapaKrakow.Dzielnice', 'MapaKrakow.Edukacja');

    public function layers()
    {

        $typ = $this->request->query['type'];
        $dane = array();

        switch ($typ) {
            case 'dzielnice': {
                $data = $this->Layers->find('all', array(
                    'conditions' => array(
                        'layer_id' => 6
                    ),
                    'fields' => array(
                        'nazwa', 'numer', 'spat'
                    )
                ));
                $dane = array(
                    'type' => 'polygon',
                    'layer' => 'dzielnice',
                    'dane' => array()
                );
                foreach ($data as $row) {
                    $ret = array(
                        'id' => $row['Layers']['numer'],
                        'spat' => $row['Layers']['spat']
                    );
                    $dane['dane'][] = $ret;
                }
                break;
            }
            case 'edukacja': {

                $data = $this->Layers->find('all', array(
                    'conditions' => array(
                        'dataset_id' => 41
                    ),
                    'fields' => array(
                        'layer_id', 'nazwa', 'ulica', 'numer', 'lokal', 'latlng', 'kod_pocztowy'
                    )
                ));

                $dane = array(
                    'type' => 'markers',
                    'layer' => 'edukacja',
                    'dane' => array()
                );

                foreach ($data as $row) {

                    if (!isset($dane['dane'][$row['Layers']['layer_id']])) {
                        $dane['dane'][$row['Layers']['layer_id']] = array();
                    }

                    $ret = array(
                        'etykieta' => $row['Layers']['nazwa'],
                        'adres' => $row['Layers']['ulica'] . ' ' . $row['Layers']['numer'] . ' ' . $row['Layers']['lokal'] . ' ' . $row['Layers']['kod_pocztowy'],
                        'latlng' => $row['Layers']['latlng']
                    );
                    $dane['dane'][$row['Layers']['layer_id']][] = $ret;

                }
                break;
            }
            case 'komunikacja': {
                $data = $this->Layers->find('all', array(
                    'conditions' => array(
                        'dataset_id' => 32,
                        'layer_id' => 68
                    ),
                    'fields' => array(
                        'layer_id', 'nazwa', 'ulica', 'rodzaj', 'type', 'latlng', 'spat'
                    )
                ));

                $dane = array(
                    'type' => 'mixed',
                    'layer' => 'komunikacja',
                    'dane' => array()
                );


                foreach ($data as $row) {

                    if (!isset($dane['dane'][$row['Layers']['layer_id']])) {
                        $dane['dane'][$row['Layers']['layer_id']] = array();
                    }

                    $ret = array(
                        'etykieta' => $row['Layers']['nazwa'],
                        'adres' => $row['Layers']['ulica'] . ' ' . $row['Layers']['numer'] . ' ' . $row['Layers']['lokal'] . ' ' . $row['Layers']['kod_pocztowy'],
                        'type' => $row['Layers']['type'],
                        'latlng' => $row['Layers']['latlng'],
                        'spat' => $row['Layers']['spat']
                    );
                    $dane['dane'][$row['Layers']['layer_id']][] = $ret;

                }
                break;
            }
        }

        $this->setSerialized('response', $dane);

    }
}