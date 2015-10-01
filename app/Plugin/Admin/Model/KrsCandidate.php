<?php

class KrsCandidate extends AppModel
{

    public $useTable = 'krs_kandydaci';

    public function all($args)
    {
        App::import('model', 'DB');
        $DB = new DB();

        $list_id = $DB->selectAssocs("SELECT DISTINCT kandydat_id FROM krs_kandydaci WHERE typ='" . $args['type'] . "' AND correct='" . $args['stan'] . "' LIMIT " . ($args['page'] - 1) * 50 . "," . ($args['page']) * 50);

        $ids = '(';
        foreach ($list_id as $single_id) {
            $ids .= $single_id['kandydat_id'] . ',';
        }
        $ids = trim($ids, ",");
        $ids .= ')';

        if ($args['type'] == 'sejm') {
            $list_kand = $DB->selectAssocs("SELECT id, imiona, nazwisko, zawod,miejsce_zamieszkania, data_urodzenia FROM PKW_parlament_2015_kandydaci_sejm WHERE id IN $ids");
        } else {
            $list_kand = $DB->selectAssocs("SELECT * FROM PKW_parlament_2015_kandydaci_senat WHERE id IN $ids");
        }
        $ret = array();
        foreach ($list_kand as $kandydat) {
            $kandydat['krs'] = $DB->selectAssocs("SELECT krs_kandydaci.id as kandydowanie_id, krs_osoby.imiona,krs_osoby.nazwisko, krs_osoby.data_urodzenia, krs_osoby.str, krs_osoby.id FROM krs_osoby JOIN krs_kandydaci ON krs_osoby.id=krs_kandydaci.krs_id WHERE krs_kandydaci.correct='" . $args['stan'] . "' AND krs_kandydaci.kandydat_id='" . $kandydat['id'] . "'");
            $ret[] = $kandydat;
        }
        return $ret;
    }

    public function pageCount($args)
    {
        App::import('model', 'DB');
        $DB = new DB();
        $page_count = $DB->selectAssoc("SELECT COUNT(DISTINCT kandydat_id) FROM krs_kandydaci WHERE typ='" . $args['type'] . "' AND correct='" . $args['stan'] . "'");


        $page_count = ceil($page_count['COUNT(DISTINCT kandydat_id)'] / 50);

        return $page_count;
    }

    public function decide($args)
    {

        App::import('model', 'DB');
        $DB = new DB();

        $ret = false;

        if ($args['type'] == 'accept') {
            $DB->updateAssoc('krs_kandydaci', array('correct' => '4'), array('kandydat_id' => $args['kandydat_id']));
            $ret = $DB->updateAssoc('krs_kandydaci', array('correct' => '3'), $args['krs_kandydat_id']);
        }
        if ($args['type'] == 'remove') {
            $ret = $DB->updateAssoc('krs_kandydaci', array('correct' => '4'), $args['krs_kandydat_id']);
        }
        if ($args['type'] == 'reconsider') {
            $ret = $DB->updateAssoc('krs_kandydaci', array('correct' => '0'), array('kandydat_id' => $args['kandydat_id']));
        }
        if ($ret) {
            return $args['type'];
        } else {
            return false;
        }
    }

}