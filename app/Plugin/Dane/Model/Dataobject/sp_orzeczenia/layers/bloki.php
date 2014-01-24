<?php
$data = $this->DB->query("SELECT id, tytul, wartosc FROM orzeczenia_bloki WHERE orzeczenie_id='$id' ORDER BY id ASC");
return $data;