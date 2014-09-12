<?

	$object = $this->getObject($dataset, $id);
	
	
	
	$output = array(
	    'groups' => array(),
	);
	
	
	$punkt_id = $object['data']['id'];


	
	/*
    $output['groups'][] = array(
        'id' => 'debaty',
        'title' => 'Przebieg prac nad projektem ustawy',
        'objects' => array(
            array(
                'dataset' => 'legislacja_projekty_ustaw',
                'object_id' => $projekt_id,
            )
        ),
    );
    */
	
	
	
	
	// DEBATY    
    
    $q = "SELECT `subpunkt_id` FROM `stenogramy_subpunkty-punkty` WHERE `punkt_id`='$punkt_id' AND `deleted`='0' LIMIT 100";
    if( $debaty = $this->DB->selectValues($q) )
    {
    	    	
    	$group = array(
	        'id' => 'debaty',
	        'title' => 'Debaty',
	        'objects' => array(),
	    );
    	
        foreach ($debaty as $oid)
            $group['objects'][] = array(
                'dataset' => 'sejm_debaty',
                'object_id' => $oid,
            );

        $output['groups'][] = $group;

    }
    
    
    
    
    // DRUKI    
        
    if( $druki = $this->DB->selectValues("SELECT `druk_id` FROM `s_posiedzenia_punkty_druki` WHERE `punkt_id`='$punkt_id' AND `deleted`='0' LIMIT 100") )
    {
    	
    	$group = array(
	        'id' => 'druki',
	        'title' => 'Powiązane druki sejmowe',
	        'objects' => array(),
	    );
    	
        foreach ($druki as $oid)
            $group['objects'][] = array(
                'dataset' => 'sejm_druki',
                'object_id' => $oid,
            );

        $output['groups'][] = $group;

    }
		
	return $output;
	