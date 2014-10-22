<?php

class Prawo extends AppModel
{

    public function keywords()
    {
	    
	    App::import('model','DB');
		$this->DB = new DB();
		
	    // return $this->DB->selectAssocs("SELECT `id`, `q` FROM `ISAP_hasla` WHERE `expose`='1' ORDER BY `id` ASC LIMIT 100");
	    return $this->DB->selectAssocs("SELECT `id`, `q` FROM `ISAP_hasla` WHERE `akcept`='1' ORDER BY `data_ostatniego_aktu` DESC LIMIT 20");
	    
    }
    
    public function popular()
    {
	    
	    App::import('model','DB');
		$this->DB = new DB();
		
	    // return $this->DB->selectAssocs("SELECT `id`, `q` FROM `ISAP_hasla` WHERE `expose`='1' ORDER BY `id` ASC LIMIT 100");
	    $data = $this->DB->selectAssocs("SELECT `id`, `typ_nazwa` as `prefix`, `m_tytul` as `nazwa` FROM `prawo` WHERE `akcept`='1' AND popularne IS NOT NULL ORDER BY `popularne` ASC LIMIT 100");
	    
	    foreach( $data as &$d )
			if(
				!(
					( stripos($d['nazwa'], 'Kodeks')===0 ) || 
					( stripos($d['nazwa'], 'Konstytucja')===0 ) 
				)
			) 
			    $d['nazwa'] = $d['prefix'] . ' ' . $d['nazwa'];
	    
	    
	    return $data;
	    
    }
	
	public function types()
	{
		
			App::import('model', 'MPCache');
		    $MPCache = new MPCache();
		    
		    $data = $MPCache->getDataSource()->get('prawo/stats.json');
		    
			return json_decode($data, true);    

		
	}
	
} 