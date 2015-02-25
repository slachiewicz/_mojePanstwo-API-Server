<?
$id = (int) $id;

$posiedzenie_rady = $this->DB->selectAssoc("
    SELECT dzielnica_id, `data`
    FROM pl_gminy_krakow_dzielnice_rady_posiedzenia
    WHERE id = $id
");

if( $posiedzenie = $this->DB->selectAssoc("
    SELECT id
    FROM rady_dzielnice_posiedzenia
    WHERE `date` = '".$posiedzenie_rady['data']."' AND dzielnica_id = ".$posiedzenie_rady['dzielnica_id']."
") ) {

return $this->DB->selectAssocs("SELECT id, tytul as `mowca_str`, video_start FROM rady_dzielnice_posiedzenia_punkty WHERE posiedzenie_id = '".$posiedzenie['id']."' AND deleted='0'");

} else return false;