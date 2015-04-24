<?php

$krs_pozycje = $this->DB->selectAssocs("
	
	SELECT 
		`id`, `nazwa`, `adres_miejscowosc`, `adres_ulica`, `adres_numer`, `adres_lokal`, 
		`adres_miejscowosc`, `adres_kod_pocztowy`, `adres_poczta`, `adres_kraj`
	FROM 
		`krs_pozycje`
	WHERE 
		`gmina_id` = $id AND 
		`akcept` = '1'

");

foreach($krs_pozycje as $i => $krs_pozycja) {

	$grupy = array(
		'wspolnik'	=> array(),
		'reprezentat'	=> array(),
		'akcjonariusz'	=> array(),
		'prokurent'	=> array(),
		'nadzorca'	=> array(),
		'zalozyciel'	=> array()
	);

	$osoba_id = (int) $krs_pozycja['id'];
	$osoby = $this->DB->selectAssocs("
		SELECT `krs_osoby-pozycje`.*, `krs_osoby`.`imie_nazwisko`
		FROM `krs_osoby-pozycje`
		JOIN `krs_osoby` ON `krs_osoby`.`id` = `krs_osoby-pozycje`.`osoba_id`
		WHERE `krs_osoby-pozycje`.`pozycja_id` = $osoba_id
	");

	foreach($grupy as $nazwa => $grupa) {
		foreach($osoby as $osoba) {
			if(isset($osoba[$nazwa]) && $osoba[$nazwa] == "1") {
				$_osoba = array(
					'imie_nazwisko' => $osoba['imie_nazwisko']				
				);

				if($nazwa == "reprezentat")
					$_osoba["funkcja"] = $osoba["reprezentat_funkcja"];

				$grupy[$nazwa][] = $_osoba;
			}
		}
	}

	$krs_pozycje[$i]['osoby'] = $grupy;

}

return json_encode($krs_pozycje);
