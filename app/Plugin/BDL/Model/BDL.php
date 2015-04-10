<?php

App::uses('AppModel', 'Model');
class BDL extends AppModel {

    public $useTable = false;
    
    public function getTree(){
	    
	    App::import('model', 'DB');
        $this->DB = new DB();
	    
	    $data = $this->DB->selectAssocs("SELECT 
			`BDL_kategorie`.`id` as 'kategoria.id', 
			`BDL_kategorie`.`tytul` as 'kategoria.tytul',
			`BDL_grupy`.`id` as 'grupa.id', 
			`BDL_grupy`.`tytul` as 'grupa.tytul',
			`BDL_podgrupy`.`id` as 'podgrupa.id', 
			`BDL_podgrupy`.`tytul` as 'podgrupa.tytul' 
			FROM `BDL_kategorie` 
			JOIN `BDL_grupy` ON `BDL_grupy`.`kat_id` = `BDL_kategorie`.`id` 
			JOIN `BDL_podgrupy` ON `BDL_podgrupy`.`grupa_id` = `BDL_grupy`.`id` AND `BDL_podgrupy`.`akcept` = '1'
			WHERE `BDL_kategorie`.`okres` = 'R'");
	    
	    $kategorie = array();
	    
	    foreach( $data as $d ) {
		    
		    $kategorie[ $d['kategoria.id'] ]['dane']['tytul'] = $d['kategoria.tytul'];
		    $kategorie[ $d['kategoria.id'] ]['grupy'][ $d['grupa.id'] ]['dane']['tytul'] = $d['grupa.tytul'];
		    $kategorie[ $d['kategoria.id'] ]['grupy'][ $d['grupa.id'] ]['podgrupy'][ $d['podgrupa.id'] ]['dane']['tytul'] = $d['podgrupa.tytul'];
		    
	    }
	    	    
	    return array(
		    'kategorie' => $kategorie,
	    );
	    
    }
    
}