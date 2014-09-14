<?

	$source_parts = explode(' ', $src);
	                    
	$source_params = array();
	foreach ($source_parts as $part) {
	
	    $p = strpos($part, ':');
	    if ($p !== false) {
	        $key = substr($part, 0, $p);
	        $value = substr($part, $p + 1);
	
	        if (($key != 'dataset') && ($key != 'datachannel'))
	            $source_params[$key] = $value;
	    }
	
	}
	
	if (!empty($source_params)) {
	    foreach ($source_params as $key => $value) {
	
	        switch ($key) {
	
	            case 'app':
	            {
	            	
	            	/*
	            	$_datasets = ClassRegistry::init('DB')->selectValues("SELECT base_alias FROM datasets WHERE app_id='" . addslashes($value) . "' AND `backup_catalog`='1'");
	            	
	            	$_term = array();
	            	
	            	foreach( $_datasets )
		            	$_term['term'] = $value[0];
	            	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:(' . implode(' OR ', $_datasets) . ')';
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'data_v3.in_reply_to_tweet_id' => $value,
		        		),
		        	);
		        	*/
	                break;
	            }
	            
	            case 'twitter.responsesTo':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.in_reply_to_tweet_id' => $value,
		        		),
		        	);
	                break;
	            }
	
	            case 'twitterAccounts.relatedTweets':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'twitter',
		        		),
		        	);
	                
	                $and_filters[] = array(
			        	'or' => array(
			        		array(
								'term' => array(
					        		'data_v3.twitter_account_id' => $value,
			        			),
		        			),
		        			array(
								'term' => array(
					        		'data_v3.in_reply_to_account_id' => $value,
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
							        		'_type' => 'sejm_wystapienia',
					        			),
			        				),
			        				array(
			        					'term' => array(
							        		'data_v3.ludzie.id' => $mowca_id,
					        			),
			        				),
			        			),
		        			),
		        			array(
								'and' => array(
			        				array(
			        					'term' => array(
							        		'_type' => 'prawo_projekty',
					        			),
			        				),
			        				array(
			        					'term' => array(
							        		'data_virtual.posel_id' => $value,
					        			),
			        				),
			        			),
		        			),
		        			array(
								'and' => array(
			        				array(
			        					'term' => array(
							        		'_type' => 'sejm_interpelacje',
					        			),
			        				),
			        				array(
			        					'term' => array(
							        		'data_virtual.posel_id' => $value,
					        			),
			        				),
			        			),
		        			),
						),
			        );
	                break;	
	                
	            }
	
	            case 'poslowie.wystapienia':
	            {
					
	                $mowca_id = ClassRegistry::init('DB')->selectValue("SELECT mowca_id FROM mowcy_poslowie WHERE posel_id='" . addslashes($value) . "'");


	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'sejm_wystapienia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.ludzie.id' => $mowca_id,
		        		),
		        	);
		        		                
	                break;
	
	            }
	            
	            case 'poslowie.interpelacje':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'sejm_interpelacje',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.posel_id' => $value,
		        		),
		        	);
	             	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.glosowania':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'poslowie_glosy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posel_id' => $value,
		        		),
		        	);
		        		                
	                break;	
	                
	
	            }
	            
	            case 'poslowie.prawo_projekty_za':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.poslowie_za' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_przeciw':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.poslowie_przeciw' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_wstrzymanie':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.poslowie_wstrzymali' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_nieobecnosc':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'prawo_projekty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.poslowie_nieobecni' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.komisja_etyki_uchwaly':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'sejm_komisje_uchwaly',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.oswiadczenia_majatkowe':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'poslowie_oswiadczenia_majatkowe',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	            
	            case 'poslowie.rejestr_korzysci':
	            {
					
					$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'poslowie_rejestr_korzysci',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	                	
	            }
	            
	            case 'poslowie.wspolpracownicy':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'poslowie_wspolpracownicy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posel_id' => $value,
		        		),
		        	);
	             	               	                	                
	                break;
	
	            }
	                                            
	            case 'bdl_wskazniki_grupy.bdl_wskazniki':
	            {
	
					$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'bdl_wskazniki',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.grupa_id' => $value,
		        		),
		        	);
	                break;
	
	            }
	
	            case 'bdl_wskazniki_kategorie.bdl_wskazniki_grupy':
	            {
	
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'bdl_wskazniki_grupy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.kategoria_id' => $value,
		        		),
		        	);
	                break;
	
	            }
	            
	            case 'krakow_posiedzenia.punkty':
	            {
					
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'krakow_posiedzenia_punkty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posiedzenie_id' => $value,
		        		),
		        	);
	                break;
	
	            }
	            
	            case 'crawlerSites.pages':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:crawler_pages AND _data_site_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'sejm_kluby.poslowie':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'poslowie',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.klub_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'sejm_komisje.poslowie':
	            {
	            
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'poslowie',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.komisja_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'wojewodztwa.gminy':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'gminy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.wojewodztwo_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'wojewodztwa.powiaty':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'powiaty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.wojewodztwa.id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'powiaty.gminy':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'gminy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.powiat_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'gminy.okregi_wyborcze':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'gminy_okregi_wyborcze',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'gminy.miejscowosci':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'miejscowosci',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);	                
	                break;	                	                
	            }
	            
	            case 'gminy.zamowienia_publiczne':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'zamowienia_publiczne',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.organizacje':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.biznes':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.forma_prawna_typ_id' => '1',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.ngo':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.forma_prawna_typ_id' => '2',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.spzoz':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'krs_podmioty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.forma_prawna_typ_id' => '3',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.radni':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'radni_gmin',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.aktywny' => '1',
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'gminy.byli_radni':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'radni_gmin',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.aktywny' => '0',
		        		),
		        	);	
	                break;
	            }
	            
	            case 'gminy.interpelacje':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'rady_gmin_interpelacje',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.radni_gmin.gmina_id' => $value,
		        		),
		        	);
	                break;
	            }
	            
	            case 'gminy.dotacje_ue':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'dotacje_ue',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.gmina_id' => $value,
		        		),
		        	);
	                break;
	            }
	            
	            case 'radni_gmin.wystapienia':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'rady_gmin_wystapienia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.radny_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'radni_gmin.glosy':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'krakow_glosowania_glosy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.radny_id' => $value,
		        		),
		        	);
	                break;

	            }
	            
	            case 'radni_gmin.oswiadczenia_majatkowe':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'radni_gmin_oswiadczenia_majatkowe',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.radny_id' => $value,
		        		),
		        	);	
	                break;
	                
	            }
	            
	            case 'radni_gmin.interpelacje':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'rady_gmin_interpelacje',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.radny_id' => $value,
		        		),
		        	);
	                break;
	                
	            }
	                                                                                                            
	            case 'sejm_posiedzenia.punkty':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'sejm_posiedzenia_punkty',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posiedzenie_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'sejm_posiedzenia.wystapienia':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'sejm_wystapienia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posiedzenie_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'sejm_posiedzenia.glosowania':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'sejm_glosowania',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.posiedzenie_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'dzielnice.radni':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:radni_dzielnic AND _data_dzielnica_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'gminy.radni_dzielnic':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:radni_dzielnic AND _data_gminy.id:(' . $value . ')';
	
	                
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
						        			'_type' => 'zamowienia_publiczne',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data_v3.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'and' => array(
	            					array(
	            						'term' => array(
						        			'_type' => 'radni_gmin',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data_v3.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'and' => array(
	            					array(
	            						'term' => array(
						        			'_type' => 'dotacje_ue',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data_v3.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'and' => array(
	            					array(
	            						'term' => array(
						        			'_type' => 'krs_podmioty',
						        		),
	            					),
	            					array(
	            						'term' => array(
						        			'data_v3.gmina_id' => $value,
						        		),
	            					),
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'_type' => 'krakow_posiedzenia',
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'_type' => 'krakow_posiedzenia_punkty',
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'_type' => 'rady_druki',
	            				),
	            			),
	            			array(
	            				'term' => array(
	            					'_type' => 'rady_gmin_interpelacje',
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
		        			'_type' => 'zamowienia_publiczne',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'terms' => array(
		        			'data_virtual.wykonawca_id' => $wykonawcy_ids,
		        		),
		        	);	                
	                break;
	   
	            }
	            
	            case 'krs_podmioty.umowy':
	            {
	                
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'umowy',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_v3.krs_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	            
	            case 'sa_sedziowie.orzeczenia':
	            {
	                $and_filters[] = array(
		        		'term' => array(
		        			'_type' => 'sa_orzeczenia',
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data_virtual.sedzia_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	
	        }
	
	    }
	}