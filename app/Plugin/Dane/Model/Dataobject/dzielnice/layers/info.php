<?
	
	return $this->DB->selectAssoc("SELECT liczba_powierzchnia, liczba_mieszkancow, liczba_gestosc_zaludnienia, url_wiki, 	liczba_frekwencja FROM pl_gminy_krakow_dzielnice_dane WHERE dzielnica_id='" . addslashes( $id ) . "'");