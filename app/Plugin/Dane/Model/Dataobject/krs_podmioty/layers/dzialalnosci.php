<?

$data = $this->DB->query("SELECT `pkd2007`.`id`, `pkd2007`.`nazwa` 
		FROM `krs_dzialalnosci` 
		JOIN `krs_pozycje-dzialalnosci` 
		ON `krs_pozycje-dzialalnosci`.`dzialalnosc_id` = `krs_dzialalnosci`.`id`
		AND `krs_pozycje-dzialalnosci`.krs_id='" . addslashes($id) . "'
		AND `krs_pozycje-dzialalnosci`.`deleted`='0' 
		JOIN `pkd2007` ON `krs_dzialalnosci`.`pkd2007_id` = `pkd2007`.`id` 
		ORDER BY `krs_dzialalnosci`.`id` ASC");

return $data;

$output = array();
foreach ($data as $d)
    $output[] = $d['krs_dzialalnosci'];

return $output;