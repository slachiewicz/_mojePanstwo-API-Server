<?

$docs = array();

$prawo = $this->DB->query("SELECT rcl_id, isap_id, src FROM prawo WHERE id='$id'");
$prawo = @$prawo[0]['prawo'];

if ($prawo['rcl_id']) {


    // ISAP
    $dokument_id = $this->DB->query("SELECT dokument_id FROM ISAP_pozycje as `files` WHERE id='" . $prawo['isap_id'] . "'");
    $dokument_id = @$dokument_id[0]['files']['dokument_id'];


    if ($dokument_id)
        $docs[] = array(
            'files' => array(
                'dokument_id' => $dokument_id,
                'nazwa' => 'Wersja sformatowana przez Kancelarię Sejmu',
            )
        );


    $table = false;
    if ($prawo['src'] == 'DzU')
        $data = array(
            'table' => 'DzU_pozycje',
            'label' => 'Dziennika Ustaw',
        );
    if ($prawo['src'] == 'MP')
        $data = array(
            'table' => 'MP_pozycje',
            'label' => 'Monitora Polskiego',
        );


    if ($data) {

        // RCL
        $dokument_id = $this->DB->query("SELECT dokument_id FROM `" . $data['table'] . "` as `files` WHERE id='" . $prawo['rcl_id'] . "'");
        $dokument_id = @$dokument_id[0]['files']['dokument_id'];

        if ($dokument_id)
            $docs[] = array(
                'files' => array(
                    'dokument_id' => $dokument_id,
                    'nazwa' => 'Wersja oryginalna z ' . $data['label'],
                ),
            );


    }

}


$categories = array();


$categories[] = array(
    'nazwa' => '',
    'files' => $docs,
);


return $categories;

?>