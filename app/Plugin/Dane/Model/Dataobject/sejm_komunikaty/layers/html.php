<?
	
	$data = S3::getObject('resources', '/sejm_komunikaty/content/' . $id . '.html');
	return @$data->body;
	