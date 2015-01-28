<?

$path = App::path('Vendor');
require_once( $path[0] . 'predis/predis/autoload.php' );

class getObjectByDataset extends \Predis\Command\ScriptCommand
{
    public function getKeysCount()
    {
        // Tell Predis to use all the arguments but the last one as arguments
        // for KEYS. The last one will be used to populate ARGV.
        return 1;
    }

    public function getScript()
    {
        return
			"
			local id = redis.call('get', KEYS[1])
			if id then
				return redis.call('get', 'data/objects/' .. id)
			else return 0
			end
			";
    }
}

class MPCacheSource extends DataSource {


    public $description = 'MPCache';
	public $API = false;
	private $scripts = array();

    public function __construct($config) {
        
        parent::__construct($config);
        $this->API = new Predis\Client(array(
		    'scheme' =>	$config['scheme'],
		    'host'   =>	$config['host'],
		    'port'   =>	$config['port'],
		));
				
    }	
	
	public function get($key) {
		return $this->API->get( $key );
	}

    public function getRedisClient() {
        return $this->API;
    }
	
	public function scriptQuery($script, $arguments) {
				
		if( !in_array($script, $this->scripts) )
			$this->API->getProfile()->defineCommand($script, $script);
		
		switch( $script ) {
			case "getObjectByDataset": {
				return $this->API->getObjectByDataset('data/datasets/' . $arguments[0] . '/' . $arguments[1]);
			}
		}
				
	}
	
}