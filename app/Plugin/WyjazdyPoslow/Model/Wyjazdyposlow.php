<?php

class Wyjazdyposlow extends AppModel
{

    public $useTable = false;

    public function getStats()
    {

        App::import('model', 'DB');
        $DB = new DB();

        // App::Import('ConnectionManager');
        // $MPSearch = ConnectionManager::getDataSource('MPSearch');


        $output = array();

        // CAŁOŚCIOWO

        $output['calosc']['indywidualne'] = $DB->selectAssocs("SELECT 
		`s_poslowie_kadencje`.`id`, 
		`s_poslowie_kadencje`.`nazwa`, 
		`s_kluby`.`id` as 'klub_id', 
		`s_kluby`.`skrot`, 
		`mowcy_poslowie`.`mowca_id`, 
		SUM(`poslowie_wyjazdy`.`koszt`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`posel_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_poslowie_kadencje` 
		ON `poslowie_wyjazdy`.`posel_id` = `s_poslowie_kadencje`.`id` 
		JOIN `s_kluby`
		ON `s_poslowie_kadencje`.`klub_id` = `s_kluby`.`id` 
		JOIN `mowcy_poslowie`
		ON `s_poslowie_kadencje`.`id` = `mowcy_poslowie`.`posel_id`
		JOIN poslowie_wyjazdy_wydarzenia e
		ON e.id = poslowie_wyjazdy.wydarzenie_id
		WHERE e.deleted = '0' AND poslowie_wyjazdy.deleted = '0'
		GROUP BY `poslowie_wyjazdy`.`posel_id`
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt`) DESC
		LIMIT 5
		");

        $output['calosc']['klubowe'] = $DB->selectAssocs("SELECT
		`s_kluby`.`id`, 
		`s_kluby`.`nazwa`, 
		SUM(`poslowie_wyjazdy`.`koszt`) as 'sum',
		COUNT(DISTINCT e.id) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_kluby`
		ON `poslowie_wyjazdy`.`klub_id` = `s_kluby`.`id`
		JOIN poslowie_wyjazdy_wydarzenia e
		ON e.id = poslowie_wyjazdy.wydarzenie_id
		WHERE e.deleted = '0' AND poslowie_wyjazdy.deleted = '0'
		GROUP BY `poslowie_wyjazdy`.`klub_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt`) DESC
		LIMIT 5
		");
		
		return $output;
        

    }

    public function getWorldStats()
    {
        App::import('model', 'DB');
        $DB = new DB();

        $sql = <<<SQL
SELECT l.iso2cc AS code, MIN(kraj) AS kraj, COUNT(DISTINCT e.id) AS ilosc_wyjazdow, SUM(w.koszt) AS laczna_kwota
FROM poslowie_wyjazdy w
INNER JOIN poslowie_wyjazdy_wydarzenia e ON (w.wydarzenie_id = e.id)
INNER JOIN poslowie_wyjazdy_lokalizacje l ON (l.lokalizacja = e.lokalizacja)
WHERE e.deleted = '0' AND w.deleted = '0'
GROUP BY l.iso2cc
ORDER BY laczna_kwota DESC
SQL;
        return $DB->selectAssocs($sql);
    }

    public function getCountryDetails($countryCode)
    {
        App::import('model', 'DB');
        $DB = new DB();

        $countryCode = $DB->DB->real_escape_string($countryCode);

        $sql = <<<SQL
SELECT
    l.iso2cc AS country_code,
    e.id AS wydarzenie_id,
    e.delegacja,
    kraj,
    miasto,
    e.wniosek_nr,
    e.liczba_dni,
    e.date_start AS od,
    e.date_stop AS do,
    w.koszt_transport,
    w.koszt_dieta,
    w.koszt_hotel,
    w.koszt_dojazd,
    w.koszt_ubezpieczenie,
    w.koszt_fundusz,
    w.koszt_kurs,
    w.koszt_zaliczki,
    w.koszt AS koszt_suma,
    p.nazwa AS posel,
    k.nazwa AS klub,
    k.glosowania_skrot AS klub_skrot

FROM poslowie_wyjazdy w
INNER JOIN poslowie_wyjazdy_wydarzenia e ON (w.wydarzenie_id = e.id)
INNER JOIN poslowie_wyjazdy_lokalizacje l ON (l.lokalizacja = e.lokalizacja)
INNER JOIN s_poslowie_kadencje p ON (w.posel_id = p.id)
LEFT OUTER JOIN s_kluby k ON (w.klub_id = k.id)
WHERE l.iso2cc = '$countryCode' AND e.deleted = '0' AND w.deleted = '0'
ORDER BY e.date_start, e.id, w.id
SQL;

        $rows = $DB->selectAssocs($sql);

        if (!$rows) {
            throw new NotFoundException();
        }

        $tree = array();
        $wydarzenie = null;

        $last_wydarzenie = null;

        for ($i = 0; $i < count($rows); $i++) {
            $row = $rows[$i];
            if ($row['wydarzenie_id'] != $last_wydarzenie) {
                if ($wydarzenie != null)
                    array_push($tree, $wydarzenie);

                $wydarzenie = array_intersect_key($row, array_flip(array(
                        'delegacja', 'country_code', 'kraj', 'miasto', 'wniosek_nr', 'liczba_dni', 'od', 'do'))
                );
                $wydarzenie['poslowie'] = array();
            }

            array_push($wydarzenie['poslowie'], array_intersect_key($row, array_flip(array(
                'posel',
                'klub',
                'klub_skrot',
                'koszt_suma',
                'koszt_transport',
                'koszt_dieta',
                'koszt_hotel',
                'koszt_dojazd',
                'koszt_ubezpieczenie',
                'koszt_fundusz',
                'koszt_kurs',
                'koszt_zaliczki',
            ))));

            if ($i == count($rows) - 1 && $row['wydarzenie_id'] != $last_wydarzenie) {
                array_push($tree, $wydarzenie); // push last
            }
            $last_wydarzenie = $row['wydarzenie_id'];
        }

        return $tree;
    }
}
