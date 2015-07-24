<?php

class ObjectPage extends AppModel {

    public $useTable = 'objects-pages';

    private $request;

    public function setRequest($request) {
        $this->request = $request;
    }

    public function setData($data, $id, $dataset)
    {
        $conditions = array(
            'ObjectPage.dataset' => $dataset,
            'ObjectPage.object_id' => (int) $id
        );

        $object = $this->find('first', array(
            'conditions' => $conditions
        ));

        if($object) {
            $success = $this->updateAll(array(
                'description' => "'".Sanitize::escape($data['description'])."'"
            ), $conditions);
        } else {
            $success = $this->save(array(
                'ObjectPage' => array(
                    'dataset' => $dataset,
                    'object_id' => (int) $id,
                    'moderated' => '1',
                    'description' => $data['description']
                )
            ));
        }

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

}