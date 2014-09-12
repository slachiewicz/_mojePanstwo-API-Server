<?

	App::import('model', 'MPCache');
    $MPCache = new MPCache();
    
    $data = $MPCache->getDataSource()->get('stats/krakow_posiedzenia/terms/' . $id . '*/*');
    
	return json_decode($data, true);    
