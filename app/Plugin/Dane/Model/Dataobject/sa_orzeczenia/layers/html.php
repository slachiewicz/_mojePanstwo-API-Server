<?

	if( 
		( $html = $this->S3Files->getBody('nsa.sejmometr.pl/' . preg_replace('/{id}/', $id, '/orzeczenia_html/{id}.html')) ) && 
		( $html = str_replace(array("\n", "\r", "\t"), '', $html) ) && 
		preg_match_all('/\<div class\=\"nsa_box\"\>\<h2\>(.*?)\<\/h2\>\<div class\=\"inner\"\>(.*?)\<\/div\>\<\/div\>/i', $html, $matches) 
	) {
		
		$output = array();
		
		for( $i=0; $i<count( $matches[0] ); $i++ )
			$output[] = array(
				'title' => $matches[1][$i],
				'content' => $matches[2][$i],
			);
		
		return $output;
		
		
	} else return false;