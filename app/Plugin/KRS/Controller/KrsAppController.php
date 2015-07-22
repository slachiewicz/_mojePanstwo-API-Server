<?php

class KrsAppController extends AppController
{

    public $uses = array('Dane.KrsPodmioty');

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
						'id' => $object['id'],
					);
					
					if( $object['dataset']=='krs_osoby' )
					{
						$search_item = array_merge($search_item, array(
							'type' => 'person',
							'id' => $object['id'],
							'nazwa' => $object['data']['krs_osoby.imiona'] . ' ' . $object['data']['krs_osoby.nazwisko'],
							'wiek' => pl_wiek( $object['data']['krs_osoby.data_urodzenia'] ),
						));
					}
					elseif( $object['dataset']=='krs_podmioty' )
					{
						$search_item = array(							
					    	'type' => 'organization',
					    	'id' => $object['id'],
					    	'nazwa' => $object['data']['krs_podmioty.nazwa'],
					    	'data_rejestracji' => $object['data']['krs_podmioty.data_rejestracji'],
					    	'kapital_zakladowy' => $object['data']['krs_podmioty.wartosc_kapital_zakladowy'],
					    	'miejscowosc' => $object['data']['krs_podmioty.adres_miejscowosc'],
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

    public function odpisy() {
        $id = (int) @ $this->request->params['id'];
        $url = false;

        App::uses('S3', 'Vendor');
        $S3 = new S3(S3_LOGIN, S3_SECRET, null, S3_ENDPOINT);
        $bucket = 'resources';
        $file = 'KRS/' . $id . '.pdf';
        $url = $S3->getAuthenticatedURL($bucket, $file, 60);

        if( $url ) {
            $url = str_replace('s3.amazonaws.com/' . $bucket, $bucket . '.sds.tiktalik.com', $url);
            $success = true;
        }

        $this->set('url', $url);
        $this->set('_serialize', array('url'));
    }
}