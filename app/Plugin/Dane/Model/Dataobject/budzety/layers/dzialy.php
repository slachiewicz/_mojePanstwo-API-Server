<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 27/08/15
 * Time: 10:58
 */

//$output=$this->DB->query("SELECT pl_budzety_wydatki.* FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok WHERE budzety.id=$id AND pl_budzety_wydatki.type='dzial' GROUP BY pl_budzety_wydatki.type ORDER BY pl_budzety_wydatki.plan DESC");

$output['dzialy']=$this->DB->query("SELECT pl_budzety_wydatki.dzial_str,pl_budzety_wydatki.tresc,SUM(pl_budzety_wydatki.plan) as plan FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok WHERE budzety.id=$id AND pl_budzety_wydatki.type='dzial' GROUP BY pl_budzety_wydatki.dzial_str ORDER BY plan DESC");
$output['rozdzialy']=$this->DB->query("SELECT pl_budzety_wydatki.rozdzial_str,pl_budzety_wydatki.tresc, pl_budzety_wydatki_dzialy.src,pl_budzety_wydatki_dzialy.tresc, SUM(pl_budzety_wydatki.plan) as plan FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok JOIN pl_budzety_wydatki_dzialy ON pl_budzety_wydatki.dzial_id=pl_budzety_wydatki_dzialy.id WHERE budzety.id=$id AND pl_budzety_wydatki.type='rozdzial' GROUP BY pl_budzety_wydatki.rozdzial_str ORDER BY plan DESC");

return $output;