<?php

$id = (int) $id;

$ranges = array(
    array(0, 20000),
    array(20000, 50000),
    array(50000, 100000),
    array(100000, 500000),
    array(500000, 999999999)
);

$commune = $this->DB->selectAssoc("SELECT id, liczba_ludnosci FROM pl_gminy WHERE id = $id");

$range = 0;
foreach($ranges as $i => $r) {
    if($commune['liczba_ludnosci'] >= $r[0] && $commune['liczba_ludnosci'] <= $r[1]) {
        $range = $i;
        break;
    }
}

$data = array();
$data['stats'] = array();

$data['sections'] = $this->DB->selectAssocs("
    SELECT
      pl_budzety_wydatki_dzialy.id,
      pl_budzety_wydatki_dzialy.src,
      pl_budzety_wydatki_dzialy.tresc,
      mf_wydatki_dzialy_zakresy.sum_wydatki,
      mf_wydatki_dzialy_zakresy.wydatki_min_gmina_id,
      mf_wydatki_dzialy_zakresy.wydatki_max_gmina_id,
      mf_wydatki_dzialy_zakresy.zakres
    FROM
      mf_wydatki_dzialy_zakresy
    JOIN
      pl_budzety_wydatki_dzialy
        ON pl_budzety_wydatki_dzialy.id = mf_wydatki_dzialy_zakresy.dzial_id
    WHERE
      mf_wydatki_dzialy_zakresy.zakres = '$range' AND mf_wydatki_dzialy_zakresy.sum_wydatki>0
    GROUP BY
      mf_wydatki_dzialy_zakresy.dzial_id
    ORDER BY
      mf_wydatki_dzialy_zakresy.sum_wydatki DESC
");

foreach($data['sections'] as $i => $section)
{
    $gmina_min = $this->DB->selectAssoc("
                SELECT
                  sum_wydatki,
                  pl_gminy.nazwa
                FROM
                  mf_wydatki_gminy_dzialy
                JOIN
                  pl_gminy ON pl_gminy.id = mf_wydatki_gminy_dzialy.gmina_id
                WHERE
                  gmina_id = ".$section['wydatki_min_gmina_id']." AND
                  dzial_id = ".$section['id']."
            ");

    $data['sections'][$i]['min'] = $gmina_min['sum_wydatki'];
    $data['sections'][$i]['min_nazwa'] = is_null($gmina_min['nazwa']) ? 'Brak' : $gmina_min['nazwa'];

    $gmina_max = $this->DB->selectAssoc("
                SELECT
                  sum_wydatki,
                  pl_gminy.nazwa
                FROM
                  mf_wydatki_gminy_dzialy
                JOIN
                  pl_gminy ON pl_gminy.id = mf_wydatki_gminy_dzialy.gmina_id
                WHERE
                  gmina_id = ".$section['wydatki_max_gmina_id']." AND
                  dzial_id = ".$section['id']."
            ");

    $data['sections'][$i]['max'] = $gmina_max['sum_wydatki'];
    $data['sections'][$i]['max_nazwa'] = is_null($gmina_max['nazwa']) ? 'Brak' : $gmina_max['nazwa'];


    $data['sections'][$i]['commune'] = $this->DB->selectValue("
        SELECT
          sum_wydatki
        FROM
          mf_wydatki_gminy_dzialy
        WHERE
          gmina_id = ".$id." AND
          dzial_id = ".$section['id']."
    ");
}

foreach($data['sections'] as $i => $section) {
    $data['sections'][$i]['buckets'] = array();
    $min = (int)$section['min'];
    $max = (int)$section['max'];
    $segment = (int) (($max - $min) / 10);
    $segments = array();
    for ($m = 1; $m <= 10; $m++) {
        $segments[] = array(
            'min' => $min + $m * $segment,
            'max' => $min + ($m + 1) * $segment
        );
    }

    foreach ($segments as $segment) {
        $count = (int) $this->DB->selectValue("
                    SELECT
                        COUNT(*)
                    FROM
                      mf_wydatki_gminy_dzialy
                    WHERE
                      dzial_id = " . $section['id'] . " AND
                      zakres = '".$section['zakres']."' AND
                      sum_wydatki
                        BETWEEN " . $segment['min'] . " AND " . $segment['max'] . "
                ");
        $data['sections'][$i]['buckets'][] = array(
            'count'     => $count,
            'height'    => $count
        );
    }
}

return $data;


