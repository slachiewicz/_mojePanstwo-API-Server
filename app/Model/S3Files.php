<?

class S3Files extends AppModel {
    
    public $useDbConfig = 'S3';
    
    private function resolveFile($file) {
	    
	    $bucket = '';
	    $parts = explode('/', $file);
	    
	    if( count($parts)>1 ) {
		
			$bucket = array_shift($parts);
			$file = implode('/', $parts);   
		    
	    }
	    
	    return array($bucket, $file);
	    
    }
    
	public function getBody($file) {

		list($bucket, $file) = $this->resolveFile( $file );
		
		$res = @$this->find('first', array(
			'conditions' => array(
				'bucket' => $bucket,
				'file' => $file,
			),
		));
		
		if( 
			!empty($res) && 
			isset($res['S3Files']) && 
			($file = $res['S3Files']) && 
			property_exists($file, 'body') 
		) {
			
			return $file->body;
			
		} else return false;
		
	}
    
}
