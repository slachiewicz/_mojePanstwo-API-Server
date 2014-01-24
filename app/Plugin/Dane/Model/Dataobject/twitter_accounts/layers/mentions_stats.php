<?

$q = "SELECT `twitter_mentions`.`id`, `twitter_mentions`.`twitter_name`, COUNT(*) as 'count'
	FROM `twitter_twitts` 
	JOIN `twitter_tweets_mentions` ON `twitter_twitts`.`id` = `twitter_tweets_mentions`.`tweet_id` 
	JOIN `twitter_mentions` ON `twitter_mentions`.`id` = `twitter_tweets_mentions`.`mention_id` 
	WHERE `twitter_twitts`.`twitter_account_id` = '" . addslashes($id) . "' AND TIMESTAMPDIFF(DAY, `twitter_twitts` .`created_at`, NOW())<=7 AND `twitter_twitts`.`akcept`='1' AND `twitter_twitts`.`deleted`='0'
	GROUP BY `twitter_mentions`.`id` 
	ORDER BY count DESC, `twitter_twitts`.`twitt_id` DESC
	LIMIT 10";

$data = array();
$tags_stats = $this->DB->query($q);

foreach ($tags_stats as &$t)
    $data[] = array_merge($t[0], $t['twitter_mentions']);

return array(
    'data' => $data,
);




