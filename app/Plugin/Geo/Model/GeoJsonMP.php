<?php

App::import('Vendor', 'geoPHP', array('file' => '/phayes/geophp/geoPHP.inc'));

class GeoJsonMP extends AppModel
{
    public $useTable = false;
    
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