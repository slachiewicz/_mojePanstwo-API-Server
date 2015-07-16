<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 15/07/15
 * Time: 10:45
 */


App::uses('ApplicationsController', 'Controller');

class BdlTempItemsController extends ApplicationsController
{

    public $components = array('RequestHandler');

    public $settings = array(
        'id' => 'bdl',
        'title' => 'Bdl',
        'subtitle' => 'Dane statystyczne o Polsce',
    );

    public function index()
    {
        $BdlTempItems = $this->BdlTempItem->find('all');
        $BdlImportItems = $this->BdlImportItem->find('all');
        $temp=array(
            'BdlTempItems'=>$BdlTempItems,
            'BdlImportItems'=>$BdlImportItems
        );
        $this->setSerialized('object', $temp);
    }

    public function view($id, $type)
    {
        if($type=='BDL'){
            $temp= $this->BdlTempItem->findById($id);
        }elseif($type=='import'){
            $temp= $this->BdlImportItem->findById($id);
        }
        $object=array(
            'id'=>$id,
            'BdlTempItem' => $temp
        );
        $this->setSerialized('object', $object);
    }


    public function save()
    {
        if($this->request->type=='BDL'){
            $this->BdlTempItem->create();
            if ($this->BdlTempItem->save($this->request->data)) {
                $message = 1;
            } else {
                $message = 0;
            }
        }elseif($this->request->type=='import'){
            $wskaznik = array();


            if ($_POST['id'] !== '') {
                $wskaznik['ImportedWskzaniki']['id'] = $_POST['id'];
            }
            $wskaznik['ImportedWskzaniki']['nazwa'] = $_POST['tytul'];
            $wskaznik['ImportedWskzaniki']['nazwa_skr'] = $_POST['tytul_skr'];
            $wskaznik['ImportedWskzaniki']['opis'] = $_POST['opis'];


            if ($_POST['url'] !== '') {
                $url = trim($_POST['url']);
                $url = str_replace('edit#', 'export?format=tsv&', $url);
                $url = filter_var($url, FILTER_SANITIZE_URL);

                //TODO: Poprawic na lepszego curla
                $curl_cmd = "curl '$url' -H 'accept-encoding: gzip, deflate, sdch' -H 'accept-language: en-US,en;q=0.8' -H 'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36' -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'authority: docs.google.com' -H 'cookie: PREF=ID=1111111111111111:FF=0:LD=pl:TM=1434983560:LM=1435156602:GM=1:S=U7BVBD-xdfI1ea9_; SID=DQAAAFACAABjwPnWqTfQg6mvy0iSTmtcVL1i3mweAsfg2vMMcmBxxZHwjpBbSWp0kMfNfOkzzi9sn1bdBdMtvBXve093HpTnWnCDH4jmGFye91VSRpeKEXELVeAn0zzumf_GPvFY8pHh8qFWdRyCnze3HG9w63JRPcXLuC113fWaemGjvC4tarRQ6nSFDRfjTgY1S8bwdZIa_xvnGOzYzVno1tnRD-TyEp_cBGrsPGfXH3eo-IwT1v2cNDM5eIZUA4IsBh_51s51AzCjQC3Wy7k1j--U36v4LWKx-GwHPuobdnsFMXShxBxLy1-nbBr3d7Mhr0z_2LUKr2KshdgXq8MakV2p7TY3M_MK3Gl8g81dQ0hYPlf3uWaaVS0zRbai1c_KAq07F5Mg8j9ER0fAWf9DPhulxfg-rHsgmPw3m6S9Vz5O4Vu76h3pUUHksUEJDKjfr9WjYTxdScMHXRlN2YwEo2eDSt_dsoKLBqDo5ITk0Z_U_2Q6cvvxzWzI2f865KT1fnwBbXIseOBwtmGZrcJLYq_51uTG3txZLvlGPLHwtbmiMuR_lrhW1DYMA-gpfE6wtz350fCbSG9uqUaYkwTmi-vAh7FB0XZS7Gnq_i1xTwlEWS5P4WgvUi3cMsj6MEg4vyvJbMxLt6GX57ys1b8oC9eg0grUHYy2964tz0o3bz6ZIuqnzy7934RHQmNa6z9KZ9GUfiOV11Su1q_cy2ZSOX8OcLX2ub_vUdQHKeYj_NfUtvJJj6NAUfrLzYy01kmSt28wH_HrpEWH21gnVrzdVrfUj9_T; HSID=AAPexiyDY4y4HLGP6; SSID=AQ-OLpYvLuG-isSOV; APISID=6SoNiL0ayM9-ip_B/AyzXYB4WEgYOnu8vS; SAPISID=qKVYlq89IxEysQ-V/AnvJbgvfdiwrTDPzF; WRITELY_SID=DQAAAFECAABHEIpKGfNhGz0lzc3hDgiEZa-vD-VQobO3DrO9_7ykPkV9MaVuxH6JmWk1dV2NEyAtIFIp70k0XyGZZ2mUqE4GWMSoVlQLB9GR09neYztLjWcjzmtkCS1pjL-phkMGrQLRJNbcsQYP2hbpEIOvPM7vdxgeCQY5Yz07shhvYzkEgNWc-wgsnmfrdM2-0fcPeYuNK7qqYYVqkqqP5FhjUL6BF0-MJU7sg2Prl61ciscyBAklHK2O3G5Ki9DjUeGWVqlBMUQ7H_z4vXLHNTQiK-77iS74eK-U40cAs6dAN2hxfzYaVlEVMwicqudVUSlL23q9-36mm-oH3fMgnBG9PpHpE-znN7Nxjd1CZoc9wYhPJKIcrAhx4tQguTSYvmltk6GdOI-TS6KQf76gW2E0qdqOh5TY8ibXPJV1Hh7ZTpMfdlwMKwA4ecLVGI9pIEZAJUAAhi4ik0D-KCkZyfN8LqsYlLB7TfdzLz0n4X_BULmyEyDzIMf39cw4S0TAFtzZyxPkSTDs9czPDpKTzA8Am1QXNeG4f9-z4jK6AIvQnT7E5v1HYThwWCtK3VN9FoXNz9NdSVoauPEsrbpAJL_t_wgt9wjCUo_i8fiHf_zA3z_0-0n8mATYTCUS21nuTGCcIg97rqnzO_hsfMb_ZknWKOiXPAJvlWZZ4EUI0o-zVW97ZcsQB89Ikun1FPnc0DBBENDLtfr3j1xCM3Az_WEskacLwdE--asGlidoTQeRGBfFAVHamG-3bZo-lGSR2LvvPfRNbIUhgGyGkHOGTmbaM8v-o3-JZyQ_MrGuQtOMd6g30Q; S=explorer=U9I3GFhNy5NKPOqqpsWrzw; NID=69=c998iVWZL271MHUl89PuZ95ufiaSfkrYZNmLNWB0mVrKOZQNcrXAMArdCkWy2lttlXsAX6VNuGteROU2eRlfYOy3sE7OPi0p1AL5R-Ab7t6_dDB1nTRK6MV-sgXWPQoXogIYkLKrQq8ltTbhxkjjuGl4Ehg3qnC7E8lfhwMJ31ohpP54zXfMJqeP_55ExsnLtycN' -H 'x-client-data: CIu2yQEIo7bJAQiptskBCMS2yQEI9IjKAQj+lsoB' --compressed";
                exec($curl_cmd, $tsv);
                foreach ($tsv as $row) {
                    $data[] = preg_split("/[\t]/", $row, 0, NULL);
                }

                $toDb = array();


                $cols = 0;
                foreach ($data[0] as $names) {
                    if (preg_match('/([0-9]{4,4})/', $names)) {
                        $cols++;
                    }
                }

                $this->BdlImportItem->saveMany($wskaznik, array('atomic'=>false));
                $wskaznik_id=$this->BdlImportItem->getLastInsertId();
                $index = 0;
                if(preg_match('wart',$data[0][1])) {
                    unset($data[0]);
                    // Dla tablic o kolumnach "terryt, wart, rocznik"
                    foreach($data as $row){
                        $toDb[] = array(
                            "wskaznik_id" => $wskaznik_id,
                            "terryt" => $row[0],
                            "wartosc" => $row[1],
                            "rocznik" => $data[2]
                        );
                    }
                }else {
                    // Dla tablic o kolumnach "terryt, rok1, rok2, ..., rokN"
                    foreach ($data as $row) {
                        if ($index != 0) {
                            for ($i = 1; $i < $cols + 1; $i++) {
                                $toDb[] = array(
                                    "wskaznik_id" => $wskaznik_id,
                                    "terryt" => $row[0],
                                    "wartosc" => $row[$i],
                                    "rocznik" => $data[0][$i]
                                );
                            }
                        };
                        $index++;
                    }
                }


                if ($this->BdlImportItemParts->saveMany($toDb, array('atomic'=>false))) {
                    $message=1;
                } else {
                    $message=0;
                }


            } else {
                $message=0;
            }
        }
        $this->setSerialized('object', $message);

    }

    public function listall()
    {
        $this->autoRender = false;
        $BdlTempItems = $this->BdlTempItem->find('list');
        $BdlImportItems = $this->BdlImportItem->find('list');
        $data=array(
            'BdlTempItems'=>$BdlTempItems,
            'BdlImportItems'=>$BdlImportItems
        );
        $this->setSerialized('object', $data);

    }

    public function delete($id)
    {
       if($this->request->type=='import') {
           $this->BdlTempItem->save(array(
               'id'=>$id,
               'deleted'=>1));
       }elseif($this->request->type=='BDL'){
           $this->BdlImportItem->save(array(
               'id'=>$id,
               'deleted'=>1));
       }

    }

}