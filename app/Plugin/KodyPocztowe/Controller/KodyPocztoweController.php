<?php

class KodyPocztoweController extends AppController
{
    public $uses = array('Dane.Dataobject');

    public function view()
    {
        $id = @$this->request->params['id'];
        $id = (int)str_replace('-', '', $id);

        // TODO $this->Dataobject->find('first' nie działa, bo Cake::Modle dostaje coś innego niż oczekuje
        $response = $this->Dataobject->find('all', array(
            'conditions' => array(
                'dataset' => 'kody_pocztowe',
                'kod_int' => $id
            )
        ));

        if (!isset($response['dataobjects']) || empty($response['dataobjects'])) {
            throw new NotFoundException();
        }

        $object = $this->Dataobject->getObject('kody_pocztowe', (int) $response['dataobjects'][0]['object_id'], $this->request->query, true);

        $this->setSerialized('code', $object);
    }

    public function address2code() {

        $q = @$this->request->query['q'];
        if( $q )
        {
            $result = array();

            $this->loadModel('Dane.Dataobject');
            $data = $this->Dataobject->find('all', array(
                'conditions' => array(
                    'dataset' => 'kody_pocztowe_ulice',
                    'q' => $q
                ),
                'limit' => 10,
            ));

            if( isset($data['dataobjects']) && !empty($data['dataobjects']) )
            {
                foreach( $data['dataobjects'] as $object )
                {
                    $search_item = array(
                        'id' => $object['data']['id'],
                    );

                    if( $object['dataset']=='krs_osoby' )
                    {
                        $search_item = array_merge($search_item, array(
                            'type' => 'person',
                            'id' => $object['data']['id'],
                            'nazwa' => $object['data']['imiona'] . ' ' . $object['data']['nazwisko'],
                            'wiek' => pl_wiek( $object['data']['data_urodzenia'] ),
                        ));
                    }
                    elseif( $object['dataset']=='krs_podmioty' )
                    {
                        $search_item = array(
                            'type' => 'organization',
                            'id' => $object['data']['id'],
                            'nazwa' => $object['data']['nazwa'],
                            'data_rejestracji' => $object['data']['data_rejestracji'],
                            'kapital_zakladowy' => $object['data']['wartosc_kapital_zakladowy'],
                            'miejscowosc' => $object['data']['adres_miejscowosc'],
                        );
                    }

                    $result[] = $search_item;
                }
            }

        } else {
            throw new BadRequestException('Query parameter is required: q');
        }

        $this->set('result', $result);
        $this->set('_serialize', 'result');


        // pl_kody_pocztowe_pna
//        'fields' => array(
//            'Address.id',
//            'Address.nazwa',
//            'Address.ulica',
//            'Address.numery',
//            'Address.kod_id',
//            'Address.kod',
//        ),
//            'order' => array('ulica ASC', 'numery ASC')
        // TODO
    }
}