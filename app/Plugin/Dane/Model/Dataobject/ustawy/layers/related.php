<?

$object = $this->getObject($dataset, $id);


$output = array(
    'groups' => array(),
);


$ustawa_id = $object['data']['id'];

$prawo_id = $this->DB->query("SELECT prawo_id FROM `prawo_ustawy_glowne` as `table` WHERE `id`='$ustawa_id'");
$prawo_id = @$prawo_id[0]['table']['prawo_id'];

$projekt_id = $this->DB->query("SELECT projekt_id FROM `prawo_ustawy` as `table` WHERE `id`='$prawo_id'");
$projekt_id = @$projekt_id[0]['table']['id'];


if ($projekt_id)
    $output['groups'][] = array(
        'id' => 'przebieg_prac',
        'title' => 'Przebieg prac nad projektem ustawy',
        'objects' => array(
            array(
                'dataset' => 'legislacja_projekty_ustaw',
                'object_id' => $projekt_id,
            )
        ),
    );


if ($prawo_id)
    $output['groups'][] = array(
        'id' => 'tekst_jednolity',
        'title' => 'Pierwsza wersja ustawy opublikowana w Dzienniku Ustaw',
        'objects' => array(
            array(
                'dataset' => 'ustawy',
                'object_id' => $prawo_id,
            )
        ),
    );


$powiazania = $this->DB->query("SELECT `prawo_isip_prawo`.`powiazanie_typ_id`, `prawo_isip_powiazania_typy`.`nazwa`, `prawo_isip_powiazania_typy`.`slug`, GROUP_CONCAT(`prawo_isip_prawo`.`prawo_child_id` ORDER BY `prawo_isip_prawo`.`prawo_child_id` DESC SEPARATOR ',') as 'ids'
	FROM `prawo_isip_prawo` 
	JOIN `prawo_isip_powiazania_typy` ON `prawo_isip_prawo`.`powiazanie_typ_id` = `prawo_isip_powiazania_typy`.`id` 
	WHERE `prawo_isip_prawo`.`prawo_id`='$prawo_id' AND `prawo_isip_prawo`.`akcept`='1' AND `prawo_isip_prawo`.`powiazanie_typ_id`!='2'
	GROUP BY `prawo_isip_prawo`.`powiazanie_typ_id` ORDER BY `prawo_isip_prawo`.`prawo_child_id` DESC");


foreach ($powiazania as $p) {

    $group = array(
        'id' => $p['prawo_isip_powiazania_typy']['slug'],
        'title' => $p['prawo_isip_powiazania_typy']['nazwa'],
        'objects' => array(),
    );

    $ids = explode(',', $p[0]['ids']);
    $ids = array_unique($ids);

    if (!empty($ids)) {

        foreach ($ids as $pid)
            $group['objects'][] = array(
                'dataset' => 'prawo',
                'object_id' => $pid,
            );

        $output['groups'][] = $group;

    }

}


/*
if( $objects )
        {

                $data = ClassRegistry::init('Dane.Dataobject')->find('all', array(
          'conditions' => array(
              'objects' => $objects,
          ),
      ));

                $output['groups'][] = array(
                        'id' => $p['prawo_isip_powiazania_typy']['slug'],
                        'title' => $p['prawo_isip_powiazania_typy']['nazwa'],
                        'objects' => $data['dataobjects'],
                );
        }

*/


return $output;






/*
$objects = array(
    'projekty' => array(),
    'punkty' => array(),
);






foreach( $this->DB->selectValues("SELECT DISTINCT(s_projekty_druki.projekt_id) FROM s_projekty_druki JOIN s_projekty ON s_projekty_druki.projekt_id=s_projekty.id WHERE s_projekty.typ_id='1' AND s_projekty.podrzedny='0' AND s_projekty_druki.druk_id='" . $druk_id . "'") as $projekt_id )
    $objects['projekty'][] = array(
        'object_class' => 'ep_Projekt_Ustawy',
        'object_id' => $projekt_id,
    );


foreach( $this->DB->selectValues("SELECT DISTINCT(punkt_id) FROM s_posiedzenia_punkty_druki WHERE druk_id='" . $druk_id . "'") as $punkt_id )
    $objects['punkty'][] = array(
        'object_class' => 'ep_Sejm_Posiedzenie_Punkt',
        'object_id' => $punkt_id,
    );






if( !empty($objects['projekty']) )
    $output['groups'][] = array(
        'id' => 'projekty',
        'title' => 'Projekty, których dotyczy ten druk',
        'objects' => $objects['projekty'],
    );


if( !empty($objects['punkty']) )
    $output['groups'][] = array(
        'id' => 'punkty',
        'title' => 'Powiązane punkty porządku dziennego',
        'objects' => $objects['punkty'],
    );



return $output;

*/