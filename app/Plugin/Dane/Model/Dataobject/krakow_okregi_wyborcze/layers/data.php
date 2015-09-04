<?
$id = (int) $id;
return $this->DB->selectAssoc("
    SELECT
      ilosc_miesz_norma_przedst
    FROM
      pl_gminy_krakow_okregi
    WHERE id = $id
");