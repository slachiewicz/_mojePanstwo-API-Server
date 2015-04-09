<?	
		
	$q = "SELECT pl_wybory_wyniki.id, pl_wybory_wyniki.src_id as 'tura_id', pl_wybory_wyniki.kandydat_nazwa, pl_wybory_wyniki.komitet_nazwa, pl_wybory_wyniki.liczba_glosow, pl_wybory_wyniki.procent_glosow, pl_wybory_tury.tura_label as 'stanowisko' FROM pl_wybory_wyniki JOIN pl_wybory_tury ON pl_wybory_wyniki.src_type='tura' AND pl_wybory_wyniki.src_id=pl_wybory_tury.id WHERE pl_wybory_wyniki.wybory_id='1' AND pl_wybory_wyniki.`gmina_id`='" . addslashes( $id ) . "' AND pl_wybory_wyniki.`wybrany_ostatecznie`='1' AND pl_wybory_wyniki.`deleted`='0' LIMIT 1";
	
	// debug($q); die();
	
	return $this->DB->selectAssoc($q);