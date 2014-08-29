<?php

class Indeks extends AppModel
{
    
    public $useDbConfig = null;
    public $useTable = false;

    public function get( $id )
    {
		
		$output = array();
				
		App::import('model','DB');
		$this->DB = new DB();
		
        $output['details'] = $this->DB->selectAssoc("SELECT `id`, `nazwa` FROM `kultura_indeksy` WHERE `id`='$id'");
		$output['organizations'] = $this->DB->selectAssocs("SELECT id, nazwa, kultura_indeks_score as 'score' 
			FROM `krs_pozycje`
			WHERE `akcept` = '1'
			AND `kultura_indeks_id` = '" . $id . "' 
			ORDER BY `krs_pozycje`.`kultura_indeks_score` DESC 
			LIMIT 20
		");
		$output['pkd'] = $this->DB->selectAssocs("SELECT `pkd2007`.`id`, `pkd2007`.`nazwa`, COUNT(*) AS 'count' FROM `krs_pozycje-pkd2007` JOIN `pkd2007` ON `krs_pozycje-pkd2007`.`pkd_id` = `pkd2007`.`id` JOIN `krs_pozycje` ON `krs_pozycje-pkd2007`.`pozycja_id` = `krs_pozycje`.`id` WHERE `krs_pozycje`.`akcept` = '1' AND `krs_pozycje`.`kultura_indeks_id` = '" . $id . "' AND `pkd2007`.`kultura_indeks_id` = '" . $id . "' GROUP BY `krs_pozycje-pkd2007`.`pkd_id` ORDER BY `count` DESC LIMIT 20");
		
		return $output;

    }
    
} 