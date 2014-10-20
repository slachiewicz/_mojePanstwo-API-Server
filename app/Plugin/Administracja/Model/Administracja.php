<?php

class Administracja extends AppModel
{

    public $useTable = false;
    
    public function getData()
    {
		
		App::import('model', 'DB');
        $this->DB = new DB();
        
        $output = array(
        	'files' => $this->DB->selectAssocs("SELECT `id`, `nazwa`, `childsCount`, `width`, `opis_html`, `budzet_plan` FROM `administracja_publiczna` WHERE `src_type_id`='1' AND `akcept`='1' AND `parent_id`='0' AND `id`!=2127 AND `id`!=578 AND `id`!=569 AND `id`!=3221 ORDER BY `nazwa` ASC"),        
        );
        
        /*
        	2127 - Inne instytucje realizujące zadania publiczne lub dysponujące majątkiem publicznym
        	578 - Samorząd terytorialny
        	3221 - Regionalne Izby Obrachunkowe
        	569 - Sądy i Trybunały
        */
        
        return $output;

    }

} 