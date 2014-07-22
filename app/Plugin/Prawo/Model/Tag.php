<?php

class Tag extends AppModel
{

    public function getExposed()
    {
	    
	    App::import('model','DB');
		$this->DB = new DB();
		
	    return $this->DB->selectAssocs("SELECT `id`, `q`, `liczba_aktow`, `liczba_hasel` FROM `ISAP_hasla` WHERE `akcept`='1' AND `expose`='1' ORDER BY `q` ASC LIMIT 100");
	    
    }

} 