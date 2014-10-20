<?

	$data = $this->DB->selectAssocs("
	SELECT
		`msig_dzialy`.`id` as 'dzial.id',
		`msig_dzialy`.`nazwa` as 'dzial.nazwa',
		`msig_dzialy`.`strona_od` as 'dzial.strona',
		`msig_rozdzialy`.`id` as 'rozdzial.id',
		`msig_rozdzialy`.`nazwa` as 'rozdzial.nazwa',
		`msig_rozdzialy`.`strona_od` as 'rozdzial.strona',
		`msig_pozycje`.`id` as 'pozycja.id',
		`msig_pozycje`.`nazwa` as 'pozycja.nazwa',
		`msig_pozycje`.`strona_od` as 'pozycja.strona'
	FROM `msig_dzialy`
	LEFT JOIN `msig_rozdzialy` 
		ON `msig_rozdzialy`.`dzial_id` = `msig_dzialy`.`id`
	LEFT JOIN `msig_pozycje`
		ON `msig_pozycje`.`rozdzial_id` = `msig_rozdzialy`.`id` 
	WHERE 
		`msig_dzialy`.`wydanie_id` = '" . addslashes( $id ) . "' AND 
		`msig_dzialy`.`akcept` = '1' 
	ORDER BY
		`msig_dzialy`.`ord` ASC, 
		`msig_rozdzialy`.`ord` ASC, 
		`msig_pozycje`.`ord` ASC 
	");
	
	$toc = array();
	
	foreach( $data as $d ) {
		
		$toc[ $d['dzial.id'] ]['id'] = $d['dzial.id'];
		$toc[ $d['dzial.id'] ]['nazwa'] = $d['dzial.nazwa'];
		$toc[ $d['dzial.id'] ]['strona'] = $d['dzial.strona'];
		
		if( $d['rozdzial.id'] ) {
			$toc[ $d['dzial.id'] ]['rozdzialy'][ $d['rozdzial.id'] ]['id'] = $d['rozdzial.id'];
			$toc[ $d['dzial.id'] ]['rozdzialy'][ $d['rozdzial.id'] ]['nazwa'] = $d['rozdzial.nazwa'];
			$toc[ $d['dzial.id'] ]['rozdzialy'][ $d['rozdzial.id'] ]['strona'] = $d['rozdzial.strona'];
		}
		
		if( $d['pozycja.id'] ) {
			$toc[ $d['dzial.id'] ]['rozdzialy'][ $d['rozdzial.id'] ]['pozycje'][ $d['pozycja.id'] ]['id'] = $d['pozycja.id'];
			$toc[ $d['dzial.id'] ]['rozdzialy'][ $d['rozdzial.id'] ]['pozycje'][ $d['pozycja.id'] ]['nazwa'] = $d['pozycja.nazwa'];
			$toc[ $d['dzial.id'] ]['rozdzialy'][ $d['rozdzial.id'] ]['pozycje'][ $d['pozycja.id'] ]['strona'] = $d['pozycja.strona'];
		}

	}
	
	return $toc;