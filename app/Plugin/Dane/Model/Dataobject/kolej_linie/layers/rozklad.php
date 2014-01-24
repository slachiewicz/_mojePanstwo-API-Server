<?php
$stacje = $this->DB->query("select a.stacja, a.stacja_id, a.przyjazd_str, a.przyjazd, a.odjazd, a.odjazd_str, b.akcept, b.loc_lat, b.loc_lng from PKP_linie_stacje a, PKP_stacje b where a.linia_id = $id  and b.id = a.stacja_id order by a.stacja_order ASC;");
return $stacje;