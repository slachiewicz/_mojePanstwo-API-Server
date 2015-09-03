<?php

class PanstwoInternet extends AppModel
{

    public $useTable = false;
	
	public function getAccountsPropositions()
	{
		
		App::import('model', 'MPCache');
        $this->MPCache = new MPCache();
        
        if(
	        ( $data = $this->MPCache->get('admin/media/twitter/new_accounts') ) && 
	        ( $data = json_decode($data, true) )
        )
        	return $data;
        else
			return false;
		
	}
	
	public function manage_account($data)
	{
		
		$map = array(
			'Komentatorzy' => '2',
	        'Urzędy' => '3',
	        'Media' => '6',
	        'Politycy' => '7',
	        'Partie' => '8',
	        'NGO' => '9',
        );
        
        if(
	        isset( $data['id'] ) &&
	        isset( $data['add'] ) && 
	        array_key_exists($data['add'], $map)
        ) {
		
			App::import('model', 'DB');
	        $this->DB = new DB();
	        
	        $params = array(
		        'typ_id' => $map[ $data['add'] ],
		        'twitter_id' => $data['id'],
		        'track' => '1',
		        'status' => '0',
		        'status_ts' => 'NOW()',
	        );
	        
	        $account = $this->DB->selectValue("SELECT id FROM twitter_accounts WHERE twitter_id='" . $params['twitter_id'] . "'");
	        
	        if( $account ) {
		        
		        return array(
			        'msg' => 'Account already exists',
		        );
		        
	        } else {
		        
		        $this->DB->insertIgnoreAssoc('twitter_accounts', $params);
		        return array(
			        'msg' => 'OK',
		        );
		        
	        }
	        
	        return array(
		        'msg' => 'Unkown error',
	        );
        
        }
		
	}
	
    public function get_annual_twitter_stats($year)
    {

        $file = 'http://admin.sejmometr.pl/_resources/twitter/stats/2013.json';
        $data = @json_decode(file_get_contents($file), true);
        return $data;

    }
    
    public function get_twitter_stats($range)
    {
				
		App::import('model', 'DB');
        $this->DB = new DB();
        
        $data = $this->DB->selectValue("SELECT `data` FROM `twitter_stats_es` WHERE `id`='" . addslashes( $range ) . "'");
        if( $data && ($data = unserialize($data)) ) {
						
	        return $data;	        
	        
        } return false;

    }

    public function twitter_accounts_types()
    {

        $result = $this->query("SELECT `id`, `nazwa`, `class` FROM `twitter_accounts_types` WHERE `ranking_new`='1' ORDER BY `ranking_ord` ASC");
        foreach ($result as &$r)
            $r = $r['twitter_accounts_types'];

        return $result;

    }

    public function twitter_accounts_group_by_types($range_id, $types, $order)
    {
		
		$hour = (int) date('G');
		
		if( $hour<7 )
			$date = date('Y-m-d', strtotime('-1 day', time()));
		else
			$date = date('Y-m-d');
			
		
		
		App::import('model', 'DB');
        $this->DB = new DB();
        
		$range_keys = array('24h', '3d', '7d', '1m', '1y');
		
		
		if( !in_array($range_id, $range_keys) )
			$range_id = $range_keys[0];
		
        foreach ($types as &$t) {
			
			$data = array();
			
			if( $order=='followers' ) {
				
				
				
				$fields = array('twitter_accounts`.`id', 'twitter_accounts`.`name', 'twitter_accounts`.`profile_image_url', 'twitter_accounts`.`followers_count');
				$fields[] = 'twitter_accounts_followers_counts`.`followers_delta_' . $range_id;
				$fields[] = 'twitter_accounts_followers_counts`.`followers_add_' . $range_id;
				$fields[] = 'twitter_accounts_followers_counts`.`followers_diff_' . $range_id;
				
				$_order = 'twitter_accounts_followers_counts`.`followers_delta_' . $range_id;
				
				$q = "SELECT `" . implode("`, `", $fields) ."` 
				FROM `twitter_accounts_followers_counts` 
				JOIN `twitter_accounts` ON `twitter_accounts_followers_counts`.`account_id` = `twitter_accounts`.`id` 
				WHERE `twitter_accounts`.`typ_id`='" . addslashes( $t ) . "' AND 
				`twitter_accounts_followers_counts`.`date` = '" . $date . "' AND 
				`twitter_accounts_followers_counts`.`followers_" . $range_id ."` = '1' 
				ORDER BY `" . $_order . "` DESC 
				LIMIT 3";
								
				$data = $this->DB->selectAssocs($q);
				
				
				
			} elseif( $order=='defollowers' ) {
				
				
				
				$fields = array('id', 'name', 'followers_date', 'profile_image_url', 'followers_count');
				$fields[] = 'followers_delta_' . $range_id;
				$fields[] = 'followers_add_' . $range_id;
				$fields[] = 'followers_diff_' . $range_id;
				$fields[] = 'followers_' . $range_id;
				
				$_order = 'followers_delta_' . $range_id;
				
				$q = "SELECT `" . implode("`, `", $fields) ."` FROM `twitter_accounts` WHERE `typ_id`='" . addslashes( $t ) . "' ORDER BY `" . $_order . "` ASC LIMIT 3";
				$data = $this->DB->selectAssocs($q);
				
				
				
			}
			
			
            $t = array(
                'id' => $t,
                'search' => $data,
            );

        }
		
		
        return array(
        	'date' => date('Y-m-d', strtotime('-1 day', strtotime($date))),
        	'types' => $types,
        );

    }

    public function get_twitter_tweets_group_by_types($range, $types, $order)
    {
				
		$range_keys = array('24h', '3d', '7d', '1m', '1y');
				
		if( !in_array($range, $range_keys) )
			$range = $range_keys[0];
			
		
		
        foreach ($types as &$t) {

            $t = array(
                'id' => $t,
                'search' => ClassRegistry::init('Dane.Dataobject')->find('all', array(
                    'conditions' => array(
	                    'dataset' => 'twitter',
                        'twitter_accounts.typ_id' => $t,
                        'twitter.retweet' => '0',
                        '_date' => 'LAST_' . $range,
                    ),
                    'order' => array('twitter.' . $order),
                    'limit' => 3,
                )),
            );

        }

        return $types;

    }

} 