<?
$id = (int) $id;
return $this->DB->selectAssocs("SELECT id, tytul as `mowca_str`, video_start FROM rady_komisje_posiedzenia_debaty WHERE posiedzenie_id = $id AND deleted='0'");