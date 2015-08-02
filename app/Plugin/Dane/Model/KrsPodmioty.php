<?php

App::uses('OrganizacjeDzialaniaTematy', 'Dane.Model');
App::uses('OrganizacjeDzialania', 'Dane.Model');
App::uses('Temat', 'Dane.Model');
App::uses('S3Component', 'Controller.Component');

App::uses('CakeSession', 'Model/Datasource');

class KrsPodmioty extends AppModel {

    public $useTable = false;

    public function save_edit_data_form($data, $id, $dataset) {
        App::uses('ObjectPage', 'Dane.Model');
        $this->ObjectPage = new ObjectPage();

        return array(
            'flash_message' => $this->ObjectPage->setData($data, $id, $dataset) ?
                'Dane zostały poprawnie zaktualizowane' :
                'Wystąpił błąd podczas zapisywania danych'
        );
    }

    public function add_activity($data, $id, $dataset) {

        $this->OrganizacjeDzialania = new OrganizacjeDzialania();
        $this->OrganizacjeDzialaniaTematy = new OrganizacjeDzialaniaTematy();
        $this->Temat = new Temat();

        $this->OrganizacjeDzialania->save(array(
            'OrganizacjeDzialania' => array(
                'owner_dataset' => $dataset,
                'owner_object_id' => $id,
                'cts' => date('Y-m-d H:i:s'),
                'user_id' => (int) CakeSession::read('Auth.User.id'),
                'tytul' => $data['tytul'],
                'opis' => $data['opis'],
                'status' => $data['status'],
                'podsumowanie' => $data['podsumowanie'],
                'cover_photo' => $data['cover_photo'] ? '1' : '0',
                'folder' => isset($data['folder']) ? $data['folder'] : '1',
                'geo_lat' => (float) $data['geo_lat'],
                'geo_lng' => (float) $data['geo_lng']
            )
        ));

        $dzialanie_id = $this->OrganizacjeDzialania->getLastInsertId();
        $this->_update_activity_tags($dzialanie_id, @$data['tagi']);
        $this->_update_activity_photo($dzialanie_id, $data);

        return array(
            'flash_message' => 'Działanie zostało poprawnie dodane',
            'redirect_url' => '/dane/' . $dataset . '/' . $id . '/dzialania/' . $dzialanie_id,
       );
    }

    public function edit_activity($data, $id, $dataset) {

        $success = false;
        $this->OrganizacjeDzialania = new OrganizacjeDzialania();
        $this->OrganizacjeDzialaniaTematy = new OrganizacjeDzialaniaTematy();
        $this->Temat = new Temat();

        $object = $this->OrganizacjeDzialania->find('first', array(
            'conditions' => array(
                'OrganizacjeDzialania.owner_dataset' => $dataset,
                'OrganizacjeDzialania.owner_object_id' => $id,
                'OrganizacjeDzialania.id' => $data['id']
            )
        ));

        $deleted = isset($data['deleted']) && $data['deleted'] == '1';

        if($object) {

            $toUpdate['mts'] = date('Y-m-d H:i:s');
            $toUpdate['id'] = $object['OrganizacjeDzialania']['id'];

            $fields = array('tytul', 'opis', 'folder', 'status', 'podsumowanie', 'geo_lat', 'geo_lng');
            if($deleted)
                $fields[] = 'deleted';

            foreach($fields as $field) {
                if(isset($data[$field]))
                    $toUpdate[$field] = $data[$field];
            }

            $this->_update_activity_tags($object['OrganizacjeDzialania']['id'], @$data['tagi']);
            $this->_update_activity_photo($object['OrganizacjeDzialania']['id'], $data);
            $toUpdateFields = array('mts', 'cover_photo', 'tytul', 'opis', 'status', 'podsumowanie', 'folder', 'geo_lat', 'geo_lng');
            if($deleted)
                $toUpdateFields[] = 'deleted';

            $success = $this->OrganizacjeDzialania->save($toUpdate, false, $toUpdateFields);

        }

        $response = array(
            'flash_message' =>
                $success ?
                    $deleted ?
                        'Działanie zostało poprawnie usunięte'
                        :
                        'Działanie zostało poprawnie zaktualizowane'
                    :
                    'Wystąpił błąd podczas aktualizacji'
        );

        if($deleted)
            $response['redirect_url'] = "/dane/$dataset/$id/dzialania";

        return $response;
    }

