<?php

class ZamowieniaPubliczne extends AppModel
{

    public $useTable = false;

    public function getStats()
    {

        $counts = Cache::read('zamowienia_publiczne:getStats', 'ultrashort');
        if (!$counts) {

            $data = $this->query("SELECT `data_publikacji`, COUNT(*) as `count` 
		    FROM `uzp_dokumenty` 
			WHERE akcept='1' 
			GROUP BY `data_publikacji` 
			ORDER BY `data_publikacji` DESC 
			LIMIT 150");

            $counts = array();
            for ($i = count($data) - 1; $i--; $i >= 0)
                $counts[] = array(strtotime($data[$i]['uzp_dokumenty']['data_publikacji']) * 1000, (int)$data[$i][0]['count']);

            Cache::write('zamowienia_publiczne:getStats', $counts, 'ultrashort');

        }

        return array(
            'days' => $counts,
        );

    }
    
    public function getNewStats($range = 'month')
    {
	    $_allowed_ranges = array('week', 'month', 'year', '3years', '5years');
	    if( !in_array($range, $_allowed_ranges) )
	    	return false;
	    
	    App::import('model', 'DB');
        $this->DB = new DB();
	    
	    $data = $this->DB->selectValue("SELECT `data` FROM `uzp_stats` WHERE `id`='" . addslashes( $range ) . "'");
	    if( !empty($data) && ( $data = unserialize(stripslashes($data)) ) ) {
		    
		    return $data;
		    
	    } else return false;
	    		    
    }

} 