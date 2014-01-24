<?
$obszary = $this->DB->query("SELECT pl_gminy.id as 'gmina_id', pl_gminy.nazwa as 'gmina_nazwa', GROUP_CONCAT(CONCAT(pna.id, \"\t\", pna.miejscowosc, \"\t\", pna.ulica, \"\t\", pna.numery) ORDER BY pna.miejscowosc ASC SEPARATOR \"\n\") AS 'pnas' FROM kody_pocztowe_pna JOIN pna ON kody_pocztowe_pna.pna_id=pna.id JOIN pl_gminy ON pna.gmina_id=pl_gminy.id WHERE kody_pocztowe_pna.kod_id='$id' AND pna.akcept='1' GROUP BY pna.gmina_id ORDER BY pl_gminy.nazwa ASC");
foreach ($obszary as &$o) {
    $pnas = explode("\n", $o[0]['pnas']);
    foreach ($pnas as &$p) {
        $parts = explode("\t", $p);
//        debug($parts);
        $p = array(
            'id' => $parts[0],
            'miejscowosc' => $parts[1],
            'ulica' => $parts[2],
            'numery' => isset($parts[3]) ? $parts[3] : null,
        );

    }
    $o['pnas'] = $pnas;
}
return $obszary;