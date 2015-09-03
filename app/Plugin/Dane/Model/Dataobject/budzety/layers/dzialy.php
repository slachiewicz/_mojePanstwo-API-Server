<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 27/08/15
 * Time: 10:58
 */

//$output=$this->DB->query("SELECT pl_budzety_wydatki.* FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok WHERE budzety.id=$id AND pl_budzety_wydatki.type='dzial' GROUP BY pl_budzety_wydatki.type ORDER BY pl_budzety_wydatki.plan DESC");

$output['dzialy']=$this->DB->query("SELECT pl_budzety_wydatki.dzial_str,pl_budzety_wydatki.tresc,SUM(pl_budzety_wydatki.plan) as plan, SUM(pl_budzety_wydatki.dotacje_i_subwencje) as dotacje_i_subwencje, SUM(pl_budzety_wydatki.swiadczenia_na_rzecz_osob_fizycznych) as swiadczenia_na_rzecz_osob_fizycznych, SUM(pl_budzety_wydatki.wydatki_biezace_jednostek_budzetowych) as wydatki_biezace_jednostek_budzetowych, SUM(pl_budzety_wydatki.wydatki_majatkowe) as wydatki_majatkowe, SUM(pl_budzety_wydatki.wydatki_na_obsluge_dlugu) as wydatki_na_obsluge_dlugu, SUM(pl_budzety_wydatki.srodki_wlasne_ue) as srodki_wlasne_ue, SUM(pl_budzety_wydatki.wspolfinansowanie_ue) as wspolfinansowanie_ue FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok WHERE budzety.id=$id AND pl_budzety_wydatki.type='dzial' GROUP BY pl_budzety_wydatki.dzial_str ORDER BY plan DESC");
$output['rozdzialy']=$this->DB->query("SELECT pl_budzety_wydatki.rozdzial_str,pl_budzety_wydatki.tresc, pl_budzety_wydatki_dzialy.src,pl_budzety_wydatki_dzialy.tresc, SUM(pl_budzety_wydatki.plan) as plan, SUM(pl_budzety_wydatki.dotacje_i_subwencje) as dotacje_i_subwencje, SUM(pl_budzety_wydatki.swiadczenia_na_rzecz_osob_fizycznych) as swiadczenia_na_rzecz_osob_fizycznych, SUM(pl_budzety_wydatki.wydatki_biezace_jednostek_budzetowych) as wydatki_biezace_jednostek_budzetowych, SUM(pl_budzety_wydatki.wydatki_majatkowe) as wydatki_majatkowe, SUM(pl_budzety_wydatki.wydatki_na_obsluge_dlugu) as wydatki_na_obsluge_dlugu, SUM(pl_budzety_wydatki.srodki_wlasne_ue) as srodki_wlasne_ue, SUM(pl_budzety_wydatki.wspolfinansowanie_ue) as wspolfinansowanie_ue FROM pl_budzety_wydatki JOIN budzety ON pl_budzety_wydatki.rocznik=budzety.rok JOIN pl_budzety_wydatki_dzialy ON pl_budzety_wydatki.dzial_id=pl_budzety_wydatki_dzialy.id WHERE budzety.id=$id AND pl_budzety_wydatki.type='rozdzial' GROUP BY pl_budzety_wydatki.rozdzial_str ORDER BY plan DESC");

return $output;