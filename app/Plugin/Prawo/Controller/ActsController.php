<?php

class ActsController extends AppController
{

    public function search()
    {

        $q = @$this->request->query['q'];
        if ($q) {


            $conditions = array(
                'dataset' => 'ustawy',
                'q' => $q . '*',
                'typ_id' => array('1', '2', '3'),
                'status_id' => '1',
            );

            $params = array(
                'conditions' => $conditions,
                'limit' => 10,
            );

            $search = ClassRegistry::init('Dane.Dataobject')->find('all', $params);

            $this->set('search', $search);
            $this->set('_serialize', array('search'));

        }

    }


    public function download()
    {

        $id = (int)$this->request->params['id'];
        $doc = $this->Act->getDoc($id);

        if ($doc) {

            header("HTTP/1.1 301 Moved Permanently");
            header("Location: " . $doc['url']);
            exit();

        }

    }

} 