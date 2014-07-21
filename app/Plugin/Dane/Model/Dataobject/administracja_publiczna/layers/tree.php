<?
	
	$_deep = 2;
	
	$_fields = array(
		'id', 'nazwa', 'file', 'childsCount', 'childsDirectCount'
	);
	
	$select_parts = array();
	foreach( $_fields as $_field )
		for( $d=1; $d<=$_deep; $d++ )
			$select_parts[] = '`t' . $d . '`.`' . $_field . '` AS `' .$d . '.' . $_field . '`';	
	
	$q = "
	SELECT " . implode(', ', $select_parts) . " 
	FROM `administracja_publiczna` AS t1 
	LEFT JOIN administracja_publiczna AS t2 ON t2.parent_id = t1.id 
	WHERE t1.parent_id = '" . addslashes( $id ) . "'";
	

	
	$temp = $this->DB->selectAssocs($q);
	$data = array(
		'items' => array(),
	);
	
	foreach( $temp as $t ) {
		
		$levels = array();
		
		if( $t['1.id'] ) {
			
			foreach( $_fields as $_field )
				$data['items'][ $t['1.id'] ][$_field] = $t['1.' . $_field];
			
			if( $t['2.id'] ) {
				
				foreach( $_fields as $_field )
					$data['items'][ $t['1.id'] ]['items'][ $t['2.id'] ][ $_field ] = $t['2.' . $_field];
				
				/*
				if( $t['3.id'] ) {

					$data['items'][ $t['1.id'] ]['items'][ $t['2.id'] ]['items'][ $t['3.id'] ]['id'] = $t['3.id'];
					$data['items'][ $t['1.id'] ]['items'][ $t['2.id'] ]['items'][ $t['3.id'] ]['nazwa'] = $t['3.nazwa'];
		
				}
				*/
				
			} 
			
		}
		
	}
	
	$data['items'] = array_values( $data['items'] );
	foreach( $data['items'] as &$items )
		if( !empty($item['items']) )
			$items['items'] = array_values( $items['items'] );
	
	return $data;