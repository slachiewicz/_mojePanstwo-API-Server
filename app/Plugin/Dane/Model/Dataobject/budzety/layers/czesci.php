<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 26/08/15
 * Time: 14:39
 */

$output=$this->DB->query("SELECT pl_budzety_wydatki.* FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok WHERE budzety.id=$id AND pl_budzety_wydatki.type='czesc' ORDER BY pl_budzety_wydatki.plan DESC");


return $output;