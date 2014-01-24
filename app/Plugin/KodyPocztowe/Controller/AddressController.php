<?php

class AddressController extends AppController
{

    public function index()
    {

        $city_id = @$this->request->params['city_id'];
        $street = @$this->request->query['street'];
        $limit = 10;

        $conditions = array(
            'miejscowosc_id' => $city_id,
        );

        if ($street) {
            $conditions['ulica LIKE'] = "%" . $street . "%";
            $limit = 30;
        }


        if ($city_id) {

            $search = $this->Address->find('all', array(
                'conditions' => $conditions,
                'limit' => $limit,
            ));
            $this->set('search', $search);
            $this->set('_serialize', array('search'));

        }

    }

} 