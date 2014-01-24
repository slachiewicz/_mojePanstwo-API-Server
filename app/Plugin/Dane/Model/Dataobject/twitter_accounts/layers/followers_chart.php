<?php
$id = addslashes($id);
$chart = $this->DB->query("SELECT date, followers_count FROM twitter_accounts_history WHERE account_id = $id ORDER BY date ASC");
$start_date = $chart[0]['twitter_accounts_history']['date'];
$data = array();
foreach ($chart as $value) {
    array_push($data, $value['twitter_accounts_history']['followers_count']);
}
return array(
    'start_date' => $start_date,
    'data' => $data,
);
