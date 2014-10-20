<?php

class Type extends AppModel
{

    public function index()
    {
	    
	    App::import('model','DB');
		$this->DB = new DB();
		
	    return $this->DB->selectAssocs("SELECT `id`, `filtr_nazwa` as 'nazwa' FROM `prawo_typy` ORDER BY `id` ASC LIMIT 100");
	    
    }

} 