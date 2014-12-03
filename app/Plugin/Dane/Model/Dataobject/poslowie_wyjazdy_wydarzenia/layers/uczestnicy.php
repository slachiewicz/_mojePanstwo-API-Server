<?
	
	return $this->DB->selectAssocs("
		SELECT 
			poslowie_wyjazdy.id,
			poslowie_wyjazdy.koszt_transport,
			poslowie_wyjazdy.koszt_dieta,
			poslowie_wyjazdy.koszt_hotel,
			poslowie_wyjazdy.koszt_dojazd,
			poslowie_wyjazdy.koszt_ubezpieczenie,
			poslowie_wyjazdy.koszt_fundusz,
			poslowie_wyjazdy.koszt_kurs,
			poslowie_wyjazdy.koszt_zaliczki,
			poslowie_wyjazdy.koszt,
			poslowie_wyjazdy.glosowania_daty,
			s_poslowie_kadencje.id AS 'poslowie.id', 
			mowcy_poslowie.mowca_id AS 'ludzie.id', 
			s_poslowie_kadencje.nazwa AS 'poslowie.nazwa', 
			s_kluby.id AS 'sejm_kluby.id', 
			s_kluby.nazwa AS 'sejm_kluby.nazwa', 
			s_kluby.skrot AS 'sejm_kluby.skrot' 
		FROM poslowie_wyjazdy 
		JOIN s_poslowie_kadencje ON poslowie_wyjazdy.posel_id = s_poslowie_kadencje.id
		JOIN mowcy_poslowie ON poslowie_wyjazdy.posel_id = mowcy_poslowie.posel_id
		JOIN s_kluby ON poslowie_wyjazdy.klub_id = s_kluby.id
		WHERE 
			poslowie_wyjazdy.deleted='0' AND 
			poslowie_wyjazdy.wydarzenie_id = '" . addslashes( $id ) . "'
	");