<?php

class ZamowieniaPubliczne extends AppModel
{

    public $useTable = false;

    public function getStats()
    {
		
		App::import('model','DB');
		$DB = new DB();
		
		App::Import('ConnectionManager');
		$MPSearch = ConnectionManager::getDataSource('MPSearch');
		    
        $response = $MPSearch->search(array(
		  "size" => 0, 
		  "query" => array(
		    "filtered" => array(
		      "filter" => array(
		        "and" => array(
		          "filters" => array(
		            array(
		              "term" => array(
		                "data_v3.status_id" => "2"
		              ),
		            ),
		            array(
		              "range" => array(
		                "date_v3" => array(
		                  "gte" => "now-1M"
		                ),
		              ),
		            ),
		          ),
		        ),
		      ),
		    ),
		  ),
		  "aggs" => array(
		    "suma" => array(
		      "sum" => array(
		        "field" => "data_v3.wartosc_cena"
		      ),
		    ),
		    "rodzaje" => array(
		      "terms" => array(
		        "field" => "data_v3.rodzaj_id",
		        "exclude" => "0",
		        "order" => array(
		          "suma_zamowien" => "desc"
		        ), 
		        "size" => 3
		      ),
		      "aggs" => array(
		        "suma_zamowien" => array(
		          "sum" => array(
		            "field" => "data_v3.wartosc_cena"
		          ),
		        ),
		      ),
		    ),
		    "tryby" => array(
		      "terms" => array(
		        "field" => "data_v3.tryb_id",
		        "exclude" => "0",
		        "order" => array(
		          "suma_zamowien" => "desc"
		        ), 
		        "size" => 10
		      ),
		      "aggs" => array(
		        "suma_zamowien" => array(
		          "sum" => array(
		            "field" => "data_v3.wartosc_cena"
		          ),
		        ),
		      ),
		    ),
		    "zamawiajacy" => array(
		      "terms" => array(
		        "field" => "data_v3.zamawiajacy_id",
		        "order" => array(
		          "suma_zamowien" => "desc"
		        ), 
		        "size" => 10
		      ),
		      "aggs" => array(
		        "suma_zamowien" => array(
		          "sum" => array(
		            "field" => "data_v3.wartosc_cena"
		          ),
		        ),
		      ),
		    ),
		  ),
        ));
        
        
        
        $aggregations = $response['aggregations'];
        $rodzaje = array();
        $tryby = array();
        $zamawiajacy = array();
        
        
		
		// RODZAJE
		
		if( !empty($aggregations['rodzaje']['buckets']) ) {
			
			$keys = array_column($aggregations['rodzaje']['buckets'], 'key');
			$dictionary = $DB->selectDictionary("SELECT id, nazwa FROM uzp_rodzaje WHERE `id`='" . implode("' OR `id`='", $keys) . "'");
						
			foreach( $aggregations['rodzaje']['buckets'] as $item )
				if( $item['doc_count'] && $item['suma_zamowien']['value'] )
					$rodzaje[] = array(
						'id' => $item['key'],
						'nazwa' => $dictionary[ $item['key'] ],
						'liczba_zamowien' => $item['doc_count'],
						'suma_zamowien' => $item['suma_zamowien']['value'],
					);
				
		}
		
		
		
		// TRYBY
		
		if( !empty($aggregations['tryby']['buckets']) ) {
			
			$keys = array_column($aggregations['tryby']['buckets'], 'key');
			$dictionary = $DB->selectDictionary("SELECT id, nazwa FROM uzp_tryby WHERE `id`='" . implode("' OR `id`='", $keys) . "'");
						
			foreach( $aggregations['tryby']['buckets'] as $item )
				if( $item['doc_count'] && $item['suma_zamowien']['value'] )
					$tryby[] = array(
						'id' => $item['key'],
						'nazwa' => $dictionary[ $item['key'] ],
						'liczba_zamowien' => $item['doc_count'],
						'suma_zamowien' => $item['suma_zamowien']['value'],
					);
				
		}
		
		
		
		// ZAMAWIAJĄCY
		
		if( !empty($aggregations['zamawiajacy']['buckets']) ) {
			
			$keys = array_column($aggregations['zamawiajacy']['buckets'], 'key');
			$dictionary = $DB->selectDictionary("SELECT id, nazwa FROM uzp_zamawiajacy WHERE `id`='" . implode("' OR `id`='", $keys) . "'");
						
			foreach( $aggregations['zamawiajacy']['buckets'] as $item )
				if( $item['doc_count'] && $item['suma_zamowien']['value'] )
					$zamawiajacy[] = array(
						'id' => $item['key'],
						'nazwa' => stripslashes( $dictionary[ $item['key'] ] ),
						'liczba_zamowien' => $item['doc_count'],
						'suma_zamowien' => $item['suma_zamowien']['value'],
					);
				
		}
        

		        
        return array(
        	'suma_zamowien' => $aggregations['suma']['value'],
        	'liczba_zamowien' => $response['hits']['total'],
        	'rodzaje' => $rodzaje,
        	'tryby' => $tryby,
        	'zamawiajacy' => $zamawiajacy,
        );

    }
    
    public function getNewStats($range = 'month')
    {
    	
	    $_allowed_ranges = array('week', 'month', 'year', '3years', '5years');
	    if( !in_array($range, $_allowed_ranges) )
	    	return false;
	    
	    App::import('model', 'DB');
        $this->DB = new DB();
	    
	    $data = $this->DB->selectValue("SELECT `data` FROM `uzp_stats` WHERE `id`='" . addslashes( $range ) . "'");
	    if( !empty($data) && ( $data = unserialize(preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $data)) )) {
		    
		    return $data;
		    
	    } else return false;
	    		    
    }

} 