<?

$data = $this->DB->query("SELECT `krs_dzialalnosci`.`id`, `krs_dzialalnosci`.`str`
		FROM `krs_dzialalnosci` 
		JOIN `krs_pozycje-dzialalnosci` 
		ON `krs_pozycje-dzialalnosci`.`dzialalnosc_id` = `krs_dzialalnosci`.`id`
		AND `krs_pozycje-dzialalnosci`.krs_id='" . addslashes($id) . "'
		AND `krs_pozycje-dzialalnosci`.`deleted`='0' 
		ORDER BY `krs_dzialalnosci`.`id` ASC");

$output = array();
foreach ($data as $d)
    $output[] = $d['krs_dzialalnosci'];

return $output;