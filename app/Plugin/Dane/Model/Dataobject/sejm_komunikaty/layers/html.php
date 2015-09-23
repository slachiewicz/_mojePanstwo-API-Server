<?
	
	$data = @S3::getObject('resources', '/sejm_komunikaty/content/' . $id . '_modified.html');
	return @$data->body;
	