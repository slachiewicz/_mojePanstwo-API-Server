<?php

class KrsAppController extends AppController
{

	public function search()
	{
		$search = array();
		
		$q = @$this->request->query['q'];
		if( $q )
		{
		
			$data = ClassRegistry::init('Dane.Dataobject')->search(array('krs_podmioty', 'krs_osoby'), array(
				'conditions' => array(
					'q' => $q,
				),
				'mode' => 'title_prefix',
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
										
					$search[] = $search_item;
					
				}
			}

        } else {
            throw new BadRequestException('Query parameter is required: q');
        }

        $this->set('search', $search);
		$this->set('_serialize', array('search'));
	
	}

    public function search_api()
    {
        $q = @$this->request->query['q'];
        if(empty($q)) {
            throw new BadRequestException('Query parameter is required: q');
        }

        $data = ClassRegistry::init('Dane.Dataobject')->find('all', array(
            'conditions' => array(
                'dataset' => array('krs_podmioty', 'krs_osoby'),
                'q' => $q . '* OR ' . $q,
            ),
            'limit' => 10,
        ));

        $this->set('search', $data);
        $this->set('_serialize', array('search'));
    }
}