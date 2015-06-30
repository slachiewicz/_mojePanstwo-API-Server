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

    public function setCover($value, $credits = null) {
        $this->setLogoOrCover('cover', $value, $credits);
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
                if($object['ObjectPage'][$sname] == '0') {
                    $remove = true;
                }
            }

            if($remove) {
                $this->deleteAll($conditions, false);
            } else {
                $data = array(
                    $name => $value ? '1' : '0'
                );

                if($credits != null)
                    $data['cover_credits'] = $credits;

                $this->updateAll($data, $conditions);
            }
        } else {

            $data = array(
                'dataset' => $this->request['dataset'],
                'object_id' => (int) $this->request['object_id'],
                $name => $value ? '1': '0',
            );

            if($credits != null)
                $data['cover_credits'] = $credits;

            $this->save(array(
                'ObjectPage' => $data
            ));
        }
    }

}