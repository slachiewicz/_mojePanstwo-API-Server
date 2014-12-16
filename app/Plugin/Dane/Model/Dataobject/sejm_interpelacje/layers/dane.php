<?
		
	$wydarzenia = $this->DB->selectAssocs("SELECT id, typ_id, nazwa, autor_str, adresaci_str, data, data_ogloszenia, dokument_id FROM s_interpelacje_tablice WHERE interpelacja_id='$id' AND akcept='1'");
	
	$wydarzenie_id = (isset($params) && isset($params['t']) && is_numeric($params['t']) && $params['t']) ? $params['t'] : $wydarzenia[ 0 ]['id'];

	$wydarzenie = array();
	
	foreach( $wydarzenia as $w )
		if( $w['id'] == $wydarzenie_id )
			$wydarzenie = $w;
	
	$teksty = $wydarzenie_id ? $this->DB->selectAssocs("
		SELECT 
			s_interpelacje_sekcje_texty.id, 
			s_interpelacje_sekcje_texty.html, 
			mowcy.id as 'mowca_id', 
			mowcy.nazwa as 'mowca_nazwa', 
			wypowiedzi_funkcje.id as 'funkcja_id', 
			wypowiedzi_funkcje.nazwa as 'funkcja_nazwa' 
		FROM s_interpelacje_sekcje_texty 
			JOIN s_interpelacje_tablice ON s_interpelacje_sekcje_texty.pole_id=s_interpelacje_tablice.pole_id 
			LEFT JOIN mowcy ON s_interpelacje_sekcje_texty.mowca_id=mowcy.id 
			LEFT JOIN wypowiedzi_funkcje ON s_interpelacje_sekcje_texty.funkcja_id=wypowiedzi_funkcje.id 
		WHERE 
			s_interpelacje_tablice.id='$wydarzenie_id' AND 
			s_interpelacje_sekcje_texty.akcept='1' AND 
			s_interpelacje_sekcje_texty.status!='-1'
	") : array();
		
  return array(
  	'wydarzenia' => $wydarzenia,
  	'wydarzenie_id' => $wydarzenie_id,
  	'wydarzenie' => $wydarzenie,
  	'teksty' => $teksty,
  );
  
  