<?php

class ZamowieniaPubliczne extends AppModel
{

    public $useTable = false;

    public function getStats()
    {

        $counts = Cache::read('zamowienia_publiczne:getStats', 'ultrashort');
        if (!$counts) {

            $data = $this->query("SELECT `data_publikacji`, COUNT(*) as `count` 
		    FROM `uzp_dokumenty` 
			WHERE akcept='1' 
			GROUP BY `data_publikacji` 
			ORDER BY `data_publikacji` DESC 
			LIMIT 150");

            $counts = array();
            for ($i = count($data) - 1; $i--; $i >= 0)
                $counts[] = array(strtotime($data[$i]['uzp_dokumenty']['data_publikacji']) * 1000, (int)$data[$i][0]['count']);

            Cache::write('zamowienia_publiczne:getStats', $counts, 'ultrashort');

        }

        return array(
            'days' => $counts,
        );

    }

} 