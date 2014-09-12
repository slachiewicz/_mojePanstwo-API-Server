<?php

class Sejmometr extends AppModel
{
    
    public $useDbConfig = 'MPSearch';
    public $useTable = false;

    public function autorzy_projektow()
    {
		
		App::import('model','DB');
		$this->DB = new DB();
		
        $data = $this->DB->selectAssocs("SELECT `s_projekty_podmioty`.`podmiot_id` as 'podmiot_id', `s_podmioty`.`legislacja_typ_id` as 'typ_id', `s_podmioty`.`nazwa`, COUNT(*) as 'count' 
        	FROM `s_projekty_podmioty` 
			JOIN `s_projekty` 
			ON `s_projekty_podmioty`.`projekt_id` = `s_projekty`.`id` 
			JOIN `s_podmioty` 
			ON `s_projekty_podmioty`.`podmiot_id` = `s_podmioty`.`id`
			WHERE `s_projekty`.`akcept` = '1' 
			AND `s_projekty`.`typ_id` = 1
			GROUP BY `s_projekty_podmioty`.`podmiot_id` 
			ORDER BY COUNT(*) DESC
			LIMIT 100");
		
		return $data;

    }
    
    public function zawody($limit = null)
    {
		App::import('model','DB');
		$this->DB = new DB();

        // TODO nieznany tez?
		$count = $this->DB->selectValue("SELECT COUNT(*) FROM s_poslowie_kadencje WHERE pkw_zawod!=''");

        $sql = "SELECT COUNT( * ) AS  'count' ,  `pkw_zawod` as 'job'
			FROM  `s_poslowie_kadencje` 
			WHERE  `pkw_zawod` !=  ''
			GROUP BY  `pkw_zawod` 
			ORDER BY  `count` DESC";

        if ($limit != null) {
            $sql .= " LIMIT $limit";
        }

        $data = $this->DB->selectAssocs($sql);
		
		foreach( $data as &$d ) {
			$d['count'] = (int) $d['count'];
			$d['percent'] = round( 1000 * $d['count'] / $count ) / 10;
		}
		
		return $data;

    }
    
    public function latestData()
    {
	    
	    $chapters = array(
	    	array(
	    		'id' => 'projekty_ustaw',
	    		'conditions' => array(
	    			'typ_id' => '1',
	    		),
	    	),
	    	array(
	    		'id' => 'projekty_uchwal',
	    		'conditions' => array(
	    			'typ_id' => '2',
	    		),
	    	),
	    	array(
	    		'id' => 'sprawozdania_kontrolne',
	    		'conditions' => array(
	    			'typ_id' => '11',
	    		),
	    	),
	    	array(
	    		'id' => 'umowy',
	    		'conditions' => array(
	    			'typ_id' => '6',
	    		),
	    	),
	    	array(
	    		'id' => 'powolania_odwolania',
	    		'conditions' => array(
	    			'typ_id' => '5',
	    		),
	    	),	    	
	    	array(
	    		'id' => 'sklady_komisji',
	    		'conditions' => array(
	    			'typ_id' => '100',
	    		),
	    	),
	    	array(
	    		'id' => 'referenda',
	    		'conditions' => array(
	    			'typ_id' => '103',
	    		),
	    	),
	    	array(
	    		'id' => 'inne',
	    		'conditions' => array(
	    			'typ_id' => '12',
	    		),
	    	),
	    );
	    
	    
	    App::import('model','Dane.Dataset');
		$this->Dataset = new Dataset();
	    $output = array();
	    
	    foreach( $chapters as $chapter ) {
	    	
	    	$data = $this->Dataset->search('prawo_projekty', array(
	            'conditions' => $chapter['conditions'],
	            'limit' => 9,
	        ));
	        
	        $href = '/dane/prawo_projekty';
	        $conditions = $chapter['conditions'];
	        unset( $conditions['dataset'] );
	        
	        if( !empty($conditions) )
	        	$href .= '?' . http_build_query($conditions);
	        
		    $output[$chapter['id']] = array_merge($data, array(
		    	'href' =>  $href,
		    ));
	        
	    }
		    
	    
	    return $output;
	    
    }
    
    public function genderStats()
    {
	    
	    App::import('model','DB');
		$this->DB = new DB();
	    
	    $data = $this->DB->selectAssocs("SELECT 
	    	`s_poslowie_kadencje`.`klub_id`, 
	    	`s_poslowie_kadencje`.`pkw_plec` as 'plec', 
	    	`s_kluby`.`nazwa`, 
	    	`s_kluby`.`skrot`, 
	    	COUNT(*) AS `count`
		FROM `s_poslowie_kadencje` 
			JOIN `s_kluby` 
				ON `s_poslowie_kadencje`.`klub_id` = `s_kluby`.`id` 
		WHERE 
			`s_poslowie_kadencje`.`deleted` = '0' AND 
			`s_poslowie_kadencje`.`klub_id` != '6' AND 
			`s_poslowie_kadencje`.`klub_id` != '7' 
		GROUP BY 
			`s_poslowie_kadencje`.`klub_id`, `s_poslowie_kadencje`.`pkw_plec`
		WITH ROLLUP");
	    
	    $stats = $this->DB->selectDictionary("SELECT `pkw_plec` as 'plec', COUNT(*) as 'count' FROM `s_poslowie_kadencje` WHERE `s_poslowie_kadencje`.`deleted` = '0' GROUP BY `pkw_plec` ORDER BY `pkw_plec` DESC");
	    	        
	    $temp = array();
	    	    
	    foreach( $data as $d ) {
		    if( is_null($d['plec']) && is_null($d['klub_id']) ) {
			    
			    $temp['total'] = $d['count'];
			    
		    } elseif( !is_null($d['plec']) && is_null($d['klub_id']) ) {
		    			    	
		    } elseif( is_null($d['plec']) && !is_null($d['klub_id']) ) {
		    	
			    $temp['kluby'][ $d['klub_id'] ]['total'] = $d['count'];
                $temp['kluby'][ $d['klub_id'] ]['klub_id'] = $d['klub_id']; // jak indeksy sa numeryczne to json ich nie przechowuje
		    	
		    } else {
		    	
			    $temp['kluby'][ $d['klub_id'] ]['stats'][ $d['plec'] ] = $d['count'];
			    $temp['kluby'][ $d['klub_id'] ]['nazwa'] = $d['nazwa'];
			    $temp['kluby'][ $d['klub_id'] ]['skrot'] = $d['skrot'];
		    			    	
		    }
	    }
	    
	    $output = array(
	    	'*' => array(
	    		'total' => $temp['total'],
	    		'stats' => $stats,
	    	),
	    	'kluby' => array(),
	    );
	    
	    
	    foreach( $temp['kluby'] as $klub_id => $data )
		    $output['kluby'][] = $data;
		    
	    return $output;
	    
    }
    
} 