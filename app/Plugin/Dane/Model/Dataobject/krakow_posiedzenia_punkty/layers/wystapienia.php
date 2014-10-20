<?

	return $this->DB->selectAssocs("SELECT id, mowca_str, video_start FROM rady_posiedzenia_wystapienia WHERE punkt_id='$id' AND deleted='0'");