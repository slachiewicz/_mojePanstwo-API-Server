<?php

class UserAdditionalData extends PaszportAppModel
{
    public $useDbConfig = 'default';
    public $useTable = 'm_users';
    public $hasAndBelongsToMany = array(
        'Stream' => array(
            'className' => 'Dane.Stream',
            'joinTable' => 'm_users-streams',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'stream_id',
        ),
    );

    public function hasPermissionToStream($stream_id)
    {
        $this->data = $this->find('first', array('conditions' => array('UserAdditionalData.id' => Configure::read('User.id'))));

        // if 1
        if ($stream_id == 1) {
            return true;
        }

        // if admin then has all access
        if ($this->data['UserAdditionalData']['group'] == 2) {
            return true;
        }
        // if
        foreach ($this->data['Stream'] as $stream) {
            if ($stream_id == $stream['id']) {
                return true;
            }
        }

        return false;
    }


    public function getAvailableDatasets($stream_id = null)
    {
    
    	App::import('model', 'MPCache');
        $this->MPCache = new MPCache();
    	
    	return $this->MPCache->getAvailableDatasets();
        
    }

    protected function normalizeDatasetsArray(&$input = array())
    {
        $opt = array();
        foreach ($input as $dataset) {
            array_push($opt, $dataset['base_alias']);
        }
        $input = $opt;
    }

} 