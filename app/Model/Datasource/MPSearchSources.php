<?

	$source_parts = explode(' ', $src);
	                    
	$source_params = array();
	foreach ($source_parts as $part) {
	
	    $p = strpos($part, ':');
	    if ($p !== false) {
	        $key = substr($part, 0, $p);
	        $value = substr($part, $p + 1);
	
	        
	    } else {
	    
		    $key = $part;
		    $value = null;
		    
	    }
	    
	    if (($key != 'dataset') && ($key != 'datachannel'))
            $source_params[$key] = $value;
	
	}
	
	if (!empty($source_params)) {
	    foreach ($source_params as $key => $value) {
	
	        switch ($key) {
	
	            case 'app':
	            {
	            	
	            	
	            	if( $value == 'prawo' ) {
		            	
		            	$force_main_weights = true;
		            	
		            	$and_filters[] = array(
			            	'terms' => array(
				            	'dataset' => array('prawo', 'prawo_hasla'),
			            	),
		            	);
		            	
	            	}
	            	
	            	/*
	            	$_datasets = ClassRegistry::init('DB')->selectValues("SELECT base_alias FROM datasets WHERE app_id='" . addslashes($value) . "' AND `backup_catalog`='1'");
	            	
	            	$_term = array();
	            	
	            	foreach( $_datasets )
		            	$_term['term'] = $value[0];
	            	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:(' . implode(' OR ', $_datasets) . ')';
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'data.in_reply_to_tweet_id' => $value,
		        		),
		        	);
		        	*/
	                break;
	            }
	            
	            case 'prawo.weszly': {
		            
		            $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo',
		        		),
		        	);
		        	
		        	$and_filters[] = array(
		        		'range' => array(
		        			'data.prawo.data_wejscia_w_zycie' => array(
		        				'lte' => 'now',
		        			),
		        		),
		        	);
		        	
		        	break;
		            
	            }
	            
	            case 'prawo.haslo': {
		            
		            $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo',
		        		),
		        	);
		        	
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.prawo.haslo_id' => $value,
		        		),
		        	);
		        	
		        	$and_filters[] = array(
		        		'or' => array(
				        	array(
				        		'and' => array(
					        		array(
						        		'term' => array(
							        		'data.prawo.typ_id' => '1',
						        		),
					        		),
					        		array(
						        		'term' => array(
						        			'data.prawo.pierwotny' => '1',
						        		),
						        	),
						        ),
				        	),
				        	array(
					        	'not' => array(
						        	'term' => array(
						        		'data.prawo.typ_id' => '1',
					        		),
					        	),
				        	),
		        		),
		        	);
		        	
		        	break;
		            
	            }
	            
	            case 'alerts': {
		            
	    			list($user_id, $group_id, $visited) = explode('|', $value);
		                      
		            $has_child_filter = array(
		        		"has_child" => array(
					        "type" => "alerts" ,
					        "filter" => array(
								"and" => array(
									array(
										"term" =>array(
							                "user_id" => $user_id,
							            ),
									),
									array(
										"term" =>array(
							                "read" => (boolean) $visited,
							            ),
									),
					            ),
							),
					    ),
		        	);	
		        	
		        	
		        	if( $group_id ) {
			        	
			        	$has_child_filter['has_child']['filter']['and'][] = array(
	        				'term' => array(
	        					'group_id' => $group_id,
	        				),
			        	);
			        	
		        	}
		        	
		        	$and_filters[] = $has_child_filter;
		        	
		        	break;
		            
	            }
	            
	            case 'twitter.responsesTo':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'data.twitter.in_reply_to_tweet_id' => $value,
		        		),
		        	);
	                break;
	            }
	
	            case 'twitterAccounts.relatedTweets':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'twitter',
		        		),
		        	);
	                
	                $and_filters[] = array(
			        	'or' => array(
			        		array(
								'term' => array(
					        		'data.twitter.twitter_account_id' => $value,
			        			),
		        			),
		        			array(
								'term' => array(
					        		'data.twitter.in_reply_to_account_id' => $value,
			        			),
		        			),
						),
			        );	
	                break;
	                
	            }
	
	            case 'poslowie.aktywnosci':
	            {
	
	                $mowca_id = ClassRegistry::init('DB')->selectValue("SELECT mowca_id FROM mowcy_poslowie WHERE posel_id='" . addslashes($value) . "'");
								       
			        
			        $and_filters[] = array(
			        	'or' => array(
			        		array(
								'and' => array(
			        				array(
			        					'term' => array(
							        		'dataset' => 'sejm_wystapienia',
					        			),
			        				),
			        				array(
			        					'term' => array(
							        		'data.ludzie.id' => $mowca_id,
					        			),
			        				),
			        			),
		        			),
		        			array(
								'and' => array(
			        				array(
			        					'term' => array(
							        		'dataset' => 'prawo_projekty',
					        			),
			        				),
			        				array(
			        					'term' => array(
							        		'data.prawo_projekty.posel_id' => $value,
					        			),
			        				),
			        			),
		        			),
		        			array(
								'and' => array(
			        				array(
			        					'term' => array(
							        		'dataset' => 'sejm_interpelacje',
					        			),
			        				),
			        				array(
			        					'term' => array(
							        		'data.sejm_interpelacje.posel_id' => $value,
					        			),
			        				),
			        			),
		        			),
						),
			        );
	                break;	
	                
	            }
				
				case 'prawo.historia': {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'or' => array(
		        			array(
		        				'term' => array(
				        			'data.prawo.orzeczenie_do_aktu' => $value,
				        		),
		        			),
		        			array(
		        				'term' => array(
				        			'data.prawo.tekst_jednolity_do_aktu' => $value,
				        		),
		        			),
		        			array(
		        				'term' => array(
				        			'data.prawo.orzeczenie_tk' => $value,
				        		),
		        			),
		        			array(
		        				'term' => array(
				        			'data.prawo.akty_zmieniajace' => $value,
				        		),
		        			),
		        			array(
		        				'term' => array(
				        			'data.prawo.akty_uchylajace' => $value,
				        		),
		        			),
		        		),		        		
		        	);
					
					break;
					
				}
				
				case 'sejm_debaty.wystapienia':
	            {
					
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_wystapienia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_wystapienia.debata_id' => $value,
		        		),
		        	);
		        		                
	                break;
	
	            }
				
	            case 'instytucje.prawo':
	            {
					
	                $podmiot_id = ClassRegistry::init('DB')->selectValue("SELECT id FROM s_podmioty WHERE instytucja_id='" . addslashes($value) . "'");

	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.prawo.autor_id' => $podmiot_id,
		        		),
		        	);
		        		                
	                break;
	
	            }
	            
	            case 'instytucje.zamowienia_udzielone':
	            {
					
	                $podmiot_ids = ClassRegistry::init('DB')->selectValues("SELECT id FROM uzp_zamawiajacy WHERE instytucja_id='" . addslashes($value) . "'");

	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'zamowienia_publiczne',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'terms' => array(
		        			'data.zamowienia_publiczne.zamawiajacy_id' => $podmiot_ids,
		        		),
		        	);
		        		                
	                break;
	
	            }
	            
	            case 'poslowie.wystapienia':
	            {
					
	                $mowca_id = ClassRegistry::init('DB')->selectValue("SELECT mowca_id FROM mowcy_poslowie WHERE posel_id='" . addslashes($value) . "'");


	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_wystapienia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.ludzie.id' => $mowca_id,
		        		),
		        	);
		        		                
	                break;
	
	            }
	            
	            case 'poslowie.interpelacje':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_interpelacje',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_interpelacje.posel_id' => $value,
		        		),
		        	);
	             	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.prawo_projekty.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.glosowania':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'poslowie_glosy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.poslowie_glosy.posel_id' => $value,
		        		),
		        	);
		        		                
	                break;	
	                
	
	            }
	            
	            case 'sejm_glosowania.glosy':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'poslowie_glosy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.poslowie_glosy.glosowanie_id' => $value,
		        		),
		        	);
		        		                
	                break;	
	                
	
	            }
	            
	            case 'sejm_posiedzenia_punkty.glosowania':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_glosowania',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_glosowania.punkt_id' => $value,
		        		),
		        	);
		        		                
	                break;	
	                
	
	            }
	            
	            case 'sejm_debaty.glosowania':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_glosowania',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_glosowania.debata_id' => $value,
		        		),
		        	);
		        		                
	                break;	
	                
	
	            }
	            	            
	            
	            case 'poslowie.prawo_projekty_za':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.prawo_projekty.poslowie_za' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_przeciw':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.prawo_projekty.poslowie_przeciw' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_wstrzymanie':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.prawo_projekty.poslowie_wstrzymali' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_nieobecnosc':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.prawo_projekty.poslowie_nieobecni' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.komisja_etyki_uchwaly':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_komisje_uchwaly',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_komisje_uchwaly.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.oswiadczenia_majatkowe':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'poslowie_oswiadczenia_majatkowe',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.poslowie_oswiadczenia_majatkowe.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.rejestr_korzysci':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'poslowie_rejestr_korzysci',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.poslowie_rejestr_korzysci.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	                	
	            }
	            
	            case 'poslowie.wspolpracownicy':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'poslowie_wspolpracownicy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.poslowie_wspolpracownicy.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	                                            
	            case 'bdl_wskazniki_grupy.bdl_wskazniki':
	            {
	
					$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'bdl_wskazniki',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.bdl_wskazniki.grupa_id' => $value,
		        		),
		        	);
	                break;
	
	            }
	
	            case 'bdl_wskazniki_kategorie.bdl_wskazniki_grupy':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'bdl_wskazniki_grupy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.bdl_wskazniki_grupy.kategoria_id' => $value,
		        		),
		        	);
	                break;
	
	            }
	            
	            case 'krakow_posiedzenia.punkty':
	            {
					
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'krakow_posiedzenia_punkty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krakow_posiedzenia_punkty.posiedzenie_id' => $value,
		        		),
		        	);
	                break;
	
	            }
	            
	            case 'crawlerSites.pages':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'crawler_pages',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.crawler_pages.site_id' => $value,
		        		),
		        	);
	                break;
	                	                
	            }
	            
	            case 'sejm_kluby.poslowie':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'poslowie',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.poslowie.klub_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'sejm_komisje.poslowie':
	            {
	            
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'poslowie',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.poslowie.komisja_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'wojewodztwa.gminy':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'gminy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.gminy.wojewodztwo_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'wojewodztwa.powiaty':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'powiaty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.powiaty.wojewodztwa.id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'powiaty.gminy':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'gminy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.gminy.powiat_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'krakow_komisje.posiedzenia':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'krakow_komisje_posiedzenia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krakow_komisje_posiedzenia.komisja_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'gminy.okregi_wyborcze':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'gminy_okregi_wyborcze',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.gminy_okregi_wyborcze.gmina_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'gminy.miejscowosci':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'miejscowosci',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.miejscowosci.gmina_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'gminy.zamowienia_publiczne':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'zamowienia_publiczne',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.zamowienia_publiczne.gmina_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.organizacje':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krs_podmioty.gmina_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.biznes':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krs_podmioty.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krs_podmioty.forma_prawna_typ_id' => '1',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.ngo':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krs_podmioty.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krs_podmioty.forma_prawna_typ_id' => '2',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.spzoz':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krs_podmioty.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krs_podmioty.forma_prawna_typ_id' => '3',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.radni':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'radni_gmin',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.radni_gmin.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.radni_gmin.aktywny' => '1',
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.byli_radni':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'radni_gmin',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.radni_gmin.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.radni_gmin.aktywny' => '0',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.interpelacje':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'rady_gmin_interpelacje',
		        		),
		        	);
	                break;
	            }
	            
	            case 'gminy.dotacje_ue':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'dotacje_ue',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.dotacje_ue.gmina_id' => $value,
		        		),
		        	);
	                break;
	            }
	            
	            case 'radni_gmin.wystapienia':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'rady_gmin_wystapienia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.rady_gmin_wystapienia.radny_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'radni_gmin.glosy':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'krakow_glosowania_glosy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.krakow_glosowania_glosy.radny_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'radni_gmin.oswiadczenia_majatkowe':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'radni_gmin_oswiadczenia_majatkowe',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.radni_gmin_oswiadczenia_majatkowe.radny_id' => $value,
		        		),
		        	);	
	                break;
	                
	            }
	            
	            case 'radni_gmin.interpelacje':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'rady_gmin_interpelacje',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.rady_gmin_interpelacje.radny_id' => $value,
		        		),
		        	);
	                break;
	                
	            }
	                                                                                                            
	            case 'sejm_posiedzenia.punkty':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_posiedzenia_punkty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_posiedzenia_punkty.posiedzenie_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'sejm_posiedzenia.wystapienia':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_wystapienia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_wystapienia.posiedzenie_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'sejm_posiedzenia.glosowania':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sejm_glosowania',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sejm_glosowania.posiedzenie_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'dzielnice.radni':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'radni_dzielnic',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.radni_dzielnic.dzielnica_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.radni_dzielnic':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'radni_dzielnic',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.radni_dzielnic.gminy.id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.szukaj':
	            {
	            
	            	$and_filters[] = array(
	            		'or' => array(
	            			array(
	            				'and' => array(
	            					array(
	            						'term' => array(
						        			'dataset' => 'zamowienia_publiczne',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data.zamowienia_publiczne.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'and' => array(
	            					array(
	            						'term' => array(
						        			'dataset' => 'radni_gmin',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data.radni_gmin.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'and' => array(
	            					array(
	            						'term' => array(
						        			'dataset' => 'dotacje_ue',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data.dotacje_ue.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'and' => array(
	            					array(
	            						'term' => array(
						        			'dataset' => 'krs_podmioty',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data.krs_podmioty.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'dataset' => 'krakow_posiedzenia',
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'dataset' => 'krakow_posiedzenia_punkty',
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'dataset' => 'rady_druki',
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'dataset' => 'rady_gmin_interpelacje',
	            				),
	            			),
	            		),
	            	);

	                break;

	            }
	            
	            case 'krs_podmioty.zamowienia':
	            {
	            	
	            	$wykonawcy_ids = ClassRegistry::init('DB')->selectValues("SELECT id FROM uzp_wykonawcy WHERE krs_id='" . addslashes($value) . "'");
	            	
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'zamowienia_publiczne',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'terms' => array(
		        			'data.zamowienia_publiczne.wykonawca_id' => $wykonawcy_ids,
		        		),
		        	);	                
	                break;
	   
	            }
	            
	            case 'krs_podmioty.historia':
	            {
	            		            	
	            	$and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'msig_zmiany',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.msig_zmiany.pozycja_id' => $value,
		        		),
		        	);	                
	                break;
	   
	            }
	            
	            case 'krs_podmioty.umowy':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'umowy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.umowy.krs_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'krs_podmioty.faktury':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'faktury',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.faktury.krs_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'sa_sedziowie.orzeczenia':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'dataset' => 'sa_orzeczenia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.sa_orzeczenia.sedzia_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	
	        }
	
	    }
	}