<?

$q = "SELECT `twitter_urls`.`id`, `twitter_urls`.`url`, COUNT(*) as 'count'
	FROM `twitter_twitts` 
	JOIN `twitter_tweets_urls` ON `twitter_twitts`.`id` = `twitter_tweets_urls`.`tweet_id` 
	JOIN `twitter_urls` ON `twitter_urls`.`id` = `twitter_tweets_urls`.`url_id` 
	WHERE `twitter_twitts`.`twitter_account_id` = '" . addslashes($id) . "' AND TIMESTAMPDIFF(DAY, `twitter_twitts` .`created_at`, NOW())<=7 AND `twitter_twitts`.`akcept`='1' AND `twitter_twitts`.`deleted`='0'
	GROUP BY `twitter_urls`.`id` 
	ORDER BY count DESC, `twitter_twitts`.`twitt_id` DESC
	LIMIT 10";

$data = array();
$tags_stats = $this->DB->query($q);

foreach ($tags_stats as &$t)
    $data[] = array_merge($t[0], $t['twitter_urls']);

return array(
    'data' => $data,
);




