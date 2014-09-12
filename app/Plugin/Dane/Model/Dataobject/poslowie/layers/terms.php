<?

	App::import('model', 'MPCache');
    $MPCache = new MPCache();
    
    $data = $MPCache->getDataSource()->get('poslowie/stats/terms/' . $id . '*/*');
    
	return json_decode($data, true);    
