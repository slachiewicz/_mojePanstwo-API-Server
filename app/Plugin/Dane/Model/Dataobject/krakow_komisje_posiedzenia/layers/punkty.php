<?
$id = (int) $id;
return $this->DB->selectAssocs("SELECT id, tytul as `mowca_str`, czas_start as `video_start` FROM rady_komisje_posiedzenia_debaty WHERE posiedzenie_id = $id AND deleted='0'");