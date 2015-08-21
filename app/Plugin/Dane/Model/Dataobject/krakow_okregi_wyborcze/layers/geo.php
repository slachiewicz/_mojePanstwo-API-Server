<?
$id = (int) $id;
return $this->DB->selectValue("
    SELECT
      AsText(polygon)
    FROM
      pl_gminy_krakow_okregi
    WHERE id = $id
");