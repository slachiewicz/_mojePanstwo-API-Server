<?php
App::uses('S3Component', 'Controller/Component');
return S3Component::getObject('nsa.sejmometr.pl', preg_replace('/{id}/', $id, '/orzeczenia_html/{id}.html'));;
?>