<?php

class PanstwoInternet extends AppModel
{

    public $useTable = false;

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
                'search' => ClassRegistry::init('Dane.Dataset')->search('twitter', array(
                        'conditions' => array(
                            'twitter_accounts.typ_id' => $t,
                            '!bez_retweetow' => '1',
                            '_date' => 'LAST_' . $range,
                        ),
                        'order' => $order,
                        'limit' => 3,
                    )),
            );

        }

        return $types;

    }

} 