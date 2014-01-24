<?php
$komitety = $this->DB->query("SELECT `pl_gminy_radni`.`komitet_id`, COUNT(*) AS `count`, `pkw_komitety`.`skrot_nazwy` AS 'nazwa', `pkw_komitety`.`klub_id` FROM `pl_gminy_radni` JOIN `pkw_komitety` ON `pl_gminy_radni`.`komitet_id` = `pkw_komitety`.`id` WHERE `pl_gminy_radni`.`gmina_id`='" . $id . "' AND `pl_gminy_radni`.`wybrany`='1' GROUP BY `pl_gminy_radni`.`komitet_id` ORDER BY `count` DESC LIMIT 4");

$komitety_count = $this->DB->query("SELECT COUNT(*) as 'total' FROM `pl_gminy_radni` WHERE `pl_gminy_radni`.`gmina_id`='" . $id . "' AND `pl_gminy_radni`.`wybrany`='1'");
//debug($komitety);

$komitety_count = @ $komitety_count[0][0]['total'];
if ($komitety_count)
    foreach ($komitety as &$komitet)
        $komitet = array_merge($komitet, array(
            'percent' => round(1000 * $komitet[0]['count'] / $komitety_count) / 10,
            'nazwa' => str_replace('""', '"', $komitet['pkw_komitety']['nazwa']),
        ));

return $komitety;