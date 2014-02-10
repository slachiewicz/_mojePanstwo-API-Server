<?php

class KrsAppController extends AppController
{

	public function search()
	{
	
		$search = array();
		
		$q = @$this->request->query['q'];
		if( $q )
		{
		
			$data = ClassRegistry::init('Dane.Dataobject')->find('all', array(
				'conditions' => array(
					'datachannel' => 'krs',
					'q' => $q,
				),
				'limit' => 12,
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
							'nazwa' => $object['data']['imiona'] . ' ' . $object['data']['nazwisko'],
							'field_name' => 'Wiek',
							'field_value' => pl_wiek( $object['data']['data_urodzenia'] ),
						));
					}
					elseif( $object['dataset']=='krs_podmioty' )
					{
						$search_item = array_merge($search_item, array(
							'type' => 'organization',
							'nazwa' => $object['data']['nazwa'],
					    	'field_name' => 'Rejestracja',
					    	'field_value' => substr($object['data']['data_rejestracji'], 0, 10),
						));
					}
										
					$search[] = $search_item;
					
				}
			}
		
		}
		
		$this->set('search', $search);
		$this->set('_serialize', array('search'));
	
	}

}