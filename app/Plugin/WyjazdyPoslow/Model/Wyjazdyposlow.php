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
		GROUP BY `poslowie_wyjazdy`.`posel_id` 
		ORDER BY SUM(`poslowie_wyjazdy`.`koszt`) DESC
		LIMIT 5
		");

        $output['calosc']['klubowe'] = $DB->selectAssocs("SELECT
		`s_kluby`.`id`, 
		`s_kluby`.`nazwa`, 
		SUM(`poslowie_wyjazdy`.`koszt`) as 'sum',
		COUNT(`poslowie_wyjazdy`.`klub_id`) as 'count'
		FROM `poslowie_wyjazdy` 
		JOIN `s_kluby`
		ON `poslowie_wyjazdy`.`klub_id` = `s_kluby`.`id`
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
SELECT l.iso2cc AS code, COUNT(e.id) AS ilosc_wyjazdow, SUM(w.koszt) AS laczna_kwota
FROM poslowie_wyjazdy w
INNER JOIN poslowie_wyjazdy_wydarzenia e ON (w.wydarzenie_id = e.id)
INNER JOIN poslowie_wyjazdy_lokalizacje l ON (l.lokalizacja = e.lokalizacja)
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
    l.iso2cc AS code,
    e.id AS wydarzenie_id,
    e.delegacja,
    e.lokalizacja,
    e.wniosek_nr,
    e.liczba_dni,
    e.date_start AS od,
    e.date_stop AS do,
    koszt_transport,
    koszt_dieta,
    koszt_hotel,
    koszt_dojazd,
    koszt_ubezpieczenie,
    koszt_fundusz,
    koszt_kurs,
    koszt_zaliczki,
    koszt AS koszt_suma,
    p.nazwa AS posel,
    k.nazwa AS klub,
    k.glosowania_skrot AS klub_skrot

FROM poslowie_wyjazdy w
INNER JOIN poslowie_wyjazdy_wydarzenia e ON (w.wydarzenie_id = e.id)
INNER JOIN poslowie_wyjazdy_lokalizacje l ON (l.lokalizacja = e.lokalizacja)
INNER JOIN s_poslowie_kadencje p ON (w.posel_id = p.id)
INNER JOIN s_kluby k ON (w.klub_id = k.id)
WHERE l.iso2cc = '$countryCode'
ORDER BY e.date_start, e.id, w.id
SQL;

        $rows = $DB->selectAssocs($sql);

        if (!$rows) {
            throw new NotFoundException();
        }

        $tree = array();
        $wydarzenie = null;

        $last_wydarzenie = null;

        foreach ($rows as $row) {
            if ($row['wydarzenie_id'] != $last_wydarzenie) {
                if ($wydarzenie != null)
                    array_push($tree, $wydarzenie);

                $wydarzenie = array_intersect_key($row, array_flip(array(
                        'delegacja', 'lokalizacja', 'wniosek_nr', 'liczba_dni', 'od', 'do'))
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

            $last_wydarzenie = $row['wydarzenie_id'];
        }

        return $tree;
    }
}
