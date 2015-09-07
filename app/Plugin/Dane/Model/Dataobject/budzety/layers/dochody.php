<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 07/09/15
 * Time: 14:25
 */

$output=$this->DB->query("SELECT pl_budzety_dochody.* FROM pl_budzety_dochody JOIN budzety ON pl_budzety_dochody.rocznik=budzety.rok WHERE budzety.id=$id");

return $output;