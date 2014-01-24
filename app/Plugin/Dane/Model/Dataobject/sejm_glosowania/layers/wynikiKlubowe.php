<?

$output = array();

$kluby = $this->DB->query("SELECT s_glosowania_kluby.id, s_glosowania_kluby.klub_id, s_glosowania_kluby.wynik_id, s_glosowania_kluby.b, s_kluby.glosowania_skrot as 'klub_nazwa' FROM s_glosowania_kluby JOIN s_kluby ON s_glosowania_kluby.klub_id=s_kluby.id WHERE glosowanie_id='" . $id . "' AND s_glosowania_kluby.klub_id!='7'");

foreach ($kluby as $klub)
    $output[$klub['s_glosowania_kluby']['wynik_id']][] = array_merge($klub['s_glosowania_kluby'], $klub['s_kluby']);

return $output;