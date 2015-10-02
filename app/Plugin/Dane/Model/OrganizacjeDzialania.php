<?php

class OrganizacjeDzialania extends AppModel {

    public $useTable = 'organizacje_dzialania';
    public $uses = array('Dane.Dataobject');
    
    public function afterSave($created, $options = array()) {
	    	  
		if( $id = $this->data['OrganizacjeDzialania']['id'] ) {
            $this->sync($id);
/*
            $obj = $this->findById($id);
            $obj = $obj['OrganizacjeDzialania'];

            $data = array(

                'id' => $obj['id'],
                'dataset' => 'dzialania',
                'slug' => Inflector::slug($obj['tytul']),
                'text' => $obj['tytul'] . ' ' . $obj['opis'],
                'date' => $obj['cts'],

                'data' => array(
                    'dzialania.dataset' => $obj['owner_dataset'],
                    'dzialania.data_utworzenia' => $obj['cts'],
                    'dzialania.geo_lat' => $obj['geo_lat'],
                    'dzialania.geo_lng' => $obj['geo_lng'],
                    'dzialania.id' => $obj['id'],
                    'dzialania.object_id' => $obj['owner_object_id'],
                    'dzialania.opis' => $obj['opis'],
                    'dzialania.photo' => $obj['cover_photo'],
                    'dzialania.podsumowanie' => $obj['podsumowanie'],
                    'dzialania.status' => $obj['status'],
                    'dzialania.tytul' => $obj['tytul'],
                    'dzialania.user_id' => $obj['user_id'],
                    'dzialania.zakonczone' => $obj['zakonczone'],
                    'dzialania.owner_name' => $obj['owner_name'],
                )

            );

            $this->syncObject(
                'dzialania',
                199,
                $data
            );*/
        }
		    	    
    }
	
	public function sync($id) {
	
		if( $id ) {
	   	  
		    $this->query("INSERT IGNORE INTO `objects` (`dataset`, `dataset_id`, `object_id`) VALUES ('dzialania', '199', '" . addslashes( $id ) . "') ON DUPLICATE KEY UPDATE `a`='1', `a_ts` = NOW()");
			    
		    $this->objectIndex(array(
			    'dataset' => 'dzialania',
			    'object_id' => $id,
		    ));
	    
	    }
	
	}

}