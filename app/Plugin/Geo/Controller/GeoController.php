<?php

class GeoController extends AppController
{
    public $uses = array(
        'Geo.GeoGmina',
        'Geo.GeoPowiat',
        'Geo.GeoWojewodztwo',
        'Geo.GeoPowiatGrodzkiGminy',
    );

    public function resolve($lat, $lng)
    {
        $sql_point = "POINT($lat, $lng)";        
        $gminy = $this->GeoGmina->query("SELECT pl_gminy.id as 'gmina_id', pl_gminy.nazwa as 'gmina_nazwa', pl_gminy_typy.nazwa as 'gmina_typ', pl_gminy.pl_powiat_id as 'powiat_id', pl_gminy.w_id as 'wojewodztwo_id' FROM pl_gminy_spats JOIN pl_gminy ON pl_gminy_spats.gmina_id=pl_gminy.id JOIN pl_gminy_typy ON pl_gminy.typ_id=pl_gminy_typy.id WHERE (pl_gminy_spats.`mode`='ADD' AND Within($sql_point, pl_gminy_spats.spat)=1) AND pl_gminy_spats.id NOT IN (SELECT pl_gminy_spats.id FROM pl_gminy_spats WHERE pl_gminy_spats.`mode`='DIFF' AND Within($sql_point, pl_gminy_spats.spat)=1) GROUP BY pl_gminy_spats.gmina_id LIMIT 10");
        $this->set(array(
            'gminy' => $gminy,
            '_serialize' => array('gminy'),
        ));
    }

    public function pl() {
        $this->viewClass = 'Media';

        $params = array(
            'id'        => 'pl.json',
            'name'      => 'pl',
            'download'  => false,
            'extension' => 'json',
            'cache' => true,
            'path'      => APP . 'Plugin/Geo/webroot/'
        );

        $this->set($params);
    }
} 