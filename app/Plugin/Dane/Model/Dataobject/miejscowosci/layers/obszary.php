<?php
$obj = new AppModel($id);
$obj->useTable = 'pl_miejscowosci';
$obj->alias = 'Miejscowosc';
$obj->read();
return $this->DB->query('SELECT Powiat.id,Powiat.nazwa, Wojewodztwo.id, Wojewodztwo.nazwa FROM pl_powiaty Powiat, wojewodztwa Wojewodztwo WHERE Powiat.id = ' . $obj->data['Miejscowosc']['powiat_id'] . ' AND Wojewodztwo.id=' . $obj->data['Miejscowosc']['woj_id']);