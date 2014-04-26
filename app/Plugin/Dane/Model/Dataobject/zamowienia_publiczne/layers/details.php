<?php

	$body = $this->S3Files->getBody('resources/UZP-details/' . $id . '.dat');
	
	if( $body && ($data = unserialize($body)) ) {
		
		return $data;
		
	} return false;