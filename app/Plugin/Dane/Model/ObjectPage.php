<?php

class ObjectPage extends AppModel {

    public $useTable = 'objects-pages';

    private $request;

    public function setRequest($request) {
        $this->request = $request;
    }

    public function setData($data, $id, $dataset)
    {
        $this->log('setData(), data: ' . $data .', id: ' . $id .', dataset: ' . $dataset, 'debug');
        $conditions = array(
            'ObjectPage.dataset' => $dataset,
            'ObjectPage.object_id' => (int) $id
        );

        $object = $this->find('first', array(
            'conditions' => $conditions
        ));

        $db = ConnectionManager::getDataSource('default');

        if(isset($data['areas']) && is_array($data['areas'])) {
            $db->query("DELETE FROM organizacja_obszar WHERE object_id = " . ((int) $id));
            foreach($data['areas'] as $area_id)
                $db->query("INSERT INTO organizacja_obszar VALUES (" . (int) $id . ", " . (int) $area_id. ")");
        }

        $fields = array(
            'description',
            'phone',
            'email',
            'wwww',
            'facebook',
            'twitter',
            'instagram',
            'youtube',
            'vine'
        );

        if($object) {
            $d = array();
            foreach($fields as $i => $field)
                if(isset($data[$field]))
                    $d[$field] = "'".Sanitize::escape($data[$field])."'";

            $success = $this->updateAll($d, $conditions);
        } else {
            $d = array();
            foreach($fields as $i => $field)
                if(isset($data[$field]))
                    $d[$field] = $data[$field];

            $success = $this->save(array(
                'ObjectPage' => array_merge(array(
                    'dataset' => $dataset,
                    'object_id' => (int) $id,
                    'moderated' => '1'
                ), $d)
            ));

            $row = $this->query('SELECT id FROM objects WHERE dataset = ? AND object_id = ?', array($dataset, $id));
            $this->query('UPDATE `objects-pages` SET id = ? WHERE dataset = ? AND object_id = ?', array($row[0]['objects']['id'], $dataset, $id));
        }

        $row = $this->query('SELECT id FROM objects WHERE dataset = ? AND object_id = ?', array(
            $dataset,
            $id
        ));

        $id = $row[0]['objects']['id'];

        if($id)
            $this->syncById($id);

        return (bool) $success;
    }

    public function setLogo($value) {
        $this->setLogoOrCover('logo', $value);
    }

    public function setCover($value, $credits = null) {
        $this->setLogoOrCover('cover', $value, $credits);
    }

    public function whenUserWasAdded() {
        $this->setModerated(true);
    }

    public function whenUsersWasDeleted() {
        $this->setModerated(false);
    }

    public function markAsModerated($dataset, $object_id) {
        $conditions = array(
            'ObjectPage.dataset' => $dataset,
            'ObjectPage.object_id' => (int) $object_id
        );

        $object = $this->find('first', array(
            'conditions' => $conditions
        ));

        if($object) {
            $this->updateAll(array(
                'moderated' => '1'
            ), $conditions);
        } else {
            $this->save(array(
                'ObjectPage' => array(
                    'dataset' => $dataset,
                    'object_id' => (int) $object_id,
                    'moderated' => '1'
                )
            ));
        }
    }

    private function setModerated($value = true) {
        $conditions = array(
            'ObjectPage.dataset' => $this->request['dataset'],
            'ObjectPage.object_id' => (int) $this->request['object_id']
        );

        $this->updateAll(array(
            'moderated' => $value ? '1' : '0'
        ), $conditions);
    }

    private function setLogoOrCover($name, $value, $credits = null) {
        $conditions = array(
            'ObjectPage.dataset' => $this->request['dataset'],
            'ObjectPage.object_id' => (int) $this->request['object_id']
        );

        $object = $this->find('first', array(
            'conditions' => $conditions
        ));

        if($object) {
            $remove = false;
            if($value == false) {
                $sname = $name == 'logo' ? 'cover' : 'logo';
                if($object['ObjectPage'][$sname] == '0' && $object['ObjectPage']['moderated'] == '0') {
                    $remove = true;
                }
            }

            if($remove) {
                $this->deleteAll($conditions, false);
            } else {
                $data = array(
                    $name => $value ? '1' : '0'
                );

                if(!is_null($credits))
                    $data['credits'] = "'$credits'";

                $this->updateAll($data, $conditions);
            }
        } else {

            $data = array(
                'dataset' => $this->request['dataset'],
                'object_id' => (int) $this->request['object_id'],
                $name => $value ? '1': '0',
            );

            if(!is_null($credits))
                $data['credits'] = "'$credits'";

            $this->save(array(
                'ObjectPage' => $data
            ));
        }
    }

    public function afterSave($created, $options) {
        if(isset($this->data['ObjectPage']['id'])) {
            $id = $this->data['ObjectPage']['id'];
        } else if(isset($this->data['ObjectPage']['dataset']) && isset($this->data['ObjectPage']['object_id'])) {
            $row = $this->query('SELECT id FROM objects WHERE dataset = ? AND object_id = ?', array(
                $this->data['ObjectPage']['dataset'],
                $this->data['ObjectPage']['object_id']
            ));

            $id = $row[0]['objects']['id'];
        } else {
            $id = false;
        }

        $this->log('afterSave() : id = ' . $id, 'debug');

        if($id)
            $this->syncById($id);
    }

    public function syncById($id) {

        if( !$id )
            return false;

        $data = $this->find('first', array(
            'conditions' => array(
                'ObjectPage.id' => $id,
            ),
        ));

        $this->log('syncById() : data = ' . $data, 'debug');

        if( $data ) {

            return $this->syncByData( $data );

        } else
            return false;

    }

    public function syncByData($data) {

        if(
            empty($data) ||
            !isset($data['ObjectPage'])
        )
            return false;

        App::import('model', 'DB');
        $this->DB = new DB();

        $data = $data['ObjectPage'];

        $this->log('syncByData() : data = ' . $data, 'debug');

        $ES = ConnectionManager::getDataSource('MPSearch');

        $params = array();
        $params['index'] = 'mojepanstwo_v1';
        $params['type']  = 'objects_pages';
        $params['id']    = $data['id'];
        $params['refresh'] = true;
        $params['parent'] = $data['id'];
        $params['body']  = array(
            'title' => $data['name'],
            'text' => $data['name'],
            'dataset' => 'objects_pages',
            'slug' => Inflector::slug($data['id']),
            'data' => array(
                'cover' => $data['cover'],
                'logo' => $data['logo'],
                'moderated' => $data['moderated'],
                'description' => $data['description'],
                'credits' => $data['credits'],
                'phone' => $data['phone'],
                'www' => $data['www'],
                'email' => $data['email'],
                'facebook' => $data['facebook'],
                'twitter' => $data['twitter'],
                'instagram' => $data['instagram'],
                'youtube' => $data['youtube'],
                'vine' => $data['vine']
            ),
        );

        $ret = $ES->API->index($params);
        return $data['id'];
    }

}