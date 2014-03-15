<?
	
	$output = array(
	    'groups' => array(),
	);
	
	$datasets_map = array(
		'przyjete_ustawy' => 'legislacja_projekty_ustaw',
		'odrzucone_projekty_ustaw' => 'legislacja_projekty_ustaw',
		'przyjete_uchwaly' => 'legislacja_projekty_uchwal',
		'odrzucone_projekty_uchwal' => 'legislacja_projekty_uchwal',
	);
	
	
	$stats = $this->DB->selectValue("SELECT `stats_meta_` FROM s_posiedzenia WHERE id='$id'");
	if( $stats && ( $stats = json_decode($stats, 1) ) )
	{
		for( $i=0; $i<count($stats); $i++ )
		{
			if( !empty($stats[$i]['ids']) )
			{
							
				$group = array(
			        'id' => $stats[$i]['slug'],
			        'title' => $stats[$i]['label_pl'],
			        'objects' => array(),
			    );
			
		        foreach ($stats[$i]['ids'] as $oid)		            
		            if( $dataset = @$datasets_map[ $stats[$i]['slug'] ] )
			            $group['objects'][] = array(
			                'dataset' => $dataset,
			                'object_id' => $oid,
			            );
		
		        $output['groups'][] = $group;

		    
		    }
				
			
		} 
	}
	
	
	return $output;