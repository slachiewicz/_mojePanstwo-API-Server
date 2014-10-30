<?

	$ids = $this->DB->selectValues("SELECT `id` FROM `krs_files` WHERE `krs_pozycje_id`='". addslashes($id) ."' AND `complete`='1' ORDER BY `complete_ts` DESC LIMIT 3");
	
	$success = false;
	$result = array(
		'status' => false,
	);
	
	App::uses('S3', 'Vendor');
	$S3 = new S3(S3_LOGIN, S3_SECRET, null, S3_ENDPOINT);
	
	while( !empty($ids) && !$success ) {
		
		$_id = array_shift($ids);
				
		$bucket = 'resources';
		$file = 'KRS/' . $_id . '.pdf';
				
		$url = $S3->getAuthenticatedURL($bucket, $file, 60);
		
		if( $url ) {
			
			$url = str_replace('s3.amazonaws.com/' . $bucket, $bucket . '.sds.tiktalik.com', $url);
			$success = true;
			
			/*
			$this->DB->insert_ignore_assoc('m_users_krs_odpisy', array(
				'user_'
			));
			*/
			
		}		
		
	}
	
	if( $success ) {
		
		$result = array_merge($result, array(
			'status' => true,
			'url' => $url,
		));
		
	}
	
	return $result;