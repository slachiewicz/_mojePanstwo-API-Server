<?php
require_once('SejmWystapienia.php');

$obj = new SejmWystapienia();
$obj->id = $id;
return $obj->html();