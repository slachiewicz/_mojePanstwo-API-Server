<?

$data = $this->DB->selectAssocs("SELECT s_glosowania_kluby.id, s_glosowania_kluby.klub_id, s_glosowania_kluby.wynik_id, s_glosowania_kluby.b, s_glosowania_kluby.l, s_glosowania_kluby.g, s_glosowania_kluby.z, s_glosowania_kluby.p, s_glosowania_kluby.w, s_glosowania_kluby.n, s_kluby.glosowania_skrot as 'klub_nazwa' FROM s_glosowania_kluby JOIN s_kluby ON s_glosowania_kluby.klub_id=s_kluby.id WHERE glosowanie_id='" . $id . "' ORDER BY s_glosowania_kluby.l DESC");

return $data;