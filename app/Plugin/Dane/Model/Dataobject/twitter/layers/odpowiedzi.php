<?php
$odpowiedzi = $this->DB->query("SELECT * FROM twitter_twitts where in_reply_to_tweet_id = $id ORDER BY created_at ASC");
foreach ($odpowiedzi as &$tweet) {
    $tweet['twitter_twitts']['src'] = json_decode($tweet['twitter_twitts']['src'], true);
}
return $odpowiedzi;