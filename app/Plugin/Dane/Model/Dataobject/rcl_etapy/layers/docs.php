<?php

$categories = array();

$katalogi_podrzedne = $this->DB->query("SELECT id, tytul FROM `rcl_katalogi` WHERE katalog_id='$id' AND docs_count>0");


foreach ($katalogi_podrzedne as $katalog) {

    $katalog = $katalog['rcl_katalogi'];

    $files = $this->DB->query("SELECT id, dokument_id, tytul as 'nazwa' FROM rcl_dokumenty as `files` WHERE katalog_id='" . $katalog['id'] . "'");

    $categories[] = array(
        'id' => $katalog['id'],
        'nazwa' => $katalog['tytul'],
        'files' => $files,
    );


}

$dokumenty_luzem = $this->DB->query("SELECT id, dokument_id, tytul as 'nazwa' FROM rcl_dokumenty as `files` WHERE katalog_id='" . $id . "'");

if ($dokumenty_luzem)
    $categories[] = array(
        'nazwa' => '',
        'files' => $dokumenty_luzem,
    );


return $categories;