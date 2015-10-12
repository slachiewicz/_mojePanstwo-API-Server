<?php

App::uses('GeoJsonMP', 'Geo.Model');

class Mapa extends AppModel
{

    public $useTable = false;
	
	public function geodecode($lat, $lon) {
		
		$lat = (float) $lat;
		$lon = (float) $lon;
			
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
													'lat' => $lat,
													'lon' => $lon,
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
							'lat' => $lat,
							'lon' => $lon,
						),
						'order' => 'asc',
						'distance_type' => 'plane',
					),
				),
			),
		);
		
		$ret = $ES->API->search($params);
		
		$place = false;
		
		foreach( $ret['hits']['hits'] as $h ) {
						
			$locations = array();
			foreach( $h['inner_hits']['miejsca-numery']['hits']['hits'] as $l )
				$locations[] = $l['_source'];
			
			$place = array(
				'data' => $h['_source']['data'],
				'locations' => $locations,
			);
			
			break;
			
		}
		
		/*
		if( $places ) {

            $types = array(
                'gmina_id' => 'gminy',
                'wojewodztwo_id' => 'wojewodztwa',
                'powiat_id' => 'powiaty'
            );

            $geo = new GeoJsonMP();

            foreach($places as $p => $place) {

                foreach($types as $t => $type) {

                    if(isset($place['data']['miejsca.' . $t]) &&
                    is_numeric($place['data']['miejsca.' . $t]) &&
                    $place['data']['miejsca.' . $t] > 0) {

                        $places[$p]['polygons'][$t] = $geo->getMapData('gm', array($type), array(
                            $type => array($place['data']['miejsca.' . $t])
                        ));

                    }

                }

            }

		}
		*/
		
		
		
		
		return $place;		
				
	}
	
	function getCode($code) {
		
		App::import('model','Dane.Dataobject');
		$this->Dataobject = new Dataobject();
		
		$code_data = $this->Dataobject->find('first', array(
			'conditions' => array(
				'dataset' => 'kody_pocztowe',
				'kody_pocztowe.kod' => $code,
			),
			'aggs' => array(
				'miejsca' => array(
					'scope' => 'global',
					'filter' => array(
						'term' => array(
							'dataset' => 'miejsca',
						),
					),
					'aggs' => array(
						'numery' => array(
							'nested' => array(
								'path' => 'miejsca-numery',
							),
							'aggs' => array(
								'miejsca' => array(
									'filter' => array(
										'term' => array(
											'kod' => $code,
										),
									),
									'aggs' => array(
										'viewport' => array(
											'geo_bounds' => array(
												'field' => 'miejsca-numery.location', 
											),
										),
										'miejsca' => array(
											'reverse_nested' => new \stdClass(),
											'aggs' => array(
												'gminy' => array(
													'terms' => array(
														'field' => 'data.miejsca.gmina_id',
														'size' => 10000,
													),
													'aggs' => array(
														'label' => array(
															'terms' => array(
																'field' => 'data.miejsca.gmina.raw',
																'size' => 1,
															),
														),
														'miejscowosci' => array(
															'terms' => array(
																'field' => 'data.miejsca.miejscowosc_id',
																'size' => 10000,
															),
															'aggs' => array(
																'label' => array(
																	'terms' => array(
																		'field' => 'data.miejsca.miejscowosc.raw',
																		'size' => 1,
																	),
																),
																'ulice' => array(
																	'terms' => array(
																		'field' => 'data.miejsca.ulica_id',
																		'size' => 10000,
																	),
																	'aggs' => array(
																		'label' => array(
																			'terms' => array(
																				'field' => 'data.miejsca.ulica.raw',
																				'size' => 1,
																			),
																		),
																	),
																),
															),
														),
													),
												),
											),
										),
									),
								),
							),
						),
					),
					
					
					
					/*
					'filter' => array(
						'nested' => array(
							'path' => 'miejsca-numery',
							'filter' => array(
								'term' => array(
									'miejsca-numery.kod' => $code,
								),
							),
						),
					),
					'aggs' => array(
						'gminy' => array(
							'terms' => array(
								'field' => 'gmina_id',
							),
						),
					),
					*/
				),
			),
		));
		
		$gminy = array();
		$obszar = false;
		
		if(
			( $miejsca = $this->Dataobject->getDataSource()->Aggs ) && 
			( $miejsca = @$miejsca['miejsca']['numery']['miejsca'] )
		) {
			
			$obszar = @$miejsca['viewport']['bounds'];
			$gminy = $miejsca['miejsca']['gminy']['buckets'];
			
			foreach( $gminy as &$g ) {
				
				$miejscowosci = array();
				foreach( $g['miejscowosci']['buckets'] as $m ) {
					
					$ulice = array();
					foreach( $m['ulice']['buckets'] as $u )
						$ulice[] = array(
							'id' => $u['key'],
							'nazwa' => $u['label']['buckets'][0]['key'],
						);
											
					$miejscowosci[] = array(
						'id' => $m['key'],
						'nazwa' => $m['label']['buckets'][0]['key'],
						'ulice' => $ulice,
					);
					
				}
				
				$g = array(
					'id' => $g['key'],
					'nazwa' => $g['label']['buckets'][0]['key'],
					'miejscowosci' => $miejscowosci,
				);
				
			}
			
		}
		
		return array(
			'kod' => $code_data,
			'gminy' => $gminy,
			'obszar' => $obszar,
		);
		
	}
	
} 