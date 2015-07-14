<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 14/07/15
 * Time: 14:40
 */


$data = S3::getObject('resources', 'senat/stenogramy/' . $id . '.html');
return $data;