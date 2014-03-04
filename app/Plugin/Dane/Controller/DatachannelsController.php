<?

class DatachannelsController extends AppController
{
    public $uses = array('Dane.Datachannel', 'Dane.Dataset', 'Dane.Dataobject', 'Dane.Stream', 'Paszport.UserAdditionalData');

    public function info()
    {

        $alias = @addslashes($this->request->params['alias']);

        $datachannel = $this->Datachannel->find('first', array(
                'conditions' => array(
                    'Datachannel.slug' => $alias,
                ),
            )
        );

        $this->set('datachannel', $datachannel);
        $this->set('_serialize', array('datachannel'));
    }

    public function index()
    {
        
        $datachannels_conditions = array();
        $channel_id = isset( $this->request->query['channel_id'] ) ? $this->request->query['channel_id'] : false;
        if( $channel_id )
        	$datachannels_conditions['id'] = $channel_id;
        
        $noCache = isset( $this->request->query['nocache'] ) ? (boolean) $this->request->query['nocache'] : false;
        $source = $noCache ? 'db' : 'cache';

        $conditions = isset( $this->request->query['conditions'] ) ? $this->request->query['conditions'] : array();
		
		if( !empty($conditions) )
			$source = 'db';
        
        
        
        
        
        if( $source=='cache' )
        {
	        
	        $datachannels = $this->Datachannel->query("SELECT `data` FROM `datachannels` WHERE `data`!='' ORDER BY `ord` ASC LIMIT 100");
	        	        	        
	        foreach( $datachannels as &$d )
	        	$d = unserialize( stripslashes( $d['datachannels']['data'] ) );
	        	        
	        	        
        }
        else
        {
        
        	 $datachannels_query = array(
	        	'contain' => array(
	            	'Dataset' => array(
	                	'fields' => array('id', 'alias', 'count', 'name', 'class'),
	                ),
	                'Dataset.Stream',
	            ),
                'fields' => array(
	                'Datachannel.id',
	                'Datachannel.name',
	                'Datachannel.slug',
	            ),
	        );
	        
	        if( !empty($datachannels_conditions) )
	        	$datachannels_query['conditions'] = $datachannels_conditions;	        
        	
        	$datachannels = $this->Datachannel->find('all', $datachannels_query);
        	
	        // var_export( $datachannels ); die();
	        
	        // if (!$this->UserAdditionalData->hasPermissionToStream($this->stream_id)) {
	
	        // }
	        
	        
	        foreach ($datachannels as $dkey => &$datachannel)
	        {
	            foreach ($datachannel['Dataset'] as $key => $dataset)
	            {
	                $found = false;
	                foreach ($dataset['Stream'] as $stream) {
	                    if ($stream['id'] == $this->stream_id) {
	                        $found = true;
	                    }
	                }
	                if (!$found) {
	                    unset($datachannel['Dataset'][$key]);
	                }
	            }
	            
	            if (count($datachannel['Dataset']) < 1)
	                unset($datachannels[$dkey]);
	           	
	            if( isset($this->request->query['includeContent']) && $this->request->query['includeContent'] )
	            {
														
					$queryData = array(
						'conditions' => array(
							'datachannel' => $datachannel['Datachannel']['slug'],
						),
						'facets' => true,
						'limit' => 12,
					);
									
					if( isset($conditions['q']) && $conditions['q'] )
						$queryData['conditions']['q'] = $conditions['q'];
					
			        $search = $this->Dataobject->find('all', $queryData);		        
			        
			        $datachannel = array_merge($datachannel, array(
			        	'dataobjects' => isset($search['dataobjects']) ? $search['dataobjects'] : array(),
			        	'facets' => isset($search['facets']) ? $search['facets'] : array(),
			        ));
			        	            
	            }
	
	        }
        
        }
        
        $this->set('datachannels', $datachannels);
        $this->set('_serialize', array('datachannels'));
    }

    public function search()
    {

        $alias = @addslashes($this->request->params['alias']);

        $queryData = $this->request->query;
        $queryData['conditions']['datachannel'] = $alias;

        if (isset($queryData['q'])) {
            $queryData['conditions']['q'] = $queryData['q'];
        }

        $search = $this->Dataobject->find('all', $queryData);

        $this->set('search', $search);
        $this->set('_serialize', array('search'));
    }

}