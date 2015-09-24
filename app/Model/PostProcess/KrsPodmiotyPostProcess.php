<?

App::uses('Lib', 'MpUtils');

class KrsPodmiotyPostProcess {
    public static function mapFields(&$output) {
        // clean ---- fields
        foreach (array(
                     'krs_podmioty.email', 'krs_podmioty.www', 'krs_podmioty.adres_lokal', 'krs_podmioty.oznaczenie_sadu',
                     'krs_podmioty.wczesniejsza_rejestracja_str', 'krs_podmioty.cel_dzialania'
                 ) as $fld) {
            if (isset($output['data'][$fld])) {
                $output['data'][$fld] = MpUtils::clean_hyphens($output['data'][$fld]);
            }
        }

        if (isset($output['data']['krs_podmioty.' . 'wojewodztwo_id'])) {
            $output['data']['krs_podmioty.' . 'wojewodztwo_url'] = Dataobject::apiUrl('wojewodztwa', $output['data']['krs_podmioty.' . 'wojewodztwo_id']);
        }

        if (isset($output['data']['krs_podmioty.forma_prawna_typ_id'])) {
            $typ_id = $output['data']['krs_podmioty.forma_prawna_typ_id'];

            if ($typ_id == '1') {
                $output['data']['krs_podmioty.forma_prawna_kategoria'] = 'biznes';
            } else if ($typ_id == '2') {
                $output['data']['krs_podmioty.forma_prawna_kategoria'] = 'ngo';
            } else if ($typ_id == '3') {
                $output['data']['krs_podmioty.forma_prawna_kategoria'] = 'spzoz';
            }
        }

        if (isset($output['data']['krs_podmioty.forma_prawna_id'])) {
            $map = array(
                '40' => 'badawczo-rozwojowa',
                '19' => 'cech-rzemieslniczy',
                '31' => 'federacja-pracodawcow',
                '1' => 'fundacja',
                '46' => 'inst-gospodarki-budzet',
                '2' => 'instytut-badawczy',
                '3' => 'izba-gospodarcza',
                '4' => 'izba-rzemieslnicza',
                '5' => 'kolko-rolnicze',
                '35' => 'opp-inne',
                '34' => 'opp-kosciol',
                '36' => 'opp-kosciol-nop',
                '37' => 'opp-nop',
                '7' => 'przedsiebiorstwo-panstwowe',
                '6' => 'przedsieborca-zagraniczny-oddzial',
                '48' => 'skok',
                '47' => 'skok-blad',
                '10' => 'sp-akcyjna',
                '38' => 'sp-europejska',
                '11' => 'sp-jawna',
                '12' => 'sp-komandytowa',
                '32' => 'sp-komandytowo-akcyjna',
                '13' => 'sp-partnerska',
                '14' => 'sp-zoo',
                '9' => 'spoldzielnia',
                '39' => 'spzoz',
                '15' => 'stowarzyszenie',
                '49' => 'stowarzyszenie-jterenowa',
                '16' => 'stowarzyszenie-kult-fiz',
                '23' => 'stowarzyszenie-kult-fiz-krajowe',
                '50' => 'stowarzyszenie-ogrodowe',
                '41' => 'transport-sanitarny',
                '42' => 'tuw',
                '24' => 'zrzeszenie-handlu-uslug',
                '33' => 'zrzeszenie-handlu-uslug-repr',
                '30' => 'zrzeszenie-miedzybranzowe',
                '8' => 'zrzeszenie-rolnicze',
                '28' => 'zrzeszenie-rolnicze-zwiazek',
                '26' => 'zrzeszenie-transportu',
                '45' => 'zrzeszenie-transportu-repr',
                '21' => 'zwiazek-miedzybranzowy',
                '17' => 'zwiazek-pracodawcow',
                '43' => 'zwiazek-rzemiosla',
                '20' => 'zwiazek-sportowy',
                '27' => 'zwiazek-sportowy',
                '25' => 'zwiazek-stowarzyszen',
                '18' => 'zwiazek-zawodowy',
                '29' => 'zwiazek-zawodowy-rolnikow-ind',
                '22' => 'zwiazki-rolnikow',
                '44' => 'zzu-go'
            );

            $output['data']['krs_podmioty.forma_prawna'] = $map[$output['data']['krs_podmioty.forma_prawna_id']];
        }

        if (isset($output['data']['krs_podmioty.forma_prawna_str'])) {
            $output['data']['krs_podmioty.forma_prawna_str'] = ucwords(mb_strtolower($output['data']['krs_podmioty.forma_prawna_str']));
        }

        if (isset($output['data']['krs_podmioty.opp']) and in_array($output['data']['krs_podmioty.opp'], array('0', '1'))) {
            $output['data']['krs_podmioty.opp'] = $output['data']['krs_podmioty.opp'] == '1';
        }

        if (isset($output['data']['krs_podmioty.wykreslony']) and in_array($output['data']['krs_podmioty.wykreslony'], array('0', '1'))) {
            $output['data']['krs_podmioty.wykreslony'] = $output['data']['krs_podmioty.wykreslony'] == '1';
        }

        if (isset($output['data']['krs_podmioty.regon']) and $output['data']['krs_podmioty.regon'] == '0') {
            $output['data']['krs_podmioty.regon'] = null;
        }

        if (isset($output['data']['krs_podmioty.kod_pocztowy_id'])) {
            if ($output['data']['krs_podmioty.kod_pocztowy_id'] == '0') {
                $output['data']['krs_podmioty.adres_kod_pocztowy_url'] = null;

            } else {
                $output['data']['krs_podmioty.adres_kod_pocztowy_url'] = Dataobject::apiUrl('kody_pocztowe', $output['data']['krs_podmioty.kod_pocztowy_id']);
            }
        }
    }

    public static function mapConditions(&$conditions) {
        // TODO
    }
}