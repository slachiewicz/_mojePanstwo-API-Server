<?php

App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));

class GeoJsonMP extends AppModel
{
    public $useTable = false;

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

        $tables = array(
            'wojewodztwa'   => 'pl_wojewodztwa_obszary',
            'powiaty'       => 'pl_powiaty_obszary',
            'gminy'         => 'pl_gminy_obszary'
        );

        $idNames = array(
            'wojewodztwa'   => 'wojewodztwo_id',
            'powiaty'       => 'powiat_id',
            'gminy'         => 'gmina_id'
        );

        $joinTableNames = array(
            'wojewodztwa'   => 'wojewodztwa',
            'powiaty'       => 'pl_powiaty',
            'gminy'         => 'pl_gminy'
        );

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