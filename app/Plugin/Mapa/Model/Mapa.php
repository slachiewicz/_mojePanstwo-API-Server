<?php

class Mapa extends AppModel
{

    public $useTable = false;
	
	public function geocode($q) {
		
		$point = explode(' ', $q);
		
		$ES = ConnectionManager::getDataSource('MPSearch');	    
	   	   
	    $params = array();
		$params['index'] = 'mojepanstwo_v1';
		$params['type']  = 'objects';
		$params['body'] = array(
			'_source' => 'data.*',
			'size' => 1,
			'query' => array(
				'bool' => array(
					'must' => array(
						array(
							'term' => array(
								'dataset' => 'miejsca',
							),
						),
						array(
							'nested' => array(
								'path' => 'miejsca-numery',
								'query' => array(
									'match_all' => new \stdClass(),
								),
								'inner_hits' => array(
									'size' => 1,
									'sort' => array(
										array(
											'_geo_distance' => array(
												'location' => array(
													'lat' => $point[0],
													'lon' => $point[1],
												),
												'order' => 'asc',
												'distance_type' => 'plane',
											),
										),
									),
								),
							),
						),
					),
				),
			),
			'sort' => array(
				array(
					'_geo_distance' => array(
						'miejsca-numery.location' => array(
							'lat' => $point[0],
							'lon' => $point[1],
						),
						'order' => 'asc',
						'distance_type' => 'plane',
					),
				),
			),
		);
		
		$ret = $ES->API->search($params);
		
		$places = array();
		foreach( $ret['hits']['hits'] as $h ) {
						
			$locations = array();
			foreach( $h['inner_hits']['miejsca-numery']['hits']['hits'] as $l )
				$locations[] = $l['_source'];
			
			$places[] = array(
				'data' => $h['_source']['data'],
				'locations' => $locations,
			);
			
		}
		
		if( $places ) {
			
			// $places[0]['polygons']
			
		}
		
		
		return array(
			'places' => $places,
		);		
				
	}

} 