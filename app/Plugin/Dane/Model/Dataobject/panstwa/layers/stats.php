<?
	
$data = array();
$id = (int) $id;

$data = $this->DB->selectAssocs("
  SELECT import_pln, eksport_pln, rocznik
  FROM hz_panstwa_roczniki
  WHERE panstwo_id = $id
  GROUP BY rocznik
  ORDER BY rocznik ASC
");

/*

$data = array(
    'import'    => array(
        'rocznik'   => 'suma_pln',
    ),
    'eksport'   => array(
        'rocznik'   => 'suma_pln
    )
);

 */

return $data;