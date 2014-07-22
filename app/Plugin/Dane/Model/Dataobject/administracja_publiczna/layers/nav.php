<?
	
	
	
	$_deep = 3;
	
	$_fields = array(
		'id', 'nazwa', 'file', 'childsCount', 'childsDirectCount', 'parent_id'
	);
	
	$nav = array();
	if( $parent_id = $this->getData('parent_id') ) {
	
		$i = 0;
		$limit = 10;
		
		while( $item = $this->DB->selectAssoc("SELECT `" . implode("`, `", $_fields) . "` FROM `administracja_publiczna` WHERE `id`='" . addslashes( $parent_id ) . "'") ) {
		
			$nav[] = $item;
			$parent_id = $item['parent_id'];
			
			$i++;
			if( $i==$limit )
				break;
			
		}
	
	}
	
	if( !empty($nav) )
		$nav = array_reverse($nav);
	
	return $nav;
	
	
	
	
	
	
	
	
	$select_parts = array();
	foreach( $_fields as $_field )
		for( $d=1; $d<=$_deep; $d++ )
			$select_parts[] = '`t' . $d . '`.`' . $_field . '` AS `' .$d . '.' . $_field . '`';	
	
	$q = "
	SELECT " . implode(', ', $select_parts) . " 
	FROM `administracja_publiczna` AS t1 
	LEFT JOIN administracja_publiczna AS t2 ON t2.parent_id = t1.id 
	LEFT JOIN administracja_publiczna AS t3 ON t3.parent_id = t2.id AND t3.id = '" . addslashes( $this->getData('parent_id') ) . "'";
	
	
	return $q;
	

	
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
				
				if( $t['3.id'] ) {

					$data['items'][ $t['1.id'] ]['items'][ $t['2.id'] ]['items'][ $t['3.id'] ]['id'] = $t['3.id'];
					$data['items'][ $t['1.id'] ]['items'][ $t['2.id'] ]['items'][ $t['3.id'] ]['nazwa'] = $t['3.nazwa'];
		
				}
				
			} 
			
		}
		
	}
	
	$data['items'] = array_values( $data['items'] );
	foreach( $data['items'] as &$items )
		if( !empty($item['items']) )
			$items['items'] = array_values( $items['items'] );
	
	return $data;