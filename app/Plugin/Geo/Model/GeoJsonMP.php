<?php

App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));

class GeoJsonMP extends AppModel
{
    public $useTable = false;

    private static $dataOptions = array(
        'wojewodztwa' => array(
            'table' => 'pl_wojewodztwa_obszary',
            'id'    => 'wojewodztwo_id',
            'join'  => 'wojewodztwa'
        ),
        'powiaty' => array(
            'table' => 'pl_powiaty_obszary',
            'id'    => 'powiat_id',
            'join'  => 'pl_powiaty'
        ),
        'gminy' => array(
            'table' => 'pl_gminy_obszary',
            'id'    => 'gmina_id',
            'join'  => 'pl_gminy'
        ),
        'parl_okregi_sejm' => array(
            'table' => 'pl_parl_okregi_sejm_obszary',
            'id'    => 'okrag_id',
            'join'  => 'pkw_parl_okregi'
        )
    );

    private static function getOptions($typeName) {
        $options = array();
        foreach(self::$dataOptions as $name => $option)
            $options[$name] = $option[$typeName];
        return $options;
    }

    /**
     * @param int $quality jakość map
     * @param array $types typy (wojewodztwa, powiaty, gminy)
     * @param array $elements elementy do pobrania (dokładne id)
     * @return array dane map
     */
    public function getMapData($quality, $types, $elements)
    {
        App::import('model', 'DB');
        $this->DB = new DB();

        $polygonName = $this->getPolygonName($quality);

        $tables = self::getOptions('table');
        $idNames = self::getOptions('id');
        $joinTableNames = self::getOptions('join');

        $data = array();

        foreach($types as $type) {
            $table = $tables[$type];
            $joinTable = $joinTableNames[$type];
            $joinField = $idNames[$type];
            $ids = $elements[$type];
            $where = '';
            if(count($ids) > 0) {
                $where = 'WHERE `'.$idNames[$type].'` IN (';
                foreach($ids as $i => $id) {
                    $where .= $id;
                    if($i != count($ids) - 1)
                        $where .= ',';
                }
                $where .= ')';
            }
            $query = "SELECT sid, $joinField as id, $joinTable.nazwa, GROUP_CONCAT(AsText($table.$polygonName) SEPARATOR \"\n\") as 'wkts' FROM `$table` JOIN $joinTable ON $joinTable.id = $table.$joinField $where GROUP BY `sid` ORDER BY $joinField ASC";
            $results = $this->DB->selectAssocs($query);
            $data = array_merge($data, $results);
        }

        $features = array();
        foreach($data as $d) {

            $wkts = explode("\n", $d['wkts']);


            $geom = geoPHP::load($wkts[0], 'wkt');
            for ($i = 1; $i < count($wkts); $i++)
                $geom = $geom->union(geoPHP::load($wkts[$i], 'wkt'));

            $geojsonConverter = new GeoJSON();
            $geojson = $geojsonConverter->write($geom, true);

            $features[] = array(
                "type" => "Feature",
                "id" => $d['sid'],
                "properties" => array(
                    'name'  => $d['nazwa'],
                    'id'    => $d['id']
                ),
                "geometry" => $geojson
            );

        }

        $featc = array(
            "type" => "FeatureCollection",
            "features" => $features
        );

        MpUtils::geoStampCRS($featc);

        return $featc;
    }

    private function getPolygonName($quality) {
        $name = 'polygon';
        return $name.'_'.$quality;
    }

    function getLabel($params) {
        $option = self::$dataOptions[$params['type']];
        $id = (int) $params['id'];

        App::import('model', 'DB');
        $this->DB = new DB();
        $query = "SELECT nazwa FROM `".$option['join']."` WHERE `id` = $id";
        $label = $this->DB->selectValue($query);

        return $label;
    }
    
    function getData($simplify = true) {
	    	    
	    App::import('model', 'DB');
	    // App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));

	    $this->DB = new DB();
	    $data = $this->DB->selectAssocs("SELECT `sid`, GROUP_CONCAT(AsText(polygon_4) SEPARATOR \"\n\") as 'wkts' FROM `pl_gminy_obszary` GROUP BY `sid`");
	    	    
	    $features = array();
        foreach($data as $d) {
            
            $wkts = explode("\n", $d['wkts']);
            
		
		    $geom = geoPHP::load($wkts[0], 'wkt');
		    for ($i = 1; $i < count($wkts); $i++)
	            $geom = $geom->union(geoPHP::load($wkts[$i], 'wkt'));		       
				
		    if ($simplify) {
		        // w tej postaci bedzie to nierównomiernie robił w pioniie i poziomie ze względu na CRS (ale roznica wymiarow tylko 2x)
		        $geom = $geom->simplify(0.006, true);
		    }
		
		    $geojsonConverter = new GeoJSON();
		    $geojson = $geojsonConverter->write($geom, true);
		    
		    /// debug( $geojson ); die();		    
		    // MpUtils::transposeCoordinates($geojson);
		    // debug( $geojson ); die();
						
			$features[] = array(
			    "type" => "Feature",
			    "id" => $d['sid'],
			    "properties" => array(
			        /*
			        'gminy.nazwa' => $data['gminy.nazwa'],
			        'gminy.teryt' => $data['gminy.teryt'],
			        'gminy.typ_nazwa' => $data['gminy.typ_nazwa'],
			        'powiaty.id' => $data['powiaty.id'],
			        'powiaty.nazwa' => $data['powiaty.nazwa'],
			        'wojewodztwa.id' => $data['wojewodztwa.id'],
			        'wojewodztwa.nazwa' => $data['wojewodztwa.nazwa'],
			        */
			    ),
			    "geometry" => $geojson
			);
			
        }
				
        $featc = array(
            "type" => "FeatureCollection",
            "features" => $features
        );

        MpUtils::geoStampCRS($featc);
        
        return $featc;
	    
    }
} 