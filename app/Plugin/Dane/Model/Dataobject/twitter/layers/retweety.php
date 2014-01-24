<?php
$retweety = $this->DB->query("SELECT * FROM twitter_twitts where retweeted_id = $id ORDER BY created_at ASC");
foreach ($retweety as &$tweet) {
    $tweet['twitter_twitts']['src'] = json_decode($tweet['twitter_twitts']['src'], true);
}
return $retweety;