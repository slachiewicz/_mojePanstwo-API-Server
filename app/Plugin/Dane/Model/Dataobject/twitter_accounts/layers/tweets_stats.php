<?

$q = "SELECT DATE(created_at) as 'date', retweet as 'retweet', COUNT(*) as 'count'
	FROM `twitter_twitts` 
	WHERE `twitter_account_id` = '" . addslashes($id) . "' AND TIMESTAMPDIFF(DAY, created_at, NOW())<31 AND `twitter_twitts`.`akcept`='1' AND `twitter_twitts`.`deleted`='0'
	GROUP BY DATE(created_at), retweet 
	ORDER BY created_at ASC";

$data = array();
$tweets_stats = $this->DB->query($q);

foreach ($tweets_stats as &$t)
    $data[] = array_merge($t[0], $t['twitter_twitts']);

return array(
    'data' => $data,
);