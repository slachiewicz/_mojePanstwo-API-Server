<?php

class CommuneBudget extends AppModel {

    public $useTable = false;

    /* @var DB */
    private $db;
    private $prefix = 'mf_v2_';

    public function __construct() {
        parent::__construct();
        App::import('model','DB');
        $this->db = new DB();
    }

    public function getSections($communeId, $type, $range) {
        $year = $range['year'];
        $quarters = $range['quarters'];

        return $this->db->selectAssocs("
            SELECT
              mf_v2_wydatki_gminy_dzialy.dzial_id as `id`,
              pl_budzety_wydatki_dzialy.tresc as `nazwa`,
              mf_v2_wydatki_gminy_dzialy.sum_wydatki as `wartosc`
            FROM
              mf_v2_wydatki_gminy_dzialy
            JOIN
              pl_budzety_wydatki_dzialy
                ON pl_budzety_wydatki_dzialy.id = mf_v2_wydatki_gminy_dzialy.dzial_id
            WHERE
              mf_v2_wydatki_gminy_dzialy.gmina_id = $communeId AND
              mf_v2_wydatki_gminy_dzialy.kwartal IN (". implode(',', $quarters) .") AND
              mf_v2_wydatki_gminy_dzialy.rocznik = $year
            GROUP BY
              mf_v2_wydatki_gminy_dzialy.dzial_id
            ORDER BY
              mf_v2_wydatki_gminy_dzialy.sum_wydatki DESC
        ");
    }

    public function getSection($id, $communeId, $type, $range) {
        $year = $range['year'];
        $quarters = $range['quarters'];

        $section = $this->db->selectAssoc("
            SELECT
              mf_v2_wydatki_dzialy_zakresy.dzial_id as `id`,
              pl_budzety_wydatki_dzialy.tresc as `nazwa`,
              SUM(mf_v2_wydatki_dzialy_zakresy.sum_wydatki) as `wartosc`,
              mf_v2_wydatki_dzialy_zakresy.wydatki_min_gmina_id as `wydatki_min_gmina_id`,
              mf_v2_wydatki_dzialy_zakresy.wydatki_max_gmina_id as `wydatki_max_gmina_id`
            FROM
              mf_v2_wydatki_dzialy_zakresy
            JOIN
              pl_budzety_wydatki_dzialy
                ON pl_budzety_wydatki_dzialy.id = mf_v2_wydatki_dzialy_zakresy.dzial_id
            WHERE
              mf_v2_wydatki_dzialy_zakresy.dzial_id = $id AND
              mf_v2_wydatki_dzialy_zakresy.kwartal IN (". implode(',', $quarters) .") AND
              mf_v2_wydatki_dzialy_zakresy.rocznik = $year
            GROUP BY
              mf_v2_wydatki_dzialy_zakresy.dzial_id
            ORDER BY
              mf_v2_wydatki_dzialy_zakresy.sum_wydatki DESC,
              mf_v2_wydatki_dzialy_zakresy.wydatki_min_gmina_id DESC,
              mf_v2_wydatki_dzialy_zakresy.wydatki_max_gmina_id DESC
        ");

        if($section['wydatki_min_gmina_id']) {
            $gmina_min = $this->db->selectAssoc("
                SELECT
                  sum_wydatki,
                  pl_gminy.nazwa
                FROM
                  mf_v2_wydatki_gminy_dzialy
                JOIN
                  pl_gminy ON pl_gminy.id = mf_v2_wydatki_gminy_dzialy.gmina_id
                WHERE
                  gmina_id = " . $section['wydatki_min_gmina_id'] . " AND
                  dzial_id = " . $section['id'] . " AND
                  kwartal IN (" . implode(',', $quarters) . ") AND
                  rocznik = $year
            ");

            $section['min'] = $gmina_min['sum_wydatki'];
            $section['min_nazwa'] = $gmina_min['nazwa'];
        } else {
            $section['min'] = 0;
            $section['min_nazwa'] = 'Brak';
        }

        if($section['wydatki_max_gmina_id']) {
            $gmina_max = $this->db->selectAssoc("
            SELECT
              sum_wydatki,
              pl_gminy.nazwa
            FROM
              mf_v2_wydatki_gminy_dzialy
            JOIN
              pl_gminy ON pl_gminy.id = mf_v2_wydatki_gminy_dzialy.gmina_id
            WHERE
              gmina_id = " . $section['wydatki_max_gmina_id'] . " AND
              dzial_id = " . $section['id'] . " AND
              kwartal IN (" . implode(',', $quarters) . ") AND
              rocznik = $year
        ");

            $section['max'] = $gmina_max['sum_wydatki'];
            $section['max_nazwa'] = $gmina_max['nazwa'];
        } else {
            $section['max'] = 0;
            $section['max_nazwa'] = 'Brak';
        }

        $section['rodzialy'] = $this->db->selectAssocs("
            SELECT
              mf_wydatki.rozdzial_id as `id`,
              pl_budzety_wydatki_rozdzialy.tresc as `nazwa`,
              ROUND(mf_wydatki.wydatki, 2) as `wartosc`
            FROM mf_wydatki
            JOIN
              pl_budzety_wydatki_rozdzialy
                ON pl_budzety_wydatki_rozdzialy.id = mf_wydatki.rozdzial_id
            WHERE
              mf_wydatki.dzial_id = " . $section['id'] . " AND
              mf_wydatki.gmina_id = $communeId AND
              mf_wydatki.kwartal IN (" . implode(',', $quarters) . ") AND
              mf_wydatki.rok = $year
            GROUP BY mf_wydatki.rozdzial_id
            ORDER BY wartosc DESC
        ");

        $section['buckets'] = array(
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0
        );

        return $section;
    }

}