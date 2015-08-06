<?php

class KrakowOkregi extends AppModel {

    public $useTable = false;

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