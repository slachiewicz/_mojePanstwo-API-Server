<?php

require_once 'ZamowieniaPubliczne.php';
$obj = new ZamowieniaPubliczne();
$obj->id = $id;
return $obj->details();