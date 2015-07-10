<?php

class PageRequest extends AppModel {

    public $useTable = 'pages_requests';

    public function accept($id, $role)
    {
        $row = $this->find('first', array(
            'conditions' => array(
                'PageRequest.id' => $id
            )
        ));

        if(!$row)
            return false;

        $this->updateAll(array(
            'status' => '2'
        ), array(
            'PageRequest.id' => $id
        ));

        App::import('Model', 'Dane.ObjectPage');
        App::import('Model', 'Dane.ObjectUser');
        $object = new ObjectPage();
        $user = new ObjectUser();

        $user->save(array(
            'ObjectUser' => array(
                'dataset' => $row['PageRequest']['dataset'],
                'object_id' => $row['PageRequest']['object_id'],
                'user_id' => $row['PageRequest']['user_id'],
                'role' => $role
            )
        ));

        $object->markAsModerated(
            $row['PageRequest']['dataset'],
            $row['PageRequest']['object_id']
        );

        return true;
    }

}