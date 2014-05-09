<?php

	$body = $this->S3Files->getBody('resources/UZP-details/' . $id . '.dat');
	
	if( $body && ($data = unserialize($body)) && is_array($data) ) {
				
		unset( $data['niepelnosprawne'] );
		return $data;
		
	} return false;