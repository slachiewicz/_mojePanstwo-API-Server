<?

class DB extends AppModel
{

    public $useTable = false;
	public $reserved_words = array('NOW()', 'NULL');
	public $DB = false;
	
    public function __construct($id = false, $table = null, $ds = null)
    {
        // parent::__construct($id, $table, $ds);
        
        $source = $this->getDataSource();        
        $this->DB = new mysqli($source->config['host'], $source->config['login'], $source->config['password'], $source->config['database']);        
        $this->DB->query('SET SESSION group_concat_max_len = 10240000');
        $this->DB->query('SET NAMES UTF8');
    }
	
	public function _getAffectedRows(){
		return $this->DB->affected_rows;
	}
	
	public function _getInsertId(){
		return $this->DB->insert_id;
	}
	
	public function q($q) {
		return $this->DB->query($q);
	}
	
    public function queryCount($q)
    {

        $rows = $this->DB->query($q);
        $count = $this->DB->query("SELECT FOUND_ROWS() as `count`");

        return array($rows, @$count[0][0]['count']);

    }
	
	public function autocommit($autocommit = true)
	{
		return $this->DB->autocommit($autocommit);
	}
	
    public function selectValues($q)
    {

		$output = array();
        $rows = $this->selectRows($q);
        
        if( !empty($rows) )
        	foreach( $rows as $row )
        		$output[] = @$row[0];
		
		return $output;
		
    }

    public function selectValue($q)
    {

        $output = $this->selectRow($q);
        
        if( empty($output) )
			return false;
		else
	        return @$output[0];

    }

	
	public function selectRows($q)
    {

        $output = array();
        $result = $this->DB->query($q);
		
		while ($row = $result->fetch_row())
	        $output[] = $row;
	    
	    $result->free();
        return $output;

    }

    public function selectRow($q)
    {

        $output = false;
        $result = $this->DB->query($q);
		
		while ($row = $result->fetch_row()) {
	        $output = $row;
	        break;
	    }
	    
	    $result->free();
        return $output;

    }
	
    public function selectAssocs($q)
    {

        $output = array();
        $result = $this->DB->query($q);
		
		while ($row = $result->fetch_assoc())
	        $output[] = $row;
	    
	    $result->free();
        return $output;

    }

    public function selectAssoc($q)
    {

        $output = false;
        $result = $this->DB->query($q);
		
		while ($row = $result->fetch_assoc()) {
	        $output = $row;
	        break;
	    }
	    
	    $result->free();
        return $output;

    }
    
    public function selectDictionary($q)
    {
	    
	    $output = array();
        $result = $this->DB->query($q);
		
		while ($row = $result->fetch_row())
	        $output[ $row[0] ] = $row[1];
	    
	    $result->free();
        return $output;
	    
    }
    
    public function insertIgnoreAssoc($table, $data){
		
		if( empty($table) || empty($data) )
			return false;      
		
		$keys = array_keys($data);
		$values = array_values($data);
		
		
		$_values = array();
		for( $i=0; $i<count($values); $i++ ) {
			$v = $values[$i];
			if ( in_array($v, $this->reserved_words) )
				$_values[] = $v;
			else
				$_values[] = "'" . $v . "'";
		}
		$values = $_values;
		
		
		$q = "INSERT IGNORE INTO `$table` (`".implode("`, `", $keys)."`) VALUES (".implode(", ", $values).")";
		$this->DB->query($q);
		$this->setInsertID( $this->DB->insert_id );
		
		return ($this->DB->affected_rows==1);
		
    }
    
    public function updateAssoc($table, $data, $where) {
    
		if( !is_array($data) )
			return false;
			
		if( empty($where) )
			return false;
		
		if( is_string($where) || is_numeric($where) ) {
			
			$where_k = 'id';
			$where_v = $where;
		
		} else 
			foreach( $where as $where_k => $where_v )
				break;
		
		$sets = array();
		
		foreach( $data as $k => $v ) {
			if($k!=$where_k) {
			
				if( in_array($v, $this->reserved_words) )
					$sets[] = "`$k`=$v";
				else 
					$sets[] = "`$k`='$v'";
			
			}
		}
		
		$q = "UPDATE `$table` SET ".implode(", ", $sets)." WHERE (`$where_k`='$where_v')";
		return $this->DB->query($q);
		
    }


}