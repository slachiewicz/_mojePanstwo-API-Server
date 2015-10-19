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
				'kody_miejsca' => array(
					'nested' => array(
						'path' => 'kody_pocztowe-miejsca',
					),
					'aggs' => array(
						'top' => array(
							'top_hits' => array(
								'size' => 100000,
								'_source' => true,
								'sort' => array(
									'kody_pocztowe-miejsca.nazwa.raw' => array(
										'order' => 'asc',
									),
								),
							),
						),
					),
				),
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
												'parl_obwody' => array(
													'terms' => array(
														'field' => 'miejsca-numery.parl_obwod_id',
														'size' => 3,
													),
												),
											    'viewport' => array(
													'geo_bounds' => array(
                                                        'field' => 'miejsca-numery.location',
													),
												),
												'wojewodztwa' => array(
													'terms' => array(
														'field' => 'data.miejsca.wojewodztwo_id',
														'size' => 10000,
														'exclude' => '0',
													),
													'aggs' => array(
														'label' => array(
															'terms' => array(
																'field' => 'data.miejsca.wojewodztwo.raw',
																'size' => 1,
															),
														),
														'miejsce_id' => array(
															'terms' => array(
																'field' => 'data.miejsca.wojewodztwo_miejsce_id',
																'size' => 1,
															),
														),
													),
												),
												'powiaty' => array(
													'terms' => array(
														'field' => 'data.miejsca.powiat_id',
														'size' => 10000,
														'exclude' => '0',
													),
													'aggs' => array(
														'label' => array(
															'terms' => array(
																'field' => 'data.miejsca.powiat.raw',
																'size' => 1,
															),
														),
														'miejsce_id' => array(
															'terms' => array(
																'field' => 'data.miejsca.powiat_miejsce_id',
																'size' => 1,
															),
														),
													),
												),
												'gminy' => array(
													'terms' => array(
														'field' => 'data.miejsca.gmina_id',
														'size' => 10000,
														'exclude' => '0',
													),
													'aggs' => array(
														'label' => array(
															'terms' => array(
																'field' => 'data.miejsca.gmina.raw',
																'size' => 1,
															),
														),
														'miejsce_id' => array(
															'terms' => array(
																'field' => 'data.miejsca.gmina_miejsce_id',
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
		
		$wojewodztwa = array();
		$powiaty = array();
		$gminy = array();
		$miejsca = array();
		$viewport = false;
		
		if( $aggs = @$this->Dataobject->getDataSource()->Aggs ) {
			
			if( $miejsca = @$aggs['kody_miejsca']['top']['hits']['hits'] )
				$miejsca = array_column($miejsca, '_source');			
			
			if( $aggs = $aggs['miejsca']['numery']['miejsca'] ) {
						
				$viewport = @$aggs['viewport']['bounds'] ? $aggs['viewport']['bounds'] : false;
				$wojewodztwa = @$aggs['miejsca']['wojewodztwa']['buckets'];
				$powiaty = @$aggs['miejsca']['powiaty']['buckets'];
				$gminy = @$aggs['miejsca']['gminy']['buckets'];
			
			}
			
		}
		
		return array(
			'kod' => $code_data,
			'wojewodztwa' => $wojewodztwa,
			'gminy' => $gminy,
			'powiaty' => $powiaty,
			'miejsca' => $miejsca,
			'viewport' => $viewport,
		);
		
	}
	
	public function obwody($id) {
		
		App::import('model','DB');
		$this->DB = new DB();
		return $this->DB->selectAssocs("SELECT pkw_parl_obwody_2015.id, pkw_parl_obwody_2015.nr_obwodu, pkw_parl_obwody_2015.adres_obwodu, pkw_parl_obwody_2015.przystosowany_dla_niepelnosprawnych, pkw_parl_obwody_2015.typ_obwodu, pkw_parl_obwody_2015.granice_obwodu, pl_punkty_adresowe.lat, pl_punkty_adresowe.lon FROM pkw_parl_obwody_2015 LEFT JOIN pl_punkty_adresowe ON pkw_parl_obwody_2015.punkt_id = pl_punkty_adresowe.id WHERE pkw_parl_obwody_2015.`id`='" . implode("' OR pkw_parl_obwody_2015.`id`='", $id) . "'");
		
	}
	
} 