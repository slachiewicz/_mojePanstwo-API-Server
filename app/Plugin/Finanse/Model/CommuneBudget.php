<?php

class CommuneBudget extends AppModel {

    public $useTable = false;

    /* @var DB */
    private $db;
    private $prefix = 'mf_';

    public function __construct() {
        parent::__construct();
        App::import('model','DB');
        $this->db = new DB();
    }

    public function getSections($communeId, $type, $range) {
        $year = $range['year'];
        $quarters = $range['quarters'];
        $table = 'mf_' . $type;

        return array();
    }

    public function getSection($id, $communeId, $type, $range) {

        return array();
    }

}