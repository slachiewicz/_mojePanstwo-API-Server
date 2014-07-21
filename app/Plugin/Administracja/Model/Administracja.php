<?php

class Administracja extends AppModel
{

    public $useTable = false;
    
    public function getData()
    {
		
		App::import('model', 'DB');
        $this->DB = new DB();
        
        $output = array(
        	'files' => $this->DB->selectAssocs("SELECT `id`, `nazwa`, `childsCount`, `width` FROM `administracja_publiczna` WHERE `akcept`='1' AND `parent_id`='0' AND `id`!=2127 ORDER BY `nazwa` ASC"),        
        );
        
        return $output;

    }

} 