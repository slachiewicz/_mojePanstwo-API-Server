<?

return $this->DB->selectRow("SELECT okregi.id, okregi.rok, okregi.nr_okregu, AsText(okregi.polygon), okregi.dzielnice, okregi.ilosc_mieszkancow, okregi.liczba_mandatow, okregi.ilosc_miesz_norma_przedst FROM pl_gminy_radni JOIN pl_gminy_krakow_okregi as okregi ON okregi.okreg_id = pl_gminy_radni.okreg_id WHERE pl_gminy_radni.id = " . addslashes( $id ));

