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
        $this->loadModel('Dane.Dataset');

        $queryData = array(
            'limit' => 10,
            'order' => $order,
        );
				
        $search = $this->Dataset->search('poslowie', $queryData);
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
                'uchwaly_komisji_etyki' => $this->getPoslowie('liczba_uchwal_komisji_etyki desc'),
                'przeloty' => $this->getPoslowie('liczba_przelotow desc'),
                'przejazdy' => $this->getPoslowie('liczba_przejazdow desc'),
                'kwatery_prywatne' => $this->getPoslowie('wartosc_refundacja_kwater_pln desc'),
                'uposazenia' => $this->getPoslowie('wartosc_uposazenia_pln desc'),
            ),
            'zawody' => $this->Sejmometr->zawody(5),
            'poslanki_poslowie' => $this->Sejmometr->genderStats(),
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

    public function okregi() {
        $this->set('data', $this->Sejmometr->getOkregi());
        $this->set('_serialize', 'data');
    }
}
