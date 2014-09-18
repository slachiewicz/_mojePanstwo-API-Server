<?

	return array(
		'prawo' => $this->DB->selectValue("SELECT id FROM s_podmioty WHERE instytucja_id='" . addslashes( $id ) . "'"),
		'zamowienia_udzielone' => $this->DB->selectValues("SELECT id FROM uzp_zamawiajacy WHERE instytucja_id='" . addslashes( $id ) . "'"),
		'budzet_czesci' => $this->DB->selectAssocs("SELECT id FROM pl_budzety_wydatki_czesci WHERE instytucja_id='" . addslashes( $id ) . "'"),
	);
	
