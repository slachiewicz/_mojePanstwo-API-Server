<?php

class ProjectsController extends AppController {

    public $uses = array('Dane.OrganizacjeDzialania', 'Paszport.User', 'Dane.ObjectUser');
    public $components = array('RequestHandler', 'S3');

    public function beforeFilter() {
        parent::beforeFilter();
        if(!$this->isAuthorized())
            throw new ForbiddenException();
    }

    public function index() {
        $results = $this->OrganizacjeDzialania->find('all', array(
            'conditions' => array(
                'OrganizacjeDzialania.owner_dataset' => $this->request['dataset'],
                'OrganizacjeDzialania.owner_object_id' => $this->request['object_id']
            )
        ));

        $dzialania = array();
        foreach($results as $row) {
            $dzialania[] = array(
                'id' => $row['OrganizacjeDzialania']['id'],
                'user_id' => $row['OrganizacjeDzialania']['user_id'],
                'cts' => $row['OrganizacjeDzialania']['cts'],
                'mts' => $row['OrganizacjeDzialania']['mts'],
                'tytul' => $row['OrganizacjeDzialania']['tytul'],
                'opis' => $row['OrganizacjeDzialania']['opis'],
                'cover_photo' => $row['OrganizacjeDzialania']['cover_photo'],
                'folder' => $row['OrganizacjeDzialania']['folder'],
                'geo_lat' => $row['OrganizacjeDzialania']['geo_lat'],
                'geo_lng' => $row['OrganizacjeDzialania']['geo_lng']
            );
        }

        $this->setSerialized('dzialania', $dzialania);
    }

    public function add() {
        $this->OrganizacjeDzialania->save(array(
            'OrganizacjeDzialania' => array(
                'owner_dataset' => $this->request['dataset'],
                'owner_object_id' => $this->request['object_id'],
                'cts' => date('Y-m-d H:i:s'),
                'user_id' => (int) $this->Auth->user('id'),
                'tytul' => $this->data['tytul'],
                'opis' => $this->data['opis'],
                'cover_photo' => $this->isCoverPhoto($this->data['cover_photo']) ? '1' : '0',
                'folder' => isset($this->data['folder']) ? $this->data['folder'] : '1',
                'geo_lat' => (float) $this->data['geo_lat'],
                'geo_lng' => (float) $this->data['geo_lng']
            )
        ));

        $id = $this->OrganizacjeDzialania->getLastInsertId();
        $this->saveCoverPhoto($this->data['cover_photo'], $id);
        $this->setSerialized('id', $id);
    }

    public function view() {
        $dzialanie = false;

        $object = $this->OrganizacjeDzialania->find('first', array(
            'conditions' => array(
                'OrganizacjeDzialania.owner_dataset' => $this->request['dataset'],
                'OrganizacjeDzialania.owner_object_id' => $this->request['object_id'],
                'OrganizacjeDzialania.id' => $this->request['id']
            )
        ));

        if($object) {

            $dzialanie = array(
                'id' => $object['OrganizacjeDzialania']['id'],
                'user_id' => $object['OrganizacjeDzialania']['user_id'],
                'cts' => $object['OrganizacjeDzialania']['cts'],
                'mts' => $object['OrganizacjeDzialania']['mts'],
                'tytul' => $object['OrganizacjeDzialania']['tytul'],
                'opis' => $object['OrganizacjeDzialania']['opis'],
                'cover_photo' => $object['OrganizacjeDzialania']['cover_photo'],
                'folder' => $object['OrganizacjeDzialania']['folder'],
                'geo_lat' => $object['OrganizacjeDzialania']['geo_lat'],
                'geo_lng' => $object['OrganizacjeDzialania']['geo_lng']
            );

        }

        $this->setSerialized('dzialanie', $dzialanie);
    }

    public function edit() {
        $success = false;
        $toUpdate = array();

        $object = $this->OrganizacjeDzialania->find('first', array(
            'conditions' => array(
                'OrganizacjeDzialania.owner_dataset' => $this->request['dataset'],
                'OrganizacjeDzialania.owner_object_id' => $this->request['object_id'],
                'OrganizacjeDzialania.id' => $this->request['id']
            )
        ));

        if($object) {

            if (isset($this->data['cover_photo'])) {
                $coverPhoto = $this->data['cover_photo'];
                if (!$this->isCoverPhoto($coverPhoto)) {
                    $this->removeCoverPhoto();
                    $photo = '0';
                } else {
                    $this->saveCoverPhoto($coverPhoto, $this->request['id']);
                    $photo = '1';
                }

                $toUpdate['cover_photo'] = $photo;
            }

            $toUpdate['mts'] = date('Y-m-d H:i:s');
            $toUpdate['id'] = $object['OrganizacjeDzialania']['id'];

            $fields = array('tytul', 'opis', 'folder', 'geo_lat', 'geo_lng');
            foreach($fields as $field) {
                if(isset($this->data[$field]))
                    $toUpdate[$field] = $this->data[$field];
            }

            $success = $this->OrganizacjeDzialania->save($toUpdate, false, array('mts', 'cover_photo', 'tytul', 'opis', 'folder', 'geo_lat', 'geo_lng'));
        }

        $this->setSerialized('success', $success);
    }

    public function delete() {
        $success = false;

        $object = $this->OrganizacjeDzialania->find('first', array(
            'conditions' => array(
                'OrganizacjeDzialania.owner_dataset' => $this->request['dataset'],
                'OrganizacjeDzialania.owner_object_id' => $this->request['object_id'],
                'OrganizacjeDzialania.id' => $this->request['id']
            )
        ));

        if($object) {
            $this->OrganizacjeDzialania->delete($object['OrganizacjeDzialania']['id']);
            $success = true;
        }

        $this->setSerialized('success', $success);
    }

    private function removeCoverPhoto() {
        $this->S3->deleteObject(
            'portal',
            'pages/dzialania/' . $this->request['dataset'] . '/' . $this->request['object_id'] . '/' . $this->request['id'] . '.jpg'
        );
    }

    private function saveCoverPhoto($photo, $id) {
        if(!$this->isCoverPhoto($photo))
            return false;

        $base64 = explode(',', $photo);
        $data = base64_decode($base64[1]);

        return $this->S3->putObject(
            $data,
            'portal',
            'pages/dzialania/' . $this->request['dataset'] . '/' . $this->request['object_id'] . '/' . $id . '.jpg',
            S3::ACL_PUBLIC_READ,
            array(),
            array('Content-Type' => 'image/jpg')
        );
    }

    private function isCoverPhoto($photo) {
        if(!$photo || $photo == 'false' || strlen($photo) < 100)
            return false;

        return true;
    }

    private function isAuthorized() {
        if($this->Auth->user('type') != 'account')
            return false;

        $this->User->recursive = 2;
        $user = $this->User->findById(
            $this->Auth->user('id')
        );

        if(!$user)
            return false;

        $roles = array();
        foreach($user['UserRole'] as $role) {
            $roles[] = $role['Role']['name'];
        }

        if(in_array('superuser', $roles)) {
            return true;
        } else {
            $object = $this->ObjectUser->find('first', array(
                'conditions' => array(
                    'ObjectUser.dataset' => $this->request['dataset'],
                    'ObjectUser.object_id' => $this->request['object_id'],
                    'ObjectUser.user_id' => $user['User']['id']
                )
            ));

            if(
                $object['ObjectUser']['role'] == '1' || // owner
                $object['ObjectUser']['role'] == '2'    // administrator
            ) {
                return true;
            }
        }

        return false;
    }

}