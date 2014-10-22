<?

	$items = $this->DB->selectAssocs("
		SELECT
			`child`.`id` as 'child.id', 
			`child`.`tytul` as 'child.tytul', 
			`child`.`pierwotny` as 'child.root', 
			`parent`.`id` as 'parent.id', 
			`parent`.`tytul` as 'parent.tytul' 
			`parent`.`pierwotny` as 'child.root' 
		FROM
			`prawo` as `parent`
		JOIN
			`prawo_powiazania` AS `parent-child` ON
				(`parent`.`id` = `parent-child`.`a_id` AND 
				`parent-child`.`typ_id` = '12' AND 
				`parent-child`.`deleted` = '0')
		JOIN 
			 `prawo` AS `child` ON 
				(`parent-child`.`b_id` = `child`.`id` AND 
				`child`.`status_id` = '1' AND 
				`child`.`akcept` = '1')
		JOIN 
			`prawo-hasla` AS `parent-tags` ON 
				(`parent`.`id` = `parent-tags`.`prawo_id` AND
				`parent-tags`.`deleted` = '0') 
		JOIN 
			`prawo-hasla` AS `child-tags` ON 
				(`child`.`id` = `child-tags`.`prawo_id` AND
				`child-tags`.`deleted` = '0') 		
		WHERE 
			`parent`.`akcept` = '1' AND 
			`parent`.`status_id` = '1' AND 
			`parent-tags`.`haslo_id` = '$id' AND 
			`child-tags`.`haslo_id` = '$id' 
		ORDER BY 
			`parent`.`typ_id` ASC, 
			`parent`.`data_publikacji` DESC, 
			`child`.`typ_id` ASC, 
			`child`.`data_publikacji` DESC
	");
	
	
	$data = array();
	foreach( $items as $item ) {
		
		$child = array(
			'id' => $item['child.id'],
			'tytul' => $item['child.tytul'],
			'root' => $item['child.pierwotny'],
		);
		
		$parent = array(
			'id' => $item['parent.id'],
			'tytul' => $item['parent.tytul'],
			'root' => $item['parent.pierwotny'],
		);
		
		if( array_key_exists($parent['id'], $data) )
			$data[ $parent['id'] ]['children'][] = array(
				'item' => $child,
			);
		else
			$data[ $parent['id'] ] = array(
				'item' => $parent,
				'children' => array(
					array(
						'data' => $child,
					),
				),
			);
					
	}
	$data = array_values( $data );
	
	return $data;