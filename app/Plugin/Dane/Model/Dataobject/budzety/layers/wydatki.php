<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 26/08/15
 * Time: 13:21
 */

$output=$this->DB->query("SELECT pl_budzety_wydatki.* FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok WHERE budzety.id=$id");


return $output;