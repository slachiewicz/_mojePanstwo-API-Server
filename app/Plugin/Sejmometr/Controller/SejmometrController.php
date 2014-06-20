<?php

class SejmometrController extends AppController
{

    public function autorzy_projektow($params = array())
    {

        $this->set('data', $this->Sejmometr->autorzy_projektow($params));
        $this->set('_serialize', 'data');

    }

    public function zawody()
    {
        $this->set('data', $this->Sejmometr->zawody());
        $this->set('_serialize', 'data');
    }

    private function getPoslowie($order, $limit = 10)
    {
        $this->loadModel('Dane.Dataobject');

        $queryData = array(
            'limit' => 10,
            'conditions' => array(
                'dataset' => 'poslowie'),
            'order' => $order,
        );

        $search = $this->Dataobject->find('all', $queryData);
        $search['order'] = $order;

        return $search;
    }

    public function stats()
    {
        $data = array(
            'poslowie' => array(
                'liczba_wypowiedzi' => $this->getPoslowie('liczba_wypowiedzi desc'),
                'frekwencja' => $this->getPoslowie('frekwencja asc'),
                'zbuntowanie' => $this->getPoslowie('zbuntowanie desc'),
                'liczba_interpelacji' => $this->getPoslowie('liczba_interpelacji desc'),
            ),
            'zawody' => $this->Sejmometr->zawody(5)
        );

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

	public function latestData()
    {
		$data = $this->Sejmometr->latestData();

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
}