    private function _update_activity_photo($id, $data) {
        if($id && strlen($data['cover_photo']) > 100) {

            $this->S3 = new S3Component(new ComponentCollection);

            $image = $data['cover_photo'];
            $ext = 'jpg';
            $x = (int) $data['x'];
            $y = (int) $data['y'];
            $zoom = ((float) $data['zoom']) * 100;
            $width = 810;
            $height = 320;

            $src = 'pages/dzialania/' . $id . '.' . $ext;
            $tmp_src = APP . 'tmp/' . $id . '.' .$ext;
            $tmp_src_zoom = APP . 'tmp/' . $id . '_zoom.' .$ext;
            $tmp_src_crop = APP . 'tmp/' . $id . '_crop.' .$ext;

            $data = explode(',', $image);
            $decoded = base64_decode($data[1]);

            $object = $this->S3->putObject(
                $decoded,
                'portal',
                '0/'.$src,
                S3::ACL_PUBLIC_READ,
                array(),
                array('Content-Type' => 'image/' . $ext)
            );

            $tmp_image = file_put_contents($tmp_src, file_get_contents('http://sds.tiktalik.com/portal/0/' . $src));
            exec("convert $tmp_src -resize $zoom% $tmp_src_zoom");

            $x = $x >= 0 ? '-' . $x : '+' . (-$x);
            $y = $y >= 0 ? '-' . $y : '+' . (-$y);

            exec("convert $tmp_src_zoom -crop {$width}x{$height}{$x}{$y}\! -background white -flatten $tmp_src_crop");

            $crop_image = file_get_contents($tmp_src_crop);

            $object = $this->S3->putObject(
                $crop_image,
                'portal',
                '1/'.$src,
                S3::ACL_PUBLIC_READ,
                array(),
                array('Content-Type' => 'image/' . $ext)
            );

            exec("convert $tmp_src_crop -resize x200 $tmp_src_crop");

            $crop_image = file_get_contents($tmp_src_crop);

            $object = $this->S3->putObject(
                $crop_image,
                'portal',
                '2/'.$src,
                S3::ACL_PUBLIC_READ,
                array(),
                array('Content-Type' => 'image/' . $ext)
            );

            unlink($tmp_src_crop);
            unlink($tmp_src_zoom);
            unlink($tmp_src);
        }
    }

    private function _update_activity_tags($id, $tags = false) {
        $tags = explode(',', $tags);

        $this->OrganizacjeDzialaniaTematy->deleteAll(array(
            'OrganizacjeDzialaniaTematy.dzialanie_id' => $id
        ), false);

        $update = array();

        if(!$tags)
            return true;

        foreach($tags as $tag) {
            $q = trim($tag);
            $temat = $this->Temat->find('first', array(
                'conditions' => array(
                    'Temat.q' => $q
                )
            ));

            if(!$temat) {
                $this->Temat->clear();
                $this->Temat->save(array(
                    'q' => $q,
                ));

                $update[] = (int) $this->Temat->getLastInsertId();
            } else {
                $update[] = (int) $temat['Temat']['id'];
            }
        }

        $update = array_unique($update);

        foreach($update as $temat_id) {
            $this->OrganizacjeDzialaniaTematy->clear();
            $this->OrganizacjeDzialaniaTematy->save(array(
                'dzialanie_id' => $id,
                'temat_id' => $temat_id
            ));
        }
    }

    public function pobierz_nowy_odpis($data, $id, $dataset) {
        $db = ConnectionManager::getDataSource('default');
        $id = (int) $id;

        $results = $db->query("
            SELECT id, complete, priority
            FROM krs_files
            WHERE
              status!='-1' AND 
              krs_pozycje_id = $id AND
              (
                complete = '0'
                OR
                (
                  complete = '1' AND
                  TIMESTAMPDIFF(HOUR, complete_ts, NOW()) <= 24
                )
              )
            ORDER BY complete_ts DESC 
            LIMIT 1
        ");
        
        if($results[0]['krs_files']) {
            $row = $results[0]['krs_files'];
            if($row['complete'] == '1') {
                $message = 'Odpis był już ostatnio aktualizowany';
            } else if($row['complete'] == '0' && $row['priority']) {
                $message = 'Odpis oczekuje w kolejce na pobranie';
            } else {
                $db->query("UPDATE krs_files SET priority = 1 WHERE id = ".$row['id']);
                $message = 'Priorytet został zwiększony i odpis zostanie wkrótce pobrany';
            }
        } else {
            $db->query("INSERT INTO krs_files(krs_pozycje_id, priority) VALUES ($id, 1)");
            $message = 'Odpis został dodany do kolejki jako priorytet i zostanie wkrótce pobrany';
        }

        return array(
            'flash_message' => $message
        );
    }

}