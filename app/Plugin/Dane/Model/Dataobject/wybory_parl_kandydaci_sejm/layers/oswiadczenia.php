<?php

$oswiadczenia = $this->DB->selectAssocs("SELECT * FROM mpw_oswiadczenia WHERE kandydat_id='$id'");
$ret = array();
foreach ($oswiadczenia as $val) {
    $val['dane'] = array();

    $val['dane']['zasoby_pieniezne'] = $this->DB->selectAssocs("SELECT * FROM mpw_oswiadczenia_zasoby_pieniezne WHERE kandydat_id='$id' AND oswiadczenie_id='" . $val['id'] . "'");
    $val['dane']['nieruchomosci'] = $this->DB->selectAssocs("SELECT * FROM mpw_oswiadczenia_nieruchomosci WHERE kandydat_id='$id' AND oswiadczenie_id='" . $val['id'] . "'");
    $val['dane']['inne_dochody'] = $this->DB->selectAssocs("SELECT * FROM mpw_oswiadczenia_inne_dochody WHERE kandydat_id='$id' AND oswiadczenie_id='" . $val['id'] . "'");
    $val['dane']['mienie_ruchome'] = $this->DB->selectAssocs("SELECT * FROM mpw_oswiadczenia_mienie_ruchome WHERE kandydat_id='$id' AND oswiadczenie_id='" . $val['id'] . "'");
    $val['dane']['udzialy'] = $this->DB->selectAssocs("SELECT * FROM mpw_oswiadczenia_udzialy WHERE kandydat_id='$id' AND oswiadczenie_id='" . $val['id'] . "'");
    $val['dane']['akcje'] = $this->DB->selectAssocs("SELECT * FROM mpw_oswiadczenia_akcje WHERE kandydat_id='$id' AND oswiadczenie_id='" . $val['id'] . "'");

    $ret[] = $val;

}
return $ret;