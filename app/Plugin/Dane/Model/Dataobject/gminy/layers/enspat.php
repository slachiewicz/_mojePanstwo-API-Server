<?php

$data = array(
    'ADD' => array(),
    'DIFF' => array(),
);
$_data = $this->DB->query("SELECT id, mode, enspat FROM pl_gminy_spats WHERE gmina_id='$id'");
foreach ($_data as $d) {
    if ($d['pl_gminy_spats']['mode'] == 'ADD')
        $data['ADD'][] = $d['pl_gminy_spats']['enspat'];
    elseif ($d['mode'] == 'DIFF')
        $data['DIFF'][] = $d['pl_gminy_spats']['enspat'];
}

return $data;