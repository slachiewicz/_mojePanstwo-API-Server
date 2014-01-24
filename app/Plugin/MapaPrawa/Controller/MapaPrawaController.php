<?php

class MapaPrawaController extends AppController
{

    public function loadBlockData()
    {

        $dataset = $this->request->query['dataset'];
        $object_id = (int)$this->request->query['object_id'];

        $data = $this->MapaPrawa->loadBlockData($dataset, $object_id);

        $this->set('data', $data);
        $this->set('_serialize', 'data');

    }

    public function loadItemData()
    {

        $dataset = $this->request->query['dataset'];
        $object_id = (int)$this->request->query['object_id'];
        $blockId = (int)$this->request->query['blockId'];
        $currentPage = (int)$this->request->query['currentPage'];
        $limitPerPage = (int)$this->request->query['limitPerPage'];

        $data = $this->MapaPrawa->loadItemData($dataset, $object_id, $blockId, $currentPage, $limitPerPage);

        $this->set('data', $data);
        $this->set('_serialize', 'data');

    }

}