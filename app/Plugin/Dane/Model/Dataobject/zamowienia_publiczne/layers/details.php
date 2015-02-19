<?php
	
	$output = $this->DB->selectAssoc("SELECT `data_start`, `data_stop`, `oferty_data_stop`, `oferty_godz`, `czas_miesiace`, `czas_dni`, `oferty_liczba_dni`, `le_adres_aukcja`, `le_adres_opis`, `le_data_skl`, `le_godz_skl`, `le_term_otw`, `le_term_war_zam` FROM `uzp_dokumenty` WHERE `id`='$id' AND akcept='1'");
	
	$output['kryteria'] = $this->DB->selectAssocs("SELECT `nazwa`, `punkty` FROM `uzp_dokumenty_kryteria` WHERE `dokument_id`='" . $id . "' AND `deleted`='0' ORDER BY `ord` ASC");
	
	$body = $this->S3Files->getBody('resources/UZP-details/' . $id . '.dat');
	
	if( $body && ($data = unserialize($body)) && is_array($data) ) {
		
		foreach( $data as $key => $value )
			if( in_array(str_ireplace('.', '', $value), array('Brak warunku szczegółowego', 'Zamawiający nie stawia szczególnych wymagań do tego warunku', 'nie określa się', 'Zamawiający nie wyznacza szczegółowego warunku w tym zakresie', 'Zamawiający nie stawia specjalnych wymagań odnośnie spełnienia tego warunku', 'Nie dotyczy', 'nie dotyczy', 'Zamawiający nie określa szczegółowo tego warunku', 'zamawiający nie precyzuje warunku w tym zakresie')) )
				unset( $data[ $key ] );
		
		unset( $data['niepelnosprawne'] );
		$output = array_merge($output, $data);
		
	}
	
	return $output;