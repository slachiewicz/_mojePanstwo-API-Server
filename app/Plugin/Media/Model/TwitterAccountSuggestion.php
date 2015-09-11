<?php

class TwitterAccountSuggestion extends AppModel {

    public $useTable = 'twitter_account_suggestions';
    private $types = array(2,3,7,8,9,10);

    public function suggest($data) {
        $required = array('user_id', 'type_id', 'name');
        $row = array();

        foreach($required as $key)
            if(!isset($data[$key]))
                throw new Exception('Pole ' . $key . ' jest wymagane');
            else $row[$key] = $data[$key];

        if(!in_array($row['type_id'], $this->types))
            throw new Exception('Nieprawidłowy typ konta');

        $data['name'] = trim($data['name']);
        if(!strlen($data['name']))
            throw new Exception('Nazwa konta nie może być pusta');

        if(preg_match("|https?://(www\.)?twitter\.com/(#!/)?@?([^/]*)|", $data['name'], $matches)) {
            $row['name'] = $matches[3];
        } else
            throw new Exception('Nieprawidłowy link do profilu');

        return $this->save($row);
    }

}