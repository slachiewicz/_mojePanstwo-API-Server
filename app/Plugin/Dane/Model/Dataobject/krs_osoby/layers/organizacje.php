<?

if (!function_exists('krs_osoby_organizacje_rola_tytul')) {
    function krs_osoby_organizacje_rola($key, $data)
    {

        $dict = array(
            'reprezentanci' => 'Reprezentant',
            'wspolnicy' => 'Wspólnik',
            'nadzorcy' => 'Członek organu nadzoru',
            'akcjonariusze' => 'Jedyny akcjonariusz',
            'zalozyciele' => 'Członek komitetu założycielskiego',
            'prokurenci' => 'Prokurent',
        );

        $label = ($key == 'reprezentanci') ?
            _ucfirst($data['funkcja']) :
            $dict[$key];

        return array(
            'key' => $key,
            'params' => $data,
            'label' => $label,
        );
    }
}

$id = addslashes($id);

$osoba = $this->DB->selectAssoc("SELECT liczba_reprezentanci, liczba_wspolnicy, liczba_nadzorcow, liczba_akcjonariusze, liczba_zalozyciele, liczba_prokurenci FROM krs_osoby WHERE id='$id'");

$data = array();


if ((int)$osoba['liczba_reprezentanci'])
    $data['reprezentanci'] = $this->DB->query("SELECT SQL_CALC_FOUND_ROWS `rola`.`id`, `rola`.`funkcja`, `rola`.`funkcja_id`,
`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`data_rejestracji`, `organizacja`.`kapital_zakladowy`
FROM `krs_reprezentanci` as `rola` 
JOIN `krs_pozycje` as `organizacja` 
ON `rola`.`pozycja_id` = `organizacja`.`id`
WHERE `rola`.`osoba_id` = '$id' 
AND `rola`.`deleted` = '0' 
ORDER BY `rola`.`id` DESC 
LIMIT 1000");


if ((int)$osoba['liczba_wspolnicy'])
    $data['wspolnicy'] = $this->DB->query("SELECT SQL_CALC_FOUND_ROWS `rola`.`id`, `rola`.`udzialy_str` as 'subtitle',
`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`data_rejestracji`, `organizacja`.`kapital_zakladowy`
FROM `krs_wspolnicy` as `rola` 
JOIN `krs_pozycje` as `organizacja` 
ON `rola`.`pozycja_id` = `organizacja`.`id`
WHERE `rola`.`osoba_id` = '$id' 
AND `rola`.`deleted` = '0' 
ORDER BY `rola`.`id` DESC 
LIMIT 1000");


if ((int)$osoba['liczba_nadzorcow'])
    $data['nadzorcy'] = $this->DB->query("SELECT SQL_CALC_FOUND_ROWS `rola`.`id`,
`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`data_rejestracji`, `organizacja`.`kapital_zakladowy`
FROM `krs_nadzorcy` as `rola` 
JOIN `krs_pozycje` as `organizacja` 
ON `rola`.`pozycja_id` = `organizacja`.`id`
WHERE `rola`.`osoba_id` = '$id' 
AND `rola`.`deleted` = '0' 
ORDER BY `rola`.`id` DESC 
LIMIT 1000");


if ((int)$osoba['liczba_akcjonariusze'])
    $data['akcjonariusze'] = $this->DB->query("SELECT SQL_CALC_FOUND_ROWS `rola`.`id`,
`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`data_rejestracji`, `organizacja`.`kapital_zakladowy`
FROM `krs_jedyni_akcjonariusze` as `rola` 
JOIN `krs_pozycje` as `organizacja` 
ON `rola`.`pozycja_id` = `organizacja`.`id`
WHERE `rola`.`osoba_id` = '$id' 
AND `rola`.`deleted` = '0' 
ORDER BY `rola`.`id` DESC 
LIMIT 1000");


if ((int)$osoba['liczba_zalozyciele'])
    $data['zalozyciele'] = $this->DB->query("SELECT SQL_CALC_FOUND_ROWS `rola`.`id`,
`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`data_rejestracji`, `organizacja`.`kapital_zakladowy`
FROM `krs_komitety_zal` as `rola` 
JOIN `krs_pozycje` as `organizacja` 
ON `rola`.`pozycja_id` = `organizacja`.`id`
WHERE `rola`.`osoba_id` = '$id' 
AND `rola`.`deleted` = '0' 
ORDER BY `rola`.`id` DESC 
LIMIT 1000");


if ((int)$osoba['liczba_prokurenci'])
    $data['prokurenci'] = $this->DB->query("SELECT SQL_CALC_FOUND_ROWS `rola`.`id`, `rola`.`rodzaj`,
`organizacja`.`id`, `organizacja`.`nazwa`, `organizacja`.`forma_prawna_str`, `organizacja`.`data_rejestracji`, `organizacja`.`kapital_zakladowy`
FROM `krs_prokurenci` as `rola` 
JOIN `krs_pozycje` as `organizacja` 
ON `rola`.`pozycja_id` = `organizacja`.`id`
WHERE `rola`.`osoba_id` = '$id' 
AND `rola`.`deleted` = '0' 
ORDER BY `rola`.`id` DESC 
LIMIT 1000");


$organizacje = array();

if (!empty($data)) {
    foreach ($data as $key => $items) {
        if (!empty($items)) {
            foreach ($items as $item) {

                $_rola = $item['rola'];
                $_organizacja = $item['organizacja'];

                if (!empty($organizacje)) {

                    reset($organizacje);
                    $found = false;
                    foreach ($organizacje as &$organizacja)
                        if ($organizacja['id'] == $_organizacja['id']) {
                            $organizacja['role'][] = krs_osoby_organizacje_rola($key, $_rola);
                            $found = true;
                        }

                    if (!$found) {
                        $_organizacja['role'] = array(
                            krs_osoby_organizacje_rola($key, $_rola),
                        );
                        $organizacje[] = $_organizacja;
                    }

                } else {
                    $_organizacja['role'] = array(
                        krs_osoby_organizacje_rola($key, $_rola),
                    );
                    $organizacje[] = $_organizacja;
                }
            }
        }
    }
}

return $organizacje;