<?php

class MapaPrawa extends AppModel
{

    public $useTable = false;

    public function loadBlockData($dataset, $object_id)
    {

        switch ($dataset) {
            case "rcl_etapy":
                return $this->loadBlockRclData($object_id);
            case "sejm_druki":
                return $this->loadBlockSejmDrukiData($object_id);
        }

    }

    public function loadBlockRclData($id)
    {
		
		$catalog = $this->query("SELECT `rcl_katalogi_typy`.`id`, `rcl_katalogi_typy`.`opis` FROM `rcl_katalogi_typy` JOIN `rcl_katalogi` ON `rcl_katalogi_typy`.`id` = `rcl_katalogi`.`typ_id` WHERE `rcl_katalogi`.`id` = '$id' LIMIT 1");
				
        $list = $this->query("SELECT id, tytul FROM `rcl_katalogi` WHERE katalog_id='" . addslashes($id) . "' AND docs_count>0");
        foreach ($list as &$l) {
            $l = $l['rcl_katalogi'];
            $l['tytul'] = ucfirst($l['tytul']);
        }

        $output = array(
            'info' => @$catalog[0]['rcl_katalogi_typy']['opis'],
            'list' => $list,
        );

        return $output;

    }

    public function loadBlockSejmDrukiData($id)
    {

        $druk = $this->query("SELECT id, nr_str FROM `s_druki` WHERE id='" . addslashes($id) . "'");

        $list = array(
            array(
                'id' => $id,
                'tytul' => 'Druk nr ' . $druk[0]['s_druki']['nr_str'],
            ),
        );

        $output = array(
            'info' => 'Druk sejmowy',
            'list' => $list,
        );

        return $output;

    }

    public function loadItemData($dataset, $object_id, $blockId, $currentPage, $limitPerPage)
    {

        switch ($dataset) {

            case "rcl_etapy":
                return $this->loadItemRclData($blockId, $currentPage, $limitPerPage);
            case "sejm_druki":
                return $this->loadItemSejmDrukiData($blockId, $currentPage, $limitPerPage);

        }

    }

    public function loadItemRclData($id, $currentPage, $limitPerPage)
    {

        if (!$limitPerPage || ($limitPerPage > 10) || ($limitPerPage < 0))
            $limitPerPage = 10;

        if (!$currentPage || ($currentPage < 0))
            $currentPage = 1;

        $docs = array();

        $offset = ($currentPage - 1) * $limitPerPage;

        $files = $this->query("SELECT SQL_CALC_FOUND_ROWS id, dokument_id, tytul as 'nazwa' FROM rcl_dokumenty as `files` WHERE katalog_id='" . addslashes($id) . "' LIMIT " . $offset . ',' . $limitPerPage);

        $total_files_count = $this->query("SELECT FOUND_ROWS()");
        $total_files_count = (int)@array_shift($total_files_count[0][0]);

        $pages_count = ceil($total_files_count / $limitPerPage);

        foreach ($files as $file) {

            $docs[] = array(
                'id' => $file['files']['id'],
                'dokument_id' => $file['files']['dokument_id'],
                'title' => $file['files']['nazwa'],
            );

        }

        $output = array(
            'pages' => $pages_count,
            'docs' => $docs,
        );

        return $output;

    }

    public function loadItemSejmDrukiData($id, $currentPage, $limitPerPage)
    {

        if (!$limitPerPage || ($limitPerPage > 10) || ($limitPerPage < 0))
            $limitPerPage = 10;

        if (!$currentPage || ($currentPage < 0))
            $currentPage = 1;

        $docs = array();

        $offset = ($currentPage - 1) * $limitPerPage;

        $files = $this->query("SELECT SQL_CALC_FOUND_ROWS id, dokument_id, CONCAT(filename, '.', extension) as 'nazwa' FROM s_druki_pliki as `files` WHERE druk_id='" . addslashes($id) . "' ORDER BY id DESC LIMIT " . $offset . ',' . $limitPerPage);

        $total_files_count = $this->query("SELECT FOUND_ROWS()");
        $total_files_count = (int)@array_shift($total_files_count[0][0]);

        $pages_count = ceil($total_files_count / $limitPerPage);

        foreach ($files as $file) {

            $docs[] = array(
                'id' => $file['files']['id'],
                'dokument_id' => $file['files']['dokument_id'],
                'title' => $file[0]['nazwa'],
            );

        }

        $output = array(
            'pages' => $pages_count,
            'docs' => $docs,
        );

        return $output;

    }


} 