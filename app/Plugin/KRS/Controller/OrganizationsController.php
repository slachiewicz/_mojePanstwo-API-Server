<?php

class OrganizationsController extends AppController
{
    public function getFeaturedByGroups()
    {
	   	// NAJNOWSZE ORGANIZACJE
	   	
	   	$data = ClassRegistry::init('Dane.Dataobject')->find('all', array(
	   		'conditions' => array(
	   			'dataset' => 'krs_podmioty',
	   		),
	   		'order' => 'data_rejestracji desc',
	   		'limit' => 12,
	   	));
	   	
	   	$najnowsze_organizacje = array();
	   	if( isset($data['dataobjects']) )
	   	{
		    foreach( $data['dataobjects'] as $object )
		    {
			    $najnowsze_organizacje[] = array(
			    	'type' => 'organization',
			    	'id' => $object['data']['id'],
			    	'nazwa' => $object['data']['nazwa'],
			    	'data_rejestracji' => $object['data']['data_rejestracji'],
			    	'kapital_zakladowy' => $object['data']['wartosc_kapital_zakladowy'],
			    	'miejscowosc' => $object['data']['adres_miejscowosc'],
			    );
		    }
	   	}
	   	
	   		   	
	   	
	   	// NAJWIĘKSZE SPÓŁKI
	   	/*
	   	$data = ClassRegistry::init('Dane.Dataobject')->find('all', array(
	   		'conditions' => array(
	   			'dataset' => 'krs_podmioty',
	   		),
	   		'order' => 'wartosc_kapital_zakladowy desc',
	   		'limit' => 12,
	   	));
	   	
	   	$najwieksze_spolki = array();
	   	if( isset($data['dataobjects']) )
	   	{
		    foreach( $data['dataobjects'] as $object )
		    {
			    $najwieksze_spolki[] = array(
			    	'type' => 'organization',
			    	'id' => $object['data']['id'],
			    	'nazwa' => $object['data']['nazwa'],
			    	'data_rejestracji' => $object['data']['data_rejestracji'],
			    	'kapital_zakladowy' => $object['data']['wartosc_kapital_zakladowy'],
			    	'miejscowosc' => $object['data']['adres_miejscowosc'],
			    );
		    }
	   	}
	   	*/
	   	
	   	
	   	
	   		    
	    $groups = array(
	    	array(
	    		'id' => 'najnowsze_organizacje',
	    		'label' => 'Najnowsze organizacje',
	    		'content' => $najnowsze_organizacje,
	    	),
	    	/*
	    	array(
	    		'id' => 'najwieksze_spolki',
	    		'label' => 'Największe spółki',
	    		'content' => $najwieksze_spolki
	    	),
	    	*/
	    );
	    
	    $this->set('groups', $groups);
        $this->set('_serialize', array('groups'));
	    
    }
    
    public function getOrganizationIdBy()
    {
	 	$data = false;
	 	   
	    $dotacje_ue_beneficjent_id = isset($this->request->query['dotacje_ue_beneficjent_id']) ? 
	    	$this->request->query['dotacje_ue_beneficjent_id'] : 
	    	false;
	    
	    if( $dotacje_ue_beneficjent_id )
	    {
	    	
	    	$DB = $this->loadModel('DB');
		    $data = (int) $this->DB->selectValue("SELECT id
FROM `krs_pozycje`
WHERE `dotacje_ue_beneficjent_id` = '" . addslashes( $dotacje_ue_beneficjent_id ) . "'
LIMIT 1");

		}

	    $this->set('data', $data);
	    $this->set('_serialize', 'data');
	    
    }
} 