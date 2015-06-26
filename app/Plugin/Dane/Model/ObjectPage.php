<?php

class ObjectPage extends AppModel {

    public $useTable = 'objects-pages';

    private $request;

    public function setRequest($request) {
        $this->request = $request;
    }

    public function setLogo($value) {
        $this->setLogoOrCover('logo', $value);
    }

    public function setCover($value) {
        $this->setLogoOrCover('cover', $value);
    }

    private function setLogoOrCover($name, $value) {
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
                if($object['ObjectPage'][$sname] == '0') {
                    $remove = true;
                }
            }

            if($remove) {
                $this->deleteAll($conditions, false);
            } else {
                $this->updateAll(array(
                    $name => $value ? '1' : '0'
                ), $conditions);
            }
        } else {
            $this->save(array(
                'ObjectPage' => array(
                    'dataset' => $this->request['dataset'],
                    'object_id' => (int) $this->request['object_id'],
                    $name => $value ? '1': '0',
                )
            ));
        }
    }

}