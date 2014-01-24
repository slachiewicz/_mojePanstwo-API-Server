<?

$q = "SELECT `twitter_tags`.`id`, `twitter_tags`.`tag`, COUNT(*) as 'count'
	FROM `twitter_twitts` 
	JOIN `twitter_twitts_tags` ON `twitter_twitts`.`id` = `twitter_twitts_tags`.`tweet_id` 
	JOIN `twitter_tags` ON `twitter_tags`.`id` = `twitter_twitts_tags`.`tag_id` 
	WHERE `twitter_twitts`.`twitter_account_id` = '" . addslashes($id) . "' AND TIMESTAMPDIFF(DAY, `twitter_twitts` .`created_at`, NOW())<=7 AND `twitter_twitts`.`akcept`='1' AND `twitter_twitts`.`deleted`='0'
	GROUP BY `twitter_tags`.`id` 
	ORDER BY count DESC, `twitter_twitts`.`twitt_id` DESC
	LIMIT 10";

$data = array();
$tags_stats = $this->DB->query($q);

foreach ($tags_stats as &$t)
    $data[] = array_merge($t[0], $t['twitter_tags']);

return array(
    'data' => $data,
);




