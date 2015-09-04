<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 04/09/15
 * Time: 15:45
 */

$ret=$this->DB->query("SELECT rok FROM budzety WHERE budzety.id=$id");

$rok_past=$ret[0]['budzety']['rok']-1;

$output=array();

$output['dzialy']=$this->DB->query("SELECT pl_budzety_wydatki.dzial_str,pl_budzety_wydatki.tresc,SUM(pl_budzety_wydatki.plan) as plan, SUM(pl_budzety_wydatki.dotacje_i_subwencje) as dotacje_i_subwencje, SUM(pl_budzety_wydatki.swiadczenia_na_rzecz_osob_fizycznych) as swiadczenia_na_rzecz_osob_fizycznych, SUM(pl_budzety_wydatki.wydatki_biezace_jednostek_budzetowych) as wydatki_biezace_jednostek_budzetowych, SUM(pl_budzety_wydatki.wydatki_majatkowe) as wydatki_majatkowe, SUM(pl_budzety_wydatki.wydatki_na_obsluge_dlugu) as wydatki_na_obsluge_dlugu, SUM(pl_budzety_wydatki.srodki_wlasne_ue) as srodki_wlasne_ue, SUM(pl_budzety_wydatki.wspolfinansowanie_ue) as wspolfinansowanie_ue FROM pl_budzety_wydatki WHERE pl_budzety_wydatki.rocznik=$rok_past AND pl_budzety_wydatki.type='dzial' AND pl_budzety_wydatki.czesc_id NOT IN (15,90,107) GROUP BY pl_budzety_wydatki.dzial_str ORDER BY plan DESC");
return $output;