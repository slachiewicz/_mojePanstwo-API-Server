<?

	$id = addslashes($id);
	$data = $this->DB->selectAssocs("SELECT date, followers_count as 'count' FROM twitter_accounts_history WHERE account_id = $id ORDER BY date DESC LIMIT 30");
		
	$data = array_reverse($data);
	foreach( $data as &$d )
		$d['date_str'] = dataSlownie($d['date']);
	
	
	return $data;