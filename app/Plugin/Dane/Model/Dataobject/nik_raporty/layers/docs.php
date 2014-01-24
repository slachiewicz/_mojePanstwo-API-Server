<?


$categories = array();

$q = "SELECT id, dokument_id, nazwa FROM `nik_raporty_pdf` as `files` WHERE raport_id='$id' AND deleted='0'";
$files = $this->DB->query($q);

if ($files)
    $categories[] = array(
        'id' => '1',
        'nazwa' => 'Pliki',
        'files' => $files,
    );


$q = "SELECT id, dokument_id, nazwa FROM `nik_raporty_podmioty_kontrolowane_pdf` as `files` WHERE raport_id='$id'";
$files = $this->DB->query($q);

if ($files)
    $categories[] = array(
        'id' => '1',
        'nazwa' => 'Podmioty kontrolowane',
        'files' => $files,
    );


/*
$dokumenty_luzem = $this->DB->query("SELECT id, dokument_id, tytul as 'nazwa' FROM rcl_dokumenty as `files` WHERE katalog_id='" . $id . "'");

if( $dokumenty_luzem )
    $categories[] = array(
      'nazwa' => '',
      'files' => $dokumenty_luzem,
    );
*/


return $categories;