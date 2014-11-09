<?
		
	$data = S3::getObject('resources', 'COE/sittings/' . $id . '.html');
	return array(
		'html' => @$data->body,
	);