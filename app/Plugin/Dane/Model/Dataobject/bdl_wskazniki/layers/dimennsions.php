<?
	
	// `BDL_podgrupy_dims`.nazwa AS `label`
	// `BDL_dims_values`.nazwa as `option_value`
	
	$wymiary = $this->DB->query("SELECT `BDL_podgrupy_dims`.nazwa, `BDL_podgrupy_dims`.ord, `BDL_dims_values`.id, `BDL_dims_values`.nazwa FROM `BDL_podgrupy_dims`
	JOIN `BDL_podgrupy_dims-values` ON `BDL_podgrupy_dims`.`ord` = `BDL_podgrupy_dims-values`.`podgrupa_ord` AND `BDL_podgrupy_dims-values`.`podgrupa_id`=`BDL_podgrupy_dims`.`podgrupa_id`
	JOIN `BDL_dims_values` ON `BDL_podgrupy_dims-values`.`value_id` = `BDL_dims_values`.id
	WHERE `BDL_podgrupy_dims`.`podgrupa_id`=$id AND  `BDL_podgrupy_dims`.deleted = '0' AND  `BDL_podgrupy_dims-values`.deleted = '0' ORDER BY `BDL_podgrupy_dims`.ord ASC, `BDL_podgrupy_dims-values`.ord ASC");
	
	$output = array();
	
	foreach( $wymiary as $wymiar )
	{
		
		if( array_key_exists($wymiar['BDL_podgrupy_dims']['ord'], $output) )
		
			$output[ $wymiar['BDL_podgrupy_dims']['ord'] ]['options'][] = array(
				'id' => $wymiar['BDL_dims_values']['id'],
				'value' => $wymiar['BDL_dims_values']['nazwa'],
			);
		
		else
			$output[ $wymiar['BDL_podgrupy_dims']['ord'] ] = array(
				'order' => $wymiar['BDL_podgrupy_dims']['ord'],
				'label' => $wymiar['BDL_podgrupy_dims']['nazwa'],
				'options' => array(array(
					'id' => $wymiar['BDL_dims_values']['id'],
					'value' => $wymiar['BDL_dims_values']['nazwa'],
				)),
			);
			
	}
	
	return $output;