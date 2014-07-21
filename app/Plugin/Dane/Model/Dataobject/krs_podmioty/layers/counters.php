<?

	return $this->DB->selectAssoc("SELECT `liczba_oddzialow`, `liczba_zmian_umow`, `liczba_emisji_akcji` FROM `krs_pozycje` WHERE `id`='$id'");