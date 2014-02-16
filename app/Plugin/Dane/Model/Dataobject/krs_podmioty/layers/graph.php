<?
	
	$q = 'MATCH (a:podmiot {mp_id: "114"})-[r]-(b)-[t]-(c) RETURN a,b,c LIMIT 100';
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"http://neo.epf.p2.tiktalik.com:7474/db/data/transaction/commit");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('statements' => array($q))));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);
	
	curl_close( $ch );
	
	var_export( $server_output );
	die();