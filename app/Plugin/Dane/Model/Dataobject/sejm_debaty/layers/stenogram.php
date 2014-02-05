<?
	$wystapienia = $this->DB->selectAssocs("SELECT stenogramy_wystapienia.id, stenogramy_wystapienia.video, SUBSTRING(stenogramy_wystapienia.p_txt, 1, 1500) as p_txt, LENGTH(stenogramy_wystapienia.p_txt) as 'length', mowcy.nazwa as 'mowca_nazwa', wypowiedzi_funkcje.nazwa as 'funkcja_nazwa', stenogramy_wystapienia.marszalek, stenogramy_wystapienia.mowca_id, mowcy.avatar as 'mowca_avatar', s_glosowania.id as 'glosowanie_id' FROM stenogramy_wystapienia LEFT JOIN mowcy ON stenogramy_wystapienia.mowca_id=mowcy.id LEFT JOIN wypowiedzi_funkcje ON stenogramy_wystapienia.mowca_funkcja_id=wypowiedzi_funkcje.id LEFT JOIN s_glosowania ON stenogramy_wystapienia.id=s_glosowania.wystapienie_id WHERE stenogramy_wystapienia.subpunkt_id='$id' ORDER BY stenogramy_wystapienia.kolejnosc ASC");
	
	return array(
		'wystapienia' => $wystapienia,
	);