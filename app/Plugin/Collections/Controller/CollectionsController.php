<?php

App::uses('AppController', 'Controller');

/**
 * @property CollectionObject CollectionObject
 * @property Collection Collection
 */
class CollectionsController extends AppController {

    public $uses = array('Collections.Collection', 'Collections.CollectionObject');
    public $components = array('S3');

    public function beforeFilter() {
        parent::beforeFilter();

        if($this->Auth->user('type') != 'account')
            throw new ForbiddenException;
    }

    public function get($id) {
        $this->set('response', $this->Collection->find('all', array(
            'conditions' => array(
                'Collection.user_id' => $this->Auth->user('id')
            ),
            'joins' => array(
                array(
                    'table' => 'collection_object',
                    'alias' => 'CollectionObject',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'CollectionObject.collection_id = Collection.id',
                        'CollectionObject.object_id' => (int) $id
                    )
                )
            ),
            'fields' => array('Collection.*', 'CollectionObject.*')
        )));
        $this->set('_serialize', 'response');
    }

    public function create() {
        $data = array_merge($this->request->data, array(
            'user_id' => $this->Auth->user('id'),
        ));

        $image = true; $imageData = array();
        $fields = array('image', 'x', 'y', 'zoom');
        foreach($fields as $f)
            if(!array_key_exists($f, $data))
                $image = false;

        if($image) {
            foreach($fields as $f) {
                $imageData[$f] = $data[$f];
                unset($data[$f]);
            }

            $data['image'] = 1;
        } else
            $data['image'] = 0;

        $this->Collection->set($data);
        if($this->Collection->validates()) {
            $response = $this->Collection->save(array(
                'Collection' => $data
            ));



            if($image) {

                $id = $response['Collection']['id'];
                $ext = 'jpg';
                $x = (int) $imageData['x'];
                $y = (int) $imageData['y'];
                $zoom = ((float) $imageData['zoom']) * 100;
                $width = 810;
                $height = 320;

                $src = 'pages/kolekcje/' . $id . '.' . $ext;
                $tmp_src = APP . 'tmp/' . $id . '.' .$ext;
                $tmp_src_zoom = APP . 'tmp/' . $id . '_zoom.' .$ext;
                $tmp_src_crop = APP . 'tmp/' . $id . '_crop.' .$ext;

                $data = explode(',', $imageData['image']);
                $decoded = base64_decode($data[1]);

                if(!$decoded)
                    throw new Exception('base64_decode error');

                $object = $this->S3->putObject(
                    $decoded,
                    'portal',
                    '0/'.$src,
                    S3::ACL_PUBLIC_READ,
                    array(),
                    array('Content-Type' => 'image/' . $ext)
                );

                if(!$object)
                    throw new Exception('S3 putObject error');

                $tmp_image = file_put_contents($tmp_src, file_get_contents('http://sds.tiktalik.com/portal/0/' . $src));

                if(!$tmp_image)
                    throw new Exception('tmp_image error');

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

        } else {
            $response = $this->Collection->validationErrors;
        }

        $this->set('response', $response);
        $this->set('_serialize', 'response');
    }

    public function addObject($id, $object_id) {
        $collection = $this->Collection->find('first', array(
            'conditions' => array(
                'Collection.id' => $id
            )
        ));

        if(!$collection)
            throw new NotFoundException;

        if($collection['Collection']['user_id'] != $this->Auth->user('id'))
            throw new ForbiddenException;

        $this->set('response', $this->CollectionObject->save(array(
            'CollectionObject' => array(
                'collection_id' => (int) $id,
                'object_id' => (int) $object_id
            )
        )));
        $this->set('_serialize', 'response');
    }

    public function removeObject($id, $object_id) {
        $collection = $this->Collection->find('first', array(
            'conditions' => array(
                'Collection.id' => $id
            )
        ));

        if(!$collection)
            throw new NotFoundException;

        if($collection['Collection']['user_id'] != $this->Auth->user('id'))
            throw new ForbiddenException;

        $this->set('response', $this->CollectionObject->query('DELETE FROM collection_object WHERE collection_id = ' . (int) $id . ' AND object_id = '. (int) $object_id));
        $this->set('_serialize', 'response');
    }
}