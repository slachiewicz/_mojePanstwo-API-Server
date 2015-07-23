<?php

App::uses('AppModel', 'Model');
class WymiaryKombinacje extends AppModel {

    public $useTable = 'BDL_wymiary_kombinacje';
    public $actsAs = array('Containable');
    public $hasMany = array(
        'DataPl' => array(
            'className' => 'BDL.DataPl',
            'foreignKey' => 'kombinacja_id',
            'conditions' => array(
            	'DataPl.zero' => '0'
            ),
            'order' => 'DataPl.rocznik ASC',
        ),
    );

    public $hasAndBelongsToMany = array(
        'BdlTempItem' =>
            array(
                'className' => 'BDL.BdlTempItem',
                'joinTable' => 'BDL_kombiancje_user_item',
                'with' => 'BDL.BDL_kombiancje_user_item',
                'foreignKey' => 'dim_id',
                'associationForeignKey' => 'useritem_id',
            )
    );
    
    public function afterFind($results, $primary = false) {
	    
	    $output = array();
	    foreach( $results as $r ) {
		    
		    $o = array(
			    'id' => $r['WymiaryKombinacje']['id'],
			    'jednostka' => $r['WymiaryKombinacje']['jednostka'],
			    'ly' => (int) $r['WymiaryKombinacje']['ly'],
			    'lv' => (float) $r['WymiaryKombinacje']['lv'],
			    'ply' => (int) $r['WymiaryKombinacje']['ply'],
			    'dv' => (float) $r['WymiaryKombinacje']['dv'],
			    'dims' => array(
				    (int) (int) $r['WymiaryKombinacje']['w1'], 
				    (int) (int) $r['WymiaryKombinacje']['w2'], 
				    (int) (int) $r['WymiaryKombinacje']['w3'], 
				    (int) (int) $r['WymiaryKombinacje']['w4'], 
				    (int) (int) $r['WymiaryKombinacje']['w5']
			    ),
			    'years' => array(),
		    );
		    
		    if( isset( $r['DataPl'] ) )
			    foreach( $r['DataPl'] as $d )
			    	$o['years'][] = array(
				    	(int) $d['rocznik'], 
				    	(float) $d['v'], 
				    	$d['a'], 
			    	);
		    
		    $output[] = $o;
		    
	    }
	    
	    return $output;
	    
    }


    
}