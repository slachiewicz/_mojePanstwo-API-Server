<?
	
	$q = "SELECT ISAP_pozycje_powiazania.typ_id as 'id', ISAP_powiazania_typy.nazwa, ISAP_powiazania_typy.slug, ISAP_pozycje_powiazania.liczba_powiazan as 'count' FROM ISAP_powiazania_typy 
	JOIN ISAP_pozycje_powiazania 
		ON ISAP_powiazania_typy.id = ISAP_pozycje_powiazania.typ_id 
	WHERE ISAP_pozycje_powiazania.pozycja_id='" . addslashes( $this->getData('isap_id') ) . "' AND
		ISAP_pozycje_powiazania.deleted='0' 
	ORDER BY ISAP_pozycje_powiazania.typ_id ASC
	";
	// echo $q; die();
	// return $q;
	
	return $this->DB->selectAssocs($q);