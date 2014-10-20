<?php

class Keyword extends AppModel
{

    public function index()
    {
	    
	    App::import('model','DB');
		$this->DB = new DB();
		
	    // return $this->DB->selectAssocs("SELECT `id`, `q` FROM `ISAP_hasla` WHERE `expose`='1' ORDER BY `id` ASC LIMIT 100");
	    return $this->DB->selectAssocs("SELECT `id`, `q` FROM `ISAP_hasla` WHERE `akcept`='1' ORDER BY `data_ostatniego_aktu` DESC LIMIT 20");
	    
    }

} 