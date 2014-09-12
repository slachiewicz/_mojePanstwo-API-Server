<?

return $this->DB->query("SELECT id, mowca_str, video_start FROM rady_posiedzenia_wystapienia WHERE debata_id='$id' AND deleted='0'");