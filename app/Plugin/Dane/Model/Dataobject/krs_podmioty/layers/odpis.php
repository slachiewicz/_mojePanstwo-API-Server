<?

	$success = false;
	$result = array(
		'status' => false,
	);
	
	if( $id = $this->DB->selectValue("SELECT `id` FROM `krs_files` WHERE `krs_pozycje_id`='". addslashes($id) ."' AND `complete`='1' ORDER BY `complete_ts` DESC LIMIT 1") ) {
			
		App::uses('S3', 'Vendor');
		$S3 = new S3(S3_LOGIN, S3_SECRET, null, S3_ENDPOINT);
					
		$bucket = 'resources';
		$file = 'KRS/' . $id . '.pdf';
				
		$url = $S3->getAuthenticatedURL($bucket, $file, 60);
		
		if( $url ) {
			
			$url = str_replace('s3.amazonaws.com/' . $bucket, $bucket . '.sds.tiktalik.com', $url);
			$success = true;
			
		}		
			
	}
	
	if( $success ) {
		
		$result = array_merge($result, array(
			'status' => true,
			'url' => $url,
		));
		
	}
	
	return $result;