<?
	
$data = array();
$id = (int) $id;

$data = $this->DB->selectAssocs("
  SELECT import_pln, eksport_pln, rocznik
  FROM hz_symbole_roczniki
  WHERE symbol_id = $id
  GROUP BY rocznik
  ORDER BY rocznik ASC
");

return $data;