<?

class BdlController extends AppController
{

    public function dataForDimmesions()
    {

        $data = array();
        $dims = isset( $this->request->query['dims'] ) ? $this->request->query['dims'] : array();
        
        App::import('model', 'DB');
        $this->DB = new DB();
        
        foreach( $dims as $dim )
        {
	        
	        $db_params = array();
	        for( $i=0; $i<5; $i++ )
	        	$db_params[ $i ] = isset( $dim[ $i ] ) ? $dim[ $i ] : 0;
	        
	        $db_data = $this->DB->selectAssoc("SELECT id, jednostka, ly, lv, ply, dv FROM `BDL_wymiary_kombinacje` WHERE `w1` = '" . addslashes( $db_params[0] ) . "' AND `w2` = '" . addslashes( $db_params[1] ) . "' AND `w3` = '" . addslashes( $db_params[2] ) . "' AND `w4` = '" . addslashes( $db_params[3] ) . "' AND `w5` = '" . addslashes( $db_params[4] ) . "' LIMIT 1");	        	
	        	
	        $data[] = array_merge($db_data, array(
	        	'dim_str' => implode(',', $db_params),
	        ));
	        		        
        }
        
        $this->set('data', $data);
        $this->set('_serialize', array('data'));

    }
    
    public function chartDataForDimmesions()
    {

        $data = array();
        $dim_ids = isset( $this->request->query['dims'] ) ? $this->request->query['dims'] : array();
                
        App::import('model', 'DB');
        $this->DB = new DB();
        
        
        
        $db_data = $this->DB->selectAssocs("SELECT `kombinacja_id` as 'dim_id', `rocznik` as 'y', `v`, `a` FROM `BDL_data_pl` WHERE (`kombinacja_id`='" . implode("' OR `kombinacja_id`='", $dim_ids) . "') AND `deleted`='0' AND `zero`='0' ORDER BY kombinacja_id ASC, rocznik ASC");
        
        $temp = array();
        foreach( $db_data as $d )
		{
			$dim_id = $d['dim_id'];
			unset( $d['dim_id'] );
			$temp[ $dim_id ][] = $d;
		}
        
        if( !empty($temp) )
        	foreach( $temp as $dim_id => $dims_data )
        		$data[] = array(
        			'id' => $dim_id,
        			'data' => $dims_data,
        		);        
        
        
        
        $this->set('data', $data);
        $this->set('_serialize', array('data'));

    }
    

}