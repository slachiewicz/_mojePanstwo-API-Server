<?php
$linie = $this->DB->query("select a.id, a.nr, b.przyjazd_str, b.przyjazd, b.odjazd, b.odjazd_str, c.nazwa, c.id as `id_stacji`, c.akcept from PKP_linie a, PKP_linie_stacje b, PKP_stacje c where b.stacja_id = $id and a.id = b.linia_id and c.id = a.stop_stacja_id order by c.nazwa ASC, b.przyjazd_str ASC, b.odjazd_str ASC");
return $linie;