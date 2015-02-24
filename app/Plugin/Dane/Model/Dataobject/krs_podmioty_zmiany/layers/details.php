<?
	
	if(
		( $data = $this->S3Files->getBody('resources/KRS/pozycje/zmiany/' . $id . '.json' ) ) && 
		( $data = json_decode($data, true) )
	) {
		
		$data['tresc'] = $data['tresc_pub'];
		unset( $data['tresc_pub'] );
		return $data;
		
	} else return false;