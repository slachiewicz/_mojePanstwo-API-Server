<?

$object = $this->getObject($dataset, $id);


$output = array(
    'groups' => array(),
);

$objects = array(
    'rcl' => array(),
    'sejm' => array(),
    'prawo' => array(),
);

$projekt_id = $object['id'];


foreach ($this->DB->selectValues("SELECT id FROM rcl_katalogi WHERE akcept='1' AND katalog_id=0 AND s_projekt_id='" . $projekt_id . "' ORDER BY `data` ASC") as $pid)
    $objects['rcl'][] = array(
        'dataset' => 'rcl_etapy',
        'object_template' => 'projekt',
        'object_id' => $pid,
    );


foreach ($this->DB->selectValues("SELECT id FROM prawo_ustawy WHERE projekt_id='" . $projekt_id . "'") as $pid)
    $objects['prawo'][] = array(
        'dataset' => 'prawo',
        'object_id' => $pid,
        'object_template' => 'projekt',
    );


$q = "SELECT s_projekty_tablice_.id, s_projekty_tablice_.typ_id, s_projekty_tablice_.c_id FROM s_projekty_tablice_ LEFT JOIN s_projekty_tablice_ntypy ON s_projekty_tablice_.ntyp_id=s_projekty_tablice_ntypy.id WHERE s_projekty_tablice_.deleted='0' AND s_projekty_tablice_.projekt_id='" . $projekt_id . "' ORDER BY s_projekty_tablice_.`date` ASC, s_projekty_tablice_ntypy.ord ASC";
foreach ($this->DB->selectAssocs($q) as $item) {

    if ($item['typ_id'] == '1' || $item['typ_id'] == '9')
        $objects['sejm'][] = array(
            'dataset' => 'sejm_druki',
            'object_template' => 'projekt',
            'object_id' => $item['c_id'],
        );
    elseif ($item['typ_id'] == '2')
        $objects['sejm'][] = array(
            'dataset' => 'sejm_posiedzenia_punkty',
            'object_template' => 'projekt',
            'object_id' => $item['c_id'],
        );
    elseif ($item['typ_id'] == '10')
        $objects['sejm'][] = array(
            'dataset' => 'sejm_glosowania',
            'object_template' => 'projekt',
            'object_id' => $item['c_id'],
        );
    elseif ($item['typ_id'] == '4')
        $objects['sejm'][] = array(
            'dataset' => 'sejm_zamrazarka',
            'object_template' => 'projekt',
            'object_id' => $item['c_id'],
        );


}


if (!empty($objects['rcl']))
    $output['groups'][] = array(
        'id' => 'rcl',
        'title' => 'Prace w rządzie',
        'objects' => $objects['rcl'],
    );

if (!empty($objects['sejm']))
    $output['groups'][] = array(
        'id' => 'sejm',
        'title' => 'Prace w Sejmie',
        'objects' => $objects['sejm'],
    );

if (!empty($objects['prawo']))
    $output['groups'][] = array(
        'id' => 'prawo',
        'title' => 'Opublikowana ustawa',
        'objects' => $objects['prawo'],
    );


return $output;
	
	
	
	
	
	
	
	
	
	
	
	