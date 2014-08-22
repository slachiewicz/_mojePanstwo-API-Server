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
		        			'data.in_reply_to_tweet_id' => $value,
		        		),
		        	);
		        	*/
	                break;
	            }
	            
	            case 'twitter.responsesTo':
	            {
	            	$and_filters[] = array(
		        		'term' => array(
		        			'data.in_reply_to_tweet_id' => $value,
		        		),
		        	);
	                break;
	            }
	
	            case 'twitterAccounts.relatedTweets':
	            {
	                $params['fq[' . $fq_iterator . ']'] = '_data_twitter_account_id:(' . $value . ') OR _data_in_reply_to_account_id:(' . $value . ')';
	
	                
	                break;
	            }
	
	            case 'poslowie.aktywnosci':
	            {
	
	                $mowca_id = ClassRegistry::init('DB')->selectValue("SELECT mowca_id FROM mowcy_poslowie WHERE posel_id='" . addslashes($value) . "'");
	
	                $fqs = array(
	                    '(dataset:sejm_wystapienia AND _data_ludzie.id:(' . $mowca_id . '))',
	                    '(dataset:legislacja_projekty_ustaw AND _multidata_posel_id:(' . $value . '))',
	                    '(dataset:legislacja_projekty_uchwal AND _multidata_posel_id:(' . $value . '))',
	                    '(dataset:sejm_interpelacje AND _multidata_posel_id:(' . $value . '))',
	                );
	
	                $params['fq[' . $fq_iterator . ']'] = implode(' OR ', $fqs);
	
	
	                
	                break;
	            }
	
	            case 'poslowie.wystapienia':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_wystapienia AND _data_ludzie.posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.interpelacje':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_interpelacje AND _multidata_posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.projekty_ustaw':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:legislacja_projekty_ustaw AND _multidata_posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.glosowania':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie_glosy AND _data_posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:prawo_projekty AND _multidata_posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_za':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:prawo_projekty AND _multidata_poslowie_za:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_przeciw':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:prawo_projekty AND _multidata_poslowie_przeciw:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_wstrzymanie':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:prawo_projekty AND _multidata_poslowie_wstrzymali:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.prawo_projekty_nieobecnosc':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:prawo_projekty AND _multidata_poslowie_nieobecni:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.komisja_etyki_uchwaly':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_komisje_uchwaly AND _data_posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.oswiadczenia_majatkowe':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie_oswiadczenia_majatkowe AND _data_posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.rejestr_korzysci':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie_rejestr_korzysci AND _data_posel_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'poslowie.wspolpracownicy':
	            {
	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie_wspolpracownicy AND _data_posel_id:(' . $value . ')';
	
	                
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
		        			'data.grupa_id' => $value,
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
		        			'data.kategoria_id' => $value,
		        		),
		        	);
	                break;
	
	            }
	            
	            case 'rady_gmin_debaty.posiedzenie_id':
	            {
					
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:rady_gmin_debaty AND _data_posiedzenie_id:(' . $value . ')';
	
	                
	                break;
	
	            }
	            
	            case 'crawlerSites.pages':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:crawler_pages AND _data_site_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'sejm_kluby.poslowie':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie AND _data_klub_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'sejm_komisje.poslowie':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:poslowie AND _multidata_komisja_id:(' . $value . ')';
	
	                
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
		        			'data.gmina_id' => $value,
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
		        			'data.gmina_id' => $value,
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
		        			'data.gmina_id' => $value,
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
		        			'data.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.forma_prawna_typ_id' => '1',
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
		        			'data.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.forma_prawna_typ_id' => '2',
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
		        			'data.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.forma_prawna_typ_id' => '3',
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
		        			'data.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.aktywny' => '1',
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
		        			'data.gmina_id' => $value,
		        		),
		        	);
		        	$and_filters[] = array(
		        		'term' => array(
		        			'data.aktywny' => '1',
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
		        			'data.radni_gmin.gmina_id' => $value,
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
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:rady_gmin_wystapienia AND _data_radny_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'radni_gmin.oswiadczenia_majatkowe':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:radni_gmin_oswiadczenia_majatkowe AND _data_radny_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'radni_gmin.interpelacje':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:rady_gmin_interpelacje AND _data_radny_id:(' . $value . ')';
	
	                
	                break;
	            }
	                                                                                                            
	            case 'sejm_posiedzenia.punkty':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_posiedzenia_punkty AND _data_posiedzenie_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'sejm_posiedzenia.wystapienia':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_wystapienia AND _data_posiedzenie_id:(' . $value . ')';
	
	                
	                break;
	            }
	            
	            case 'sejm_posiedzenia.glosowania':
	            {
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:sejm_glosowania AND _data_posiedzenie_id:(' . $value . ')';
	
	                
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
	            
	            case 'krs_podmioty.zamowienia':
	            {
	            	
	            	$wykonawcy_ids = ClassRegistry::init('DB')->selectValues("SELECT id FROM uzp_wykonawcy WHERE krs_id='" . addslashes($value) . "'");
	            	
	            	if( !$wykonawcy_ids )
	            		$wykonawcy_ids = array('false');
	            	
	                $params['fq[' . $fq_iterator . ']'] = 'dataset:zamowienia_publiczne AND _multidata_wykonawca_id:(' . implode(' OR ', $wykonawcy_ids) . ')';
	                
	                
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
		        			'data.krs_id' => $value,
		        		),
		        	);	                
	                break;
	            }
	
	        }
	
	    }
	}