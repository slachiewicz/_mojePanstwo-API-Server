<?php

class KrakowOkregi extends AppModel {

    public $useTable = false;

    public function getOkreg($id) {
        try {
            App::import('model','DB');
            $db = new DB();
            return $db->selectRow("
                SELECT
                  id,
                  rok,
                  nr_okregu,
                  AsText(polygon),
                  dzielnice,
                  ilosc_mieszkancow,
                  liczba_mandatow
                FROM
                  pl_gminy_krakow_okregi
                WHERE id = $id
            ");
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getOkregi() {
        try {
            App::import('model','DB');
            $db = new DB();
            return $db->selectRows("
                SELECT
                  id,
                  rok,
                  nr_okregu,
                  AsText(polygon),
                  dzielnice
                FROM
                  pl_gminy_krakow_okregi
            ");
        }
        catch (Exception $e) {
            return $e->getMessage();
        }
    }

}