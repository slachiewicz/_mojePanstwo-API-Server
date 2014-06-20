<?php

class Sejmometr extends AppModel
{
    
    public $useDbConfig = 'solr';
    public $useTable = false;

    public function autorzy_projektow()
    {
		
		App::import('model','DB');
		$this->DB = new DB();
		
        $data = $this->DB->selectAssocs("SELECT `s_projekty_podmioty`.`podmiot_id` as 'podmiot_id', `s_podmioty`.`legislacja_typ_id` as 'typ_id', `s_podmioty`.`nazwa`, COUNT(*) as 'count' 
        	FROM `s_projekty_podmioty` 
			JOIN `s_projekty` 
			ON `s_projekty_podmioty`.`projekt_id` = `s_projekty`.`id` 
			JOIN `s_podmioty` 
			ON `s_projekty_podmioty`.`podmiot_id` = `s_podmioty`.`id`
			WHERE `s_projekty`.`akcept` = '1' 
			AND `s_projekty`.`typ_id` = 1
			GROUP BY `s_projekty_podmioty`.`podmiot_id` 
			ORDER BY COUNT(*) DESC
			LIMIT 100");
		
		return $data;

    }
    
    public function zawody($limit = null)
    {
		App::import('model','DB');
		$this->DB = new DB();

        // TODO nieznany tez?
		$count = $this->DB->selectValue("SELECT COUNT(*) FROM s_poslowie_kadencje WHERE pkw_zawod!=''");

        $sql = "SELECT COUNT( * ) AS  'count' ,  `pkw_zawod` as 'job'
			FROM  `s_poslowie_kadencje` 
			WHERE  `pkw_zawod` !=  ''
			GROUP BY  `pkw_zawod` 
			ORDER BY  `count` DESC";

        if ($limit != null) {
            $sql .= " LIMIT $limit";
        }

        $data = $this->DB->selectAssocs($sql);
		
		foreach( $data as &$d ) {
			$d['count'] = (int) $d['count'];
			$d['percent'] = round( 1000 * $d['count'] / $count ) / 10;
		}
		
		return $data;

    }
    
    public function latestData()
    {
	    
	    echo '{
    "projekty_ustaw": {
        "pagination": {
            "total": 1067
        },
        "dataobjects": [
            {
                "id": "12151285",
                "dataset": "prawo_projekty",
                "object_id": 2367,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Ruch Palikota\" src=\"http://resources.sejmometr.pl/podmioty/a/6/237.png\" /> <span>Ruch Palikota</span></li></ul>",
                    "autorzy_str": "Ruch Palikota",
                    "autor_typ_id": "5",
                    "data_start": "2014-06-17",
                    "data_status": "2014-06-18",
                    "dokument_id": "462614",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2367",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy przywrócenia stanu sprzed 2008 r. pozwalającego sprzedawać najemcom Poczty Polskiej S.A. zajmowane przez nich mieszkania po cenach preferencyjnych bez względu na przeznaczenie budynku w którym się znajdują",
                    "opis_skrocony": "Dotyczy przywrócenia stanu sprzed 2008 r. pozwalającego sprzedawać najemcom Poczty Polskiej S.A. zajmowane przez nich mieszkania po cenach preferencyjnych bez względu na przeznaczenie budynku w którym się znajdują",
                    "podrzedny": "0",
                    "przebieg_str": "18-06-2014 skierowany do opinii <a title=\"Biuro Legislacyjne Kancelarii Sejmu\">BL</a>;<BR>18-06-2014 skierowany do opinii <a title=\"Biuro Analiz Sejmowych Kancelarii Sejmu\">BAS</a> - zgodność z prawem UE",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem. <span class=\"_ds\" value=\"2014-06-18\">18 czerwca 2014 r.</span> skierowany do opinii BL.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o zmianie ustawy o komercjalizacji państwowego przedsiębiorstwa użyteczności publicznej „Poczta Polska”",
                    "tytul_skrocony": "o zmianie ustawy o komercjalizacji państwowego przedsiębiorstwa użyteczności publicznej „Poczta Polska”",
                    "autor_id": [
                        "237"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107344",
                "dataset": "prawo_projekty",
                "object_id": 2359,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Senat\" src=\"http://resources.sejmometr.pl/podmioty/a/6/60.png\" /> <span>Senat</span></li></ul>",
                    "autorzy_str": "Senat",
                    "autor_typ_id": "4",
                    "data_start": "2014-06-13",
                    "data_status": "2014-06-13",
                    "dokument_id": "462272",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2359",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy dostosowania systemu prawa do wyroku Trybunału Konstytucyjnego z dnia 8 października 2013 r., w którym mowa o wyznaczeniu obrońcy z urzędu",
                    "opis_skrocony": "Dotyczy dostosowania systemu prawa do wyroku Trybunału Konstytucyjnego z dnia 8 października 2013 r., w którym mowa o wyznaczeniu obrońcy z urzędu",
                    "podrzedny": "0",
                    "przebieg_str": "13-06-2014 skierowany do opinii <a title=\"Biuro Legislacyjne Kancelarii Sejmu\">BL</a>;<BR>13-06-2014 skierowany do opinii <a title=\"Biuro Analiz Sejmowych Kancelarii Sejmu\">BAS</a> - zgodność z prawem UE",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem. <span class=\"_ds\" value=\"2014-06-13\">13 czerwca 2014 r.</span> skierowany do opinii BL.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o zmianie ustawy – Kodeks postępowania karnego oraz niektórych innych ustaw",
                    "tytul_skrocony": "o zmianie ustawy – Kodeks postępowania karnego oraz niektórych innych ustaw",
                    "autor_id": [
                        "60"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107316",
                "dataset": "prawo_projekty",
                "object_id": 2327,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-06-12",
                    "data_status": "2014-06-12",
                    "dokument_id": "461850",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2327",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy problematyki związanej z zapobieganiem, zwalczaniem oraz ściganiem przemocy wobec kobiet, w tym przemocy domowej",
                    "opis_skrocony": "Dotyczy problematyki związanej z zapobieganiem, zwalczaniem oraz ściganiem przemocy wobec kobiet, w tym przemocy domowej",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o ratyfikacji Konwencji Rady Europy o zapobieganiu i zwalczaniu przemocy wobec kobiet i przemocy domowej, sporządzonej w Stambule dnia 11 maja 2011 r.",
                    "tytul_skrocony": "o ratyfikacji Konwencji Rady Europy o zapobieganiu i zwalczaniu przemocy wobec kobiet i przemocy domowej, sporządzonej w Stambule dnia 11 maja 2011 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107315",
                "dataset": "prawo_projekty",
                "object_id": 2326,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-06-12",
                    "data_status": "2014-06-12",
                    "dokument_id": "461849",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2326",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy związania Rzeczypospolitej Polskiej Traktatem w celu zapewnienia międzynarodowego pokoju i bezpieczeństwa oraz upowszechniania zasad przestrzegania praw człowieka i międzynarodowego prawa humanitarnego w związku z prawnie wiążącymi standardami międzynarodowymi w handlu bronią konwencjonalną, zapobiegając i eliminując nielegalny handel",
                    "opis_skrocony": "Dotyczy związania Rzeczypospolitej Polskiej Traktatem w celu zapewnienia międzynarodowego pokoju i bezpieczeństwa oraz upowszechniania zasad przestrzegania praw człowieka i międzynarodowego prawa humanitarnego w związku z prawnie wiążącymi standardami międzynarodowymi w handlu bronią...",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o ratyfikacji Traktatu o handlu bronią, sporządzonego w Nowym Jorku dnia 2 kwietnia 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Traktatu o handlu bronią, sporządzonego w Nowym Jorku dnia 2 kwietnia 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107314",
                "dataset": "prawo_projekty",
                "object_id": 2325,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-06-12",
                    "data_status": "2014-06-12",
                    "dokument_id": "461848",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2325",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy promowania wszechstronnego i zrównoważonego wykorzystania energii odnawialnej",
                    "opis_skrocony": "Dotyczy promowania wszechstronnego i zrównoważonego wykorzystania energii odnawialnej",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy o przywilejach i immunitetach Międzynarodowej Agencji Energii Odnawialnej, sporządzonej w Abu Zabi dnia 13 stycznia 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy o przywilejach i immunitetach Międzynarodowej Agencji Energii Odnawialnej, sporządzonej w Abu Zabi dnia 13 stycznia 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107307",
                "dataset": "prawo_projekty",
                "object_id": 2300,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość",
                    "autor_typ_id": "5",
                    "data_start": "2014-06-12",
                    "data_status": "2014-06-13",
                    "dokument_id": "462034",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2300",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy zachowania po dniu 1 lipca 2014 r. skróconego czasu pracy przez pracowników wybranych zakładów i pracowni leczniczych (radiologii, radioterapii, medycyny nuklearnej)",
                    "opis_skrocony": "Dotyczy zachowania po dniu 1 lipca 2014 r. skróconego czasu pracy przez pracowników wybranych zakładów i pracowni leczniczych (radiologii, radioterapii, medycyny nuklearnej)",
                    "podrzedny": "0",
                    "przebieg_str": "13-06-2014 skierowany do opinii <a title=\"Biuro Legislacyjne Kancelarii Sejmu\">BL</a>;<BR>13-06-2014 skierowany do opinii <a title=\"Biuro Analiz Sejmowych Kancelarii Sejmu\">BAS</a> - zgodność z prawem UE",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem. <span class=\"_ds\" value=\"2014-06-13\">13 czerwca 2014 r.</span> skierowany do opinii BL.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o zmianie ustawy o działalności leczniczej",
                    "tytul_skrocony": "o zmianie ustawy o działalności leczniczej",
                    "autor_id": [
                        "8"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12127318",
                "dataset": "prawo_projekty",
                "object_id": 2362,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-06-11",
                    "data_status": "2014-06-17",
                    "dokument_id": "462364",
                    "druki_str": "Druk nr <b>2493</b>.",
                    "faza_id": "2",
                    "id": "2362",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-06-17\">17 czerwca 2014 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o zmianie ustawy o wyrobach budowlanych oraz ustawy - Prawo budowlane",
                    "tytul_skrocony": "o zmianie ustawy o wyrobach budowlanych oraz ustawy - Prawo budowlane",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12127316",
                "dataset": "prawo_projekty",
                "object_id": 2361,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-06-11",
                    "data_status": "2014-06-17",
                    "dokument_id": "462367",
                    "druki_str": "Druk nr <b>2494</b>.",
                    "faza_id": "2",
                    "id": "2361",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-06-17\">17 czerwca 2014 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o zmianie ustawy o autostradach płatnych oraz o Krajowym Funduszu Drogowym, ustawy o Funduszu Kolejowym oraz ustawy o podatku akcyzowym",
                    "tytul_skrocony": "o zmianie ustawy o autostradach płatnych oraz o Krajowym Funduszu Drogowym, ustawy o Funduszu Kolejowym oraz ustawy o podatku akcyzowym",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12127321",
                "dataset": "prawo_projekty",
                "object_id": 2363,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-06-11",
                    "data_status": "2014-06-17",
                    "dokument_id": "462359",
                    "druki_str": "Druk nr <b>2492</b>.",
                    "faza_id": "2",
                    "id": "2363",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-06-17\">17 czerwca 2014 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "1",
                    "tytul": "Projekt ustawy o zmianie ustawy o rolnictwie ekologicznym",
                    "tytul_skrocony": "o zmianie ustawy o rolnictwie ekologicznym",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=1"
    },
    "projekty_uchwal": {
        "pagination": {
            "total": 235
        },
        "dataobjects": [
            {
                "id": "12127372",
                "dataset": "prawo_projekty",
                "object_id": 2366,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Ruch Palikota\" src=\"http://resources.sejmometr.pl/podmioty/a/6/237.png\" /> <span>Ruch Palikota</span></li></ul>",
                    "autorzy_str": "Ruch Palikota",
                    "autor_typ_id": "5",
                    "data_start": "2014-06-17",
                    "data_status": "2014-06-17",
                    "dokument_id": "462583",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2366",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy skrócenia kadencji Sejmu Rzeczypospolitej Polskiej.",
                    "opis_skrocony": "Dotyczy skrócenia kadencji Sejmu Rzeczypospolitej Polskiej.",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie skrócenia kadencji Sejmu Rzeczypospolitej Polskiej",
                    "tytul_skrocony": "w sprawie skrócenia kadencji Sejmu Rzeczypospolitej Polskiej",
                    "autor_id": [
                        "237"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12127345",
                "dataset": "prawo_projekty",
                "object_id": 2365,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość",
                    "autor_typ_id": "5",
                    "data_start": "2014-06-16",
                    "data_status": "2014-06-16",
                    "dokument_id": "462361",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2365",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy upamiętnienia postaci Romualda Traugutta w 150 rocznicę jego śmierci.",
                    "opis_skrocony": "Dotyczy upamiętnienia postaci Romualda Traugutta w 150 rocznicę jego śmierci.",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie upamiętnienia 150 rocznicy śmierci Romualda Traugutta",
                    "tytul_skrocony": "w sprawie upamiętnienia 150 rocznicy śmierci Romualda Traugutta",
                    "autor_id": [
                        "8"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12127323",
                "dataset": "prawo_projekty",
                "object_id": 2364,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość",
                    "autor_typ_id": "5",
                    "data_start": "2014-06-16",
                    "data_status": "2014-06-16",
                    "dokument_id": "462360",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2364",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy oddania hołdu ofiarom Obławy Augustynowskiej.",
                    "opis_skrocony": "Dotyczy oddania hołdu ofiarom Obławy Augustynowskiej.",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie uczczenia pamięci ofiar Obławy Augustynowskiej z lipca 1945 roku",
                    "tytul_skrocony": "w sprawie uczczenia pamięci ofiar Obławy Augustynowskiej z lipca 1945 roku",
                    "autor_id": [
                        "8"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107345",
                "dataset": "prawo_projekty",
                "object_id": 2360,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość",
                    "autor_typ_id": "5",
                    "data_start": "2014-06-13",
                    "data_status": "2014-06-13",
                    "dokument_id": "462273",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2360",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie powołania komisji śledczej do zbadania zasadności, legalności i prawidłowości działań organów podległych Ministrowi Sprawiedliwości - Prokuratorowi Generalnemu podejmowanych w latach 2007-2010 w związku z podejrzeniem posiadania nieujawnionych i nielegalnych źródeł dochodu przez Jolantę i Aleksandra Kwaśniewskich w ramach postępowania przygotowawczego prowadzonego przez Prokuraturę Apelacyjną w Katowicach, a także związanymi z tym działaniami i czynnościami podejmowanymi przez Prezesa Rady Ministrów, Centralne Biuro Antykorupcyjne, organy administracji rządowej podległe Ministrowi Finansów, a także w sprawie zbadania okoliczności wszczęcia i przebiegu śledztwa oraz sporządzenia i przedstawienia wniosku Prokuratora Okręgowego Warszawa-Praga w Warszawie o wyrażenie zgody przez Sejm RP na pociągnięcie do odpowiedzialności karnej posła na Sejm RP Mariusza Kamińskiego",
                    "tytul_skrocony": "w sprawie powołania komisji śledczej do zbadania zasadności, legalności i prawidłowości działań organów podległych Ministrowi Sprawiedliwości - Prokuratorowi Generalnemu podejmowanych w latach 2007-2010 w związku z podejrzeniem posiadania nieujawnionych i nielegalnych źródeł dochodu przez Jolantę i Aleksandra Kwaśniewskich w ramach postępowania przygotowawczego prowadzonego przez Prokuraturę Apelacyjną w Katowicach, a także związanymi z tym działaniami i czynnościami podejmowanymi przez Prezesa Rady Ministrów, Centralne Biuro Antykorupcyjne, organy administracji rządowej podległe Ministrowi Finansów, a także w sprawie zbadania okoliczności wszczęcia i przebiegu śledztwa oraz sporządzenia i przedstawienia wniosku Prokuratora Okręgowego Warszawa-Praga w Warszawie o wyrażenie zgody przez Sejm RP na pociągnięcie do odpowiedzialności karnej posła na Sejm RP Mariusza Kamińskiego",
                    "autor_id": [
                        "8"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107300",
                "dataset": "prawo_projekty",
                "object_id": 2293,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość",
                    "autor_typ_id": "5",
                    "data_start": "2014-06-04",
                    "data_status": "2014-06-04",
                    "dokument_id": "461066",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2293",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie upamiętnienia NSZZ „Solidarność” i rządu Premiera Jana Olszewskiego",
                    "tytul_skrocony": "w sprawie upamiętnienia NSZZ „Solidarność” i rządu Premiera Jana Olszewskiego",
                    "autor_id": [
                        "8"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107279",
                "dataset": "prawo_projekty",
                "object_id": 2251,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość",
                    "autor_typ_id": "5",
                    "data_start": "2014-05-08",
                    "data_status": "2014-05-08",
                    "dokument_id": "456495",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2251",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Dotyczy upamiętnienia 70 rocznicy zwycięskiej bitwy o Monte Cassino",
                    "opis_skrocony": "Dotyczy upamiętnienia 70 rocznicy zwycięskiej bitwy o Monte Cassino",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie upamiętnienia 70 rocznicy zwycięskiej bitwy o Monte Cassino",
                    "tytul_skrocony": "w sprawie upamiętnienia 70 rocznicy zwycięskiej bitwy o Monte Cassino",
                    "autor_id": [
                        "8"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107271",
                "dataset": "prawo_projekty",
                "object_id": 2243,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Sojusz Lewicy Demokratycznej\" src=\"http://resources.sejmometr.pl/podmioty/a/6/132.png\" /> <span>Sojusz Lewicy Demokratycznej</span></li></ul>",
                    "autorzy_str": "Sojusz Lewicy Demokratycznej",
                    "autor_typ_id": "5",
                    "data_start": "2014-04-22",
                    "data_status": "2014-04-22",
                    "dokument_id": "454981",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2243",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie 10. rocznicy członkostwa Polski w Unii Europejskiej",
                    "tytul_skrocony": "w sprawie 10. rocznicy członkostwa Polski w Unii Europejskiej",
                    "autor_id": [
                        "132"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107267",
                "dataset": "prawo_projekty",
                "object_id": 2238,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Sojusz Lewicy Demokratycznej\" src=\"http://resources.sejmometr.pl/podmioty/a/6/132.png\" /> <span>Sojusz Lewicy Demokratycznej</span></li></ul>",
                    "autorzy_str": "Sojusz Lewicy Demokratycznej",
                    "autor_typ_id": "5",
                    "data_start": "2014-04-09",
                    "data_status": "2014-04-09",
                    "dokument_id": "453338",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2238",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Sejm RP wyraża żołnierzom i pracownikom Wojskowych Służb Informacyjnych podziękowanie za ofiarną służbę Ojczyźnie",
                    "opis_skrocony": "Sejm RP wyraża żołnierzom i pracownikom Wojskowych Służb Informacyjnych podziękowanie za ofiarną służbę Ojczyźnie",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie ochrony dobrego imienia żołnierzy i pracowników Wojskowych Służb<br/>Informacyjnych",
                    "tytul_skrocony": "w sprawie ochrony dobrego imienia żołnierzy i pracowników Wojskowych Służb<br/>Informacyjnych",
                    "autor_id": [
                        "132"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107262",
                "dataset": "prawo_projekty",
                "object_id": 2233,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Polskie Stronnictwo Ludowe\" src=\"http://resources.sejmometr.pl/podmioty/a/6/7.png\" /> <span>Polskie Stronnictwo Ludowe</span></li></ul>",
                    "autorzy_str": "Polskie Stronnictwo Ludowe",
                    "autor_typ_id": "5",
                    "data_start": "2014-04-03",
                    "data_status": "2014-04-10",
                    "dokument_id": "452421",
                    "druki_str": "",
                    "faza_id": "2",
                    "id": "2233",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "Sejm wyraża nadzieję, że kanonizacja Ojca Świętego Jana Pawła II będzie dla wszystkich Polaków okazją do radosnego i solidarnego świętowania.",
                    "opis_skrocony": "Sejm wyraża nadzieję, że kanonizacja Ojca Świętego Jana Pawła II będzie dla wszystkich Polaków okazją do radosnego i solidarnego świętowania.",
                    "podrzedny": "0",
                    "przebieg_str": "10-04-2014 skierowany do uzgodnienia tekstu z klubami (upoważniono Wicemarszałka Sejmu Eugeniusza Grzeszczaka)",
                    "status_id": "10",
                    "status_str": "Przed pierwszym czytaniem. <span class=\"_ds\" value=\"2014-04-10\">10 kwietnia 2014 r.</span> skierowany do uzgodnienia tekstu z klubami.",
                    "typ_id": "2",
                    "tytul": "Projekt uchwały w sprawie uczczenia papieża bł. Jana Pawła II w dniu Jego kanonizacji",
                    "tytul_skrocony": "w sprawie uczczenia papieża bł. Jana Pawła II w dniu Jego kanonizacji",
                    "autor_id": [
                        "7"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=2"
    },
    "sprawozdania_kontrolne": {
        "pagination": {
            "total": 111
        },
        "dataobjects": [
            {
                "id": "12106974",
                "dataset": "prawo_projekty",
                "object_id": 1787,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_start": "2013-07-20",
                    "data_status": "2013-09-12",
                    "dokument_id": "415104",
                    "druki_str": "Druki nr <b>1615</b>, <b>1710</b>.",
                    "faza_id": "2",
                    "id": "1787",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-09-12\">12 września 2013 r.</span> komisja zarekomendowała przyjęcie sprawozdania.",
                    "typ_id": "11",
                    "tytul": "Sprawozdanie finansowe Narodowego Funduszu Zdrowia za rok 2012",
                    "tytul_skrocony": "Sprawozdanie finansowe Narodowego Funduszu Zdrowia za rok 2012"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106975",
                "dataset": "prawo_projekty",
                "object_id": 1788,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2014-04-04",
                    "data_start": "2013-07-19",
                    "data_status": "2014-04-04",
                    "dokument_id": "443629",
                    "druki_str": "Druki nr <b>1614</b>, <b>2125</b>.",
                    "faza_id": "2",
                    "id": "1788",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2014-04-04\">4 kwietnia 2014 r.</span>",
                    "typ_id": "11",
                    "tytul": "Rządowy dokument: Informacja o realizacji działań wynikających z Narodowego Programu Ochrony Zdrowia Psychicznego w 2011 roku",
                    "tytul_skrocony": "Rządowy dokument: Informacja o realizacji działań wynikających z Narodowego Programu Ochrony Zdrowia Psychicznego w 2011 roku"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106954",
                "dataset": "prawo_projekty",
                "object_id": 1765,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2014-02-07",
                    "data_start": "2013-07-18",
                    "data_status": "2014-02-07",
                    "dokument_id": "421604",
                    "druki_str": "Druki nr <b>1597</b>, <b>1819</b>.",
                    "faza_id": "2",
                    "id": "1765",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2014-02-07\">7 lutego 2014 r.</span>",
                    "typ_id": "11",
                    "tytul": "Rządowy dokument: \"Informacja o realizacji działań wynikających z Krajowego Programu Przeciwdziałania Narkomanii w 2011 roku\"",
                    "tytul_skrocony": "Rządowy dokument: \"Informacja o realizacji działań wynikających z Krajowego Programu Przeciwdziałania Narkomanii w 2011 roku\""
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106953",
                "dataset": "prawo_projekty",
                "object_id": 1764,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_start": "2013-07-11",
                    "data_status": "2013-07-17",
                    "dokument_id": "370949",
                    "druki_str": "Druk nr <b>1561</b>.",
                    "faza_id": "2",
                    "id": "1764",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-07-17\">17 lipca 2013 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "11",
                    "tytul": "Sprawozdanie komisji o poselskim projekcie ustawy o zmianie ustawy o współpracy rozwojowej",
                    "tytul_skrocony": "o poselskim projekcie ustawy o zmianie ustawy o współpracy rozwojowej"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106940",
                "dataset": "prawo_projekty",
                "object_id": 1738,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_start": "2013-06-20",
                    "data_status": "2013-08-28",
                    "dokument_id": "402270",
                    "druki_str": "Druki nr <b>1543</b>, <b>1668</b>.",
                    "faza_id": "2",
                    "id": "1738",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-08-28\">28 sierpnia 2013 r.</span> komisja przedstawiła stanowisko.",
                    "typ_id": "11",
                    "tytul": "Raport z działalności Agencji Nieruchomości Rolnych na Zasobie Własności Rolnej Skarbu Państwa w 2012 r.",
                    "tytul_skrocony": "Raport z działalności Agencji Nieruchomości Rolnych na Zasobie Własności Rolnej Skarbu Państwa w 2012 r."
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106914",
                "dataset": "prawo_projekty",
                "object_id": 1697,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2013-07-24",
                    "data_start": "2013-06-12",
                    "data_status": "2013-07-24",
                    "dokument_id": "370769",
                    "druki_str": "Druki nr <b>1462</b>, <b>1556</b>.",
                    "faza_id": "2",
                    "id": "1697",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-07-24\">24 lipca 2013 r.</span>",
                    "typ_id": "11",
                    "tytul": "Sprawozdanie z działalności Najwyższej Izby Kontroli w 2012 r.",
                    "tytul_skrocony": "Sprawozdanie z działalności Najwyższej Izby Kontroli w 2012 r."
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106912",
                "dataset": "prawo_projekty",
                "object_id": 1695,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_start": "2013-06-12",
                    "data_status": "2013-06-18",
                    "dokument_id": "367548",
                    "druki_str": "Druk nr <b>1457</b>.",
                    "faza_id": "2",
                    "id": "1695",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-06-18\">18 czerwca 2013 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "11",
                    "tytul": "Przedstawiona przez Prezesa Najwyższej Izby Kontroli \"Analiza wykonania budżetu państwa i założeń polityki pieniężnej w 2012 roku\"",
                    "tytul_skrocony": "Przedstawiona przez Prezesa Najwyższej Izby Kontroli \"Analiza wykonania budżetu państwa i założeń polityki pieniężnej w 2012 roku\""
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106917",
                "dataset": "prawo_projekty",
                "object_id": 1700,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2014-03-14",
                    "data_start": "2013-06-06",
                    "data_status": "2014-03-14",
                    "dokument_id": "371707",
                    "druki_str": "Druki nr <b>1478</b>, <b>1578</b>.",
                    "faza_id": "2",
                    "id": "1700",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2014-03-14\">14 marca 2014 r.</span>",
                    "typ_id": "11",
                    "tytul": "Rządowy dokument: Stan bezpieczeństwa ruchu drogowego oraz działania realizowane w tym zakresie w 2012 r.",
                    "tytul_skrocony": "Rządowy dokument: Stan bezpieczeństwa ruchu drogowego oraz działania realizowane w tym zakresie w 2012 r."
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106921",
                "dataset": "prawo_projekty",
                "object_id": 1704,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2014-04-04",
                    "data_start": "2013-05-31",
                    "data_status": "2014-04-04",
                    "dokument_id": "448768",
                    "druki_str": "Druki nr <b>1449</b>, <b>2211</b>.",
                    "faza_id": "2",
                    "id": "1704",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2014-04-04\">4 kwietnia 2014 r.</span>",
                    "typ_id": "11",
                    "tytul": "Przedstawiona przez Ministra Środowiska \"Informacja o realizacji Programu budowy Zbiornika Wodnego Świnna Poręba w latach 2006-2013 w roku 2012\"",
                    "tytul_skrocony": "Przedstawiona przez Ministra Środowiska \"Informacja o realizacji Programu budowy Zbiornika Wodnego Świnna Poręba w latach 2006-2013 w roku 2012\""
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=11"
    },
    "umowy": {
        "pagination": {
            "total": 78
        },
        "dataobjects": [
            {
                "id": "12107291",
                "dataset": "prawo_projekty",
                "object_id": 2284,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-05-12",
                    "data_status": "2014-06-05",
                    "dokument_id": "461215",
                    "druki_str": "Druki nr <b>2416</b>, <b>2463</b>.",
                    "faza_id": "2",
                    "id": "2284",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy wprowadzenia skutecznego narzędzia weryfikacji deklarowanych podstaw opodatkowania przez podatników osiągających dochody w relacjach z Kajmanami i stanowi ważny krok w kierunku rozwoju dwustronnej współpracy podatkowej , która może stanowić podstawę do podjęcia rozmów zmierzających do zawarcia pełnej umowy o unikaniu podwójnego opodatkowania z Kajmanami.</p>",
                    "opis_skrocony": "<p>Dotyczy wprowadzenia skutecznego narzędzia weryfikacji deklarowanych podstaw opodatkowania przez podatników osiągających dochody w relacjach z Kajmanami i stanowi ważny krok w kierunku rozwoju dwustronnej współpracy podatkowej , która może stanowić podstawę do podjęcia rozmów...",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-06-05\">5 czerwca 2014 r.</span> komisja zarekomendowała przyjęcie projektu bez poprawek.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy między Rzecząpospolitą Polską a Kajmanami o wymianie informacji w sprawach podatkowych, podpisanej w Londynie dnia 29 listopada 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy między Rzecząpospolitą Polską a Kajmanami o wymianie informacji w sprawach podatkowych, podpisanej w Londynie dnia 29 listopada 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107290",
                "dataset": "prawo_projekty",
                "object_id": 2283,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-05-06",
                    "data_status": "2014-05-28",
                    "dokument_id": "459927",
                    "druki_str": "Druki nr <b>2379</b>, <b>2431</b>.",
                    "faza_id": "2",
                    "id": "2283",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy zawarcia umowy na podstawie Modelowej Umowy OECD o wymianie informacji w sprawach podatkowych - w przypadku Polski będzie to podatek dochodowy od osób prawnych oraz podatek dochodowy od osób fizycznych zaś w przypadku Bermudów będą to podatki bezpośrednie, bez względu na rodzaj i nazwę.</p>",
                    "opis_skrocony": "<p>Dotyczy zawarcia umowy na podstawie Modelowej Umowy OECD o wymianie informacji w sprawach podatkowych - w przypadku Polski będzie to podatek dochodowy od osób prawnych oraz podatek dochodowy od osób fizycznych zaś w przypadku Bermudów będą to podatki bezpośrednie, bez względu na...",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-05-28\">28 maja 2014 r.</span> komisja zarekomendowała przyjęcie projektu bez poprawek.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy między Rządem Rzeczypospolitej Polskiej a Rządem Bermudów (zgodnie z upoważnieniem Rządu Zjednoczonego Królestwa Wielkiej Brytanii i Irlandii Północnej) o wymianie informacji w sprawach podatkowych oraz Uzgodnień końcowych dotyczących interpretacji oraz stosowania Umowy, podpisanych w Londynie dnia 25 listopada 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy między Rządem Rzeczypospolitej Polskiej a Rządem Bermudów (zgodnie z upoważnieniem Rządu Zjednoczonego Królestwa Wielkiej Brytanii i Irlandii Północnej) o wymianie informacji w sprawach podatkowych oraz Uzgodnień końcowych dotyczących interpretacji oraz stosowania Umowy, podpisanych w Londynie dnia 25 listopada 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107289",
                "dataset": "prawo_projekty",
                "object_id": 2282,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-05-06",
                    "data_status": "2014-05-28",
                    "dokument_id": "459926",
                    "druki_str": "Druki nr <b>2380</b>, <b>2432</b>.",
                    "faza_id": "2",
                    "id": "2282",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy zawarcia umowy na podstawie Modelowej Umowy OECD o wymianie informacji w sprawach podatkowych - w przypadku Polski będzie to podatek dochodowy od osób prawnych, podatek dochodowy od osób fizycznych oraz podatek od towarów i usług zaś w przypadku Brytyjskich Wysp Dziewiczych będzie to podatek dochodowy, podatek od wynagrodzeń oraz podatek od majątku.</p>",
                    "opis_skrocony": "<p>Dotyczy zawarcia umowy na podstawie Modelowej Umowy OECD o wymianie informacji w sprawach podatkowych - w przypadku Polski będzie to podatek dochodowy od osób prawnych, podatek dochodowy od osób fizycznych oraz podatek od towarów i usług zaś w przypadku Brytyjskich Wysp Dziewiczych...",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-05-28\">28 maja 2014 r.</span> komisja zarekomendowała przyjęcie projektu bez poprawek.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy między Rządem Rzeczypospolitej Polskiej a Rządem Brytyjskich Wysp Dziewiczych o wymianie informacji w sprawach podatkowych oraz Protokołu i Wspólnej Deklaracji Rządu Rzeczypospolitej Polskiej i Rządu Brytyjskich Wysp Dziewiczych, podpisanych w Londynie dnia 28 listopada 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy między Rządem Rzeczypospolitej Polskiej a Rządem Brytyjskich Wysp Dziewiczych o wymianie informacji w sprawach podatkowych oraz Protokołu i Wspólnej Deklaracji Rządu Rzeczypospolitej Polskiej i Rządu Brytyjskich Wysp Dziewiczych, podpisanych w Londynie dnia 28 listopada 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107288",
                "dataset": "prawo_projekty",
                "object_id": 2281,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-05-06",
                    "data_status": "2014-05-28",
                    "dokument_id": "459928",
                    "druki_str": "Druki nr <b>2378</b>, <b>2430</b>.",
                    "faza_id": "2",
                    "id": "2281",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy dostosowania postanowień Umowy określających zasady opodatkowania zysków przedsiębiorstw oraz wymiany informacji podatkowych do Modelowej Konwencji OECD oraz szeregu technicznych kwestii wypływających ze stosowania umowy w zmieniających się uwarunkowaniach gospodarczych.</p>",
                    "opis_skrocony": "<p>Dotyczy dostosowania postanowień Umowy określających zasady opodatkowania zysków przedsiębiorstw oraz wymiany informacji podatkowych do Modelowej Konwencji OECD oraz szeregu technicznych kwestii wypływających ze stosowania umowy w zmieniających się uwarunkowaniach gospodarczych.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-05-28\">28 maja 2014 r.</span> komisja zarekomendowała przyjęcie projektu bez poprawek.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Protokołu między Rządem Rzeczypospolitej Polskiej a Rządem Zjednoczonych Emiratów Arabskich zmieniającego Umowę między Rządem Rzeczypospolitej Polskiej a Rządem Zjednoczonych Emiratów Arabskich w sprawie unikania podwójnego opodatkowania i zapobiegania uchylaniu się od opodatkowania w zakresie podatków od dochodu i majątku, sporządzoną w Abu Zabi dnia 31 stycznia 1993 r., oraz Protokół, sporządzony w Abu Zabi dnia 31 stycznia 1993 r., podpisanego w Abu Zabi dnia 11 g",
                    "tytul_skrocony": "o ratyfikacji Protokołu między Rządem Rzeczypospolitej Polskiej a Rządem Zjednoczonych Emiratów Arabskich zmieniającego Umowę między Rządem Rzeczypospolitej Polskiej a Rządem Zjednoczonych Emiratów Arabskich w sprawie unikania podwójnego opodatkowania i zapobiegania uchylaniu się od opodatkowania w zakresie podatków od dochodu i majątku, sporządzoną w Abu Zabi dnia 31 stycznia 1993 r., oraz Protokół, sporządzony w Abu Zabi dnia 31 stycznia 1993 r., podpisanego w Abu Zabi dnia 11 grudnia 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107252",
                "dataset": "prawo_projekty",
                "object_id": 2205,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_start": "2014-03-20",
                    "data_status": "2014-04-23",
                    "dokument_id": "455142",
                    "druki_str": "Druki nr <b>2246</b>, <b>2317</b>.",
                    "faza_id": "2",
                    "id": "2205",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Umowa została opracowana przez stronę polską i reguluje kwestie współpracy operacyjnej właściwych polskich organów państwowych z ich chorwackimi odpowiednikami, mającej na celu zwalczanie przestępczości przez jej zapobieganie i ujawnianie sprawców przestępstw.</p>",
                    "opis_skrocony": "<p>Umowa została opracowana przez stronę polską i reguluje kwestie współpracy operacyjnej właściwych polskich organów państwowych z ich chorwackimi odpowiednikami, mającej na celu zwalczanie przestępczości przez jej zapobieganie i ujawnianie sprawców przestępstw.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2014-04-23\">23 kwietnia 2014 r.</span> komisja zarekomendowała przyjęcie projektu bez poprawek.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy między Rządem Rzeczypospolitej Polskiej a Rządem Republiki Chorwacji o współpracy w zwalczaniu przestępczości, podpisanej w Dubrowniku dnia 9 lipca 2010 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy między Rządem Rzeczypospolitej Polskiej a Rządem Republiki Chorwacji o współpracy w zwalczaniu przestępczości, podpisanej w Dubrowniku dnia 9 lipca 2010 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107239",
                "dataset": "prawo_projekty",
                "object_id": 2188,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_przyjecia": "2014-05-09",
                    "data_start": "2014-03-11",
                    "data_status": "2014-05-09",
                    "dokument_id": "455140",
                    "druki_str": "Druki nr <b>2247</b>, <b>2318</b>.",
                    "faza_id": "3",
                    "id": "2188",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Umowa, określając zasady wzajemnego przekazywania skazanych, stworzy podstawy umożliwiające obywatelom RP skazanym w Brazylii powrót do kraju.</p>",
                    "opis_skrocony": "<p>Umowa, określając zasady wzajemnego przekazywania skazanych, stworzy podstawy umożliwiające obywatelom RP skazanym w Brazylii powrót do kraju.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "60",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2014-05-09\">9 maja 2014 r.</span>. Oczekuje na stanowisko Senatu",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy między Rzecząpospolitą Polską a Federacyjną Republiką Brazylii o przekazywaniu osób skazanych, podpisanej w Brasilii dnia 26 listopada 2012 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy między Rzecząpospolitą Polską a Federacyjną Republiką Brazylii o przekazywaniu osób skazanych, podpisanej w Brasilii dnia 26 listopada 2012 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107188",
                "dataset": "prawo_projekty",
                "object_id": 2126,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_przyjecia": "2014-03-14",
                    "data_start": "2014-01-23",
                    "data_status": "2014-05-23",
                    "dokument_id": "456521",
                    "druki_str": "Druki nr <b>2090</b>, <b>2155</b>.",
                    "faza_id": "3",
                    "id": "2126",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy wprowadzenia efektywnych mechanizmów unikania podwójnego opodatkowania dochodów z pracy najemnej oraz emerytur i rent, uzyskiwanych przez osoby fizyczne pracujące w relacjach Polska-Guernsey.</p>",
                    "opis_skrocony": "<p>Dotyczy wprowadzenia efektywnych mechanizmów unikania podwójnego opodatkowania dochodów z pracy najemnej oraz emerytur i rent, uzyskiwanych przez osoby fizyczne pracujące w relacjach Polska-Guernsey.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "40",
                    "status_str": "Ustawa obowiązująca. <span class=\"_ds\" value=\"2014-05-23\">23 maja 2014 r.</span> przepisy weszły w życie.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy między Rzecząpospolitą Polską a Baliwatem Guernsey w sprawie unikania podwójnego opodatkowania niektórych kategorii dochodów osób fizycznych, podpisanej w Londynie dnia 8 października 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy między Rzecząpospolitą Polską a Baliwatem Guernsey w sprawie unikania podwójnego opodatkowania niektórych kategorii dochodów osób fizycznych, podpisanej w Londynie dnia 8 października 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107186",
                "dataset": "prawo_projekty",
                "object_id": 2124,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_przyjecia": "2014-03-14",
                    "data_start": "2014-01-23",
                    "data_status": "2014-05-23",
                    "dokument_id": "456522",
                    "druki_str": "Druki nr <b>2091</b>, <b>2156</b>.",
                    "faza_id": "3",
                    "id": "2124",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy aktualizacji Konwencji, m. in. w zakresie postanowień dotyczących wymiany informacji podatkowych i wprowadzenia technicznych zmian dostosowujących Konwencję do aktualnej wersji Modelowej Konwencji OECD.</p>",
                    "opis_skrocony": "<p>Dotyczy aktualizacji Konwencji, m. in. w zakresie postanowień dotyczących wymiany informacji podatkowych i wprowadzenia technicznych zmian dostosowujących Konwencję do aktualnej wersji Modelowej Konwencji OECD.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "40",
                    "status_str": "Ustawa obowiązująca. <span class=\"_ds\" value=\"2014-05-23\">23 maja 2014 r.</span> przepisy weszły w życie.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Protokołu między Rządem Rzeczypospolitej Polskiej a Rządem Republiki Korei o zmianie Konwencji między Rządem Rzeczypospolitej Polskiej a Rządem Republiki Korei w sprawie unikania podwójnego opodatkowania i zapobiegania uchylaniu się od opodatkowania w zakresie podatków od dochodu, podpisanej w Seulu dnia 21 czerwca 1991 roku, podpisanego w Seulu dnia 22 października 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Protokołu między Rządem Rzeczypospolitej Polskiej a Rządem Republiki Korei o zmianie Konwencji między Rządem Rzeczypospolitej Polskiej a Rządem Republiki Korei w sprawie unikania podwójnego opodatkowania i zapobiegania uchylaniu się od opodatkowania w zakresie podatków od dochodu, podpisanej w Seulu dnia 21 czerwca 1991 roku, podpisanego w Seulu dnia 22 października 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107185",
                "dataset": "prawo_projekty",
                "object_id": 2123,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Rada Ministrów\" src=\"http://resources.sejmometr.pl/podmioty/a/6/55.png\" /> <span>Rada Ministrów</span></li></ul>",
                    "autorzy_str": "Rada Ministrów",
                    "autor_typ_id": "1",
                    "data_przyjecia": "2014-03-14",
                    "data_start": "2014-01-23",
                    "data_status": "2014-05-23",
                    "dokument_id": "456520",
                    "druki_str": "Druki nr <b>2089</b>, <b>2154</b>.",
                    "faza_id": "3",
                    "id": "2123",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy rozgraniczenia praw Polski i Guernsey do opodatkowania dochodów z transportu międzynarodowego.</p>",
                    "opis_skrocony": "<p>Dotyczy rozgraniczenia praw Polski i Guernsey do opodatkowania dochodów z transportu międzynarodowego.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "40",
                    "status_str": "Ustawa obowiązująca. <span class=\"_ds\" value=\"2014-05-23\">23 maja 2014 r.</span> przepisy weszły w życie.",
                    "typ_id": "6",
                    "tytul": "Projekt ustawy o ratyfikacji Umowy między Rzecząpospolitą Polską a Baliwatem Guernsey w sprawie unikania podwójnego opodatkowania w odniesieniu do przedsiębiorstw eksploatujących statki morskie lub statki powietrzne w transporcie międzynarodowym, podpisanej w Londynie dnia 8 października 2013 r.",
                    "tytul_skrocony": "o ratyfikacji Umowy między Rzecząpospolitą Polską a Baliwatem Guernsey w sprawie unikania podwójnego opodatkowania w odniesieniu do przedsiębiorstw eksploatujących statki morskie lub statki powietrzne w transporcie międzynarodowym, podpisanej w Londynie dnia 8 października 2013 r.",
                    "autor_id": [
                        "55"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=6"
    },
    "powolania_odwolania": {
        "pagination": {
            "total": 24
        },
        "dataobjects": [
            {
                "id": "12107005",
                "dataset": "prawo_projekty",
                "object_id": 1852,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość",
                    "autor_typ_id": "5",
                    "data_odrzucenia": "2013-10-11",
                    "data_start": "2013-09-11",
                    "data_status": "2013-10-11",
                    "dokument_id": "419134",
                    "druki_str": "Druki nr <b>1720</b>, <b>1772</b>.",
                    "faza_id": "4",
                    "id": "1852",
                    "ilosc_podpisow": "134",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy wyrażenia wotum nieufności wobec Ministra Rolnictwa i Rozwoju Wsi Stanisława Kalemby. Wnioskodawcy uzasadniają wniosek marginalizacją sektora rolniczego oraz obszarów wiejskich.</p>",
                    "opis_skrocony": "<p>Dotyczy wyrażenia wotum nieufności wobec Ministra Rolnictwa i Rozwoju Wsi Stanisława Kalemby. Wnioskodawcy uzasadniają wniosek marginalizacją sektora rolniczego oraz obszarów wiejskich.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "50",
                    "status_str": "Odrzucony <span class=\"_ds\" value=\"2013-10-11\">11 października 2013 r.</span>",
                    "typ_id": "5",
                    "tytul": "Wniosek o wyrażenie wotum nieufności wobec Ministra Rolnictwa i Rozwoju Wsi Stanisława Kalemby",
                    "tytul_skrocony": "o wyrażenie wotum nieufności wobec Ministra Rolnictwa i Rozwoju Wsi Stanisława Kalemby",
                    "autor_id": [
                        "8"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106952",
                "dataset": "prawo_projekty",
                "object_id": 1763,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2013-07-26",
                    "data_start": "2013-07-24",
                    "data_status": "2013-07-26",
                    "dokument_id": "371627",
                    "druki_str": "Druki nr <b>1595</b>, <b>1602</b>.",
                    "faza_id": "2",
                    "id": "1763",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-07-26\">26 lipca 2013 r.</span>",
                    "typ_id": "5",
                    "tytul": "Kandydat na stanowisko Prezesa Najwyższej Izby Kontroli",
                    "tytul_skrocony": "Kandydat na stanowisko Prezesa Najwyższej Izby Kontroli"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106899",
                "dataset": "prawo_projekty",
                "object_id": 1639,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2013-07-12",
                    "data_start": "2013-06-26",
                    "data_status": "2013-07-12",
                    "dokument_id": "370548",
                    "druki_str": "Druki nr <b>1494</b>, <b>1555</b>.",
                    "faza_id": "2",
                    "id": "1639",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-07-12\">12 lipca 2013 r.</span>",
                    "typ_id": "5",
                    "tytul": "Kandydat na stanowisko Rzecznika Praw Dziecka",
                    "tytul_skrocony": "Kandydat na stanowisko Rzecznika Praw Dziecka"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106776",
                "dataset": "prawo_projekty",
                "object_id": 1447,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li><li><img alt=\"Posłowie niezrzeszeni\" src=\"http://resources.sejmometr.pl/podmioty/a/6/135.png\" /> <span>Posłowie niezrzeszeni</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość, Niezrzeszeni",
                    "autor_typ_id": "5",
                    "data_start": "2013-04-09",
                    "data_status": "2013-04-18",
                    "dokument_id": "357371",
                    "druki_str": "Druki nr <b>1241</b>, <b>1285</b>.",
                    "faza_id": "2",
                    "id": "1447",
                    "ilosc_podpisow": "79",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy wyrażenia wotum nieufności wobec Ministra Skarbu Państwa Mikołaja Budzanowskiego. Wnioskodawcy uzasadniają wniosek złym zarządzaniem spółkami Skarbu Państwa.</p>",
                    "opis_skrocony": "<p>Dotyczy wyrażenia wotum nieufności wobec Ministra Skarbu Państwa Mikołaja Budzanowskiego. Wnioskodawcy uzasadniają wniosek złym zarządzaniem spółkami Skarbu Państwa.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-04-18\">18 kwietnia 2013 r.</span> komisja wydała opinię negatywną.",
                    "typ_id": "5",
                    "tytul": "Wniosek o wyrażenie wotum nieufności wobec Ministra Skarbu Państwa Mikołaja Budzanowskiego.",
                    "tytul_skrocony": "o wyrażenie wotum nieufności wobec Ministra Skarbu Państwa Mikołaja Budzanowskiego.",
                    "autor_id": [
                        "8",
                        "135"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106738",
                "dataset": "prawo_projekty",
                "object_id": 1382,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li><li><img alt=\"Sojusz Lewicy Demokratycznej\" src=\"http://resources.sejmometr.pl/podmioty/a/6/132.png\" /> <span>Sojusz Lewicy Demokratycznej</span></li><li><img alt=\"Ruch Palikota\" src=\"http://resources.sejmometr.pl/podmioty/a/6/237.png\" /> <span>Ruch Palikota</span></li><li><img alt=\"Solidarna Polska\" src=\"http://resources.sejmometr.pl/podmioty/a/6/238.png\" /> <span>Solidarna Polska</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość, Sojusz Lewicy Demokratycznej, Ruch Palikota, Solidarna Polska",
                    "autor_typ_id": "5",
                    "data_odrzucenia": "2013-04-05",
                    "data_start": "2013-03-11",
                    "data_status": "2013-04-05",
                    "dokument_id": "335138",
                    "druki_str": "Druki nr <b>1169</b>, <b>1178</b>.",
                    "faza_id": "4",
                    "id": "1382",
                    "ilosc_podpisow": "74",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy wotum nieufności wobec Ministra Transportu, Budownictwa i Gospodarki Morskiej Sławomira Nowaka. Wnioskodawcy uzasadniają wniosek fatalnym wykonaniem powierzonych ministrowi zadań i złą oceną Jego działań przez Polaków.</p>",
                    "opis_skrocony": "<p>Dotyczy wotum nieufności wobec Ministra Transportu, Budownictwa i Gospodarki Morskiej Sławomira Nowaka. Wnioskodawcy uzasadniają wniosek fatalnym wykonaniem powierzonych ministrowi zadań i złą oceną Jego działań przez Polaków.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "50",
                    "status_str": "Odrzucony <span class=\"_ds\" value=\"2013-04-05\">5 kwietnia 2013 r.</span>",
                    "typ_id": "5",
                    "tytul": "Wniosek o wyrażenie wotum nieufności wobec Ministra Transportu, Budownictwa i Gospodarki Morskiej Sławomira Nowaka.",
                    "tytul_skrocony": "o wyrażenie wotum nieufności wobec Ministra Transportu, Budownictwa i Gospodarki Morskiej Sławomira Nowaka.",
                    "autor_id": [
                        "8",
                        "132",
                        "237",
                        "238"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106691",
                "dataset": "prawo_projekty",
                "object_id": 1315,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li><li><img alt=\"Posłowie niezrzeszeni\" src=\"http://resources.sejmometr.pl/podmioty/a/6/135.png\" /> <span>Posłowie niezrzeszeni</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość, Niezrzeszeni",
                    "autor_typ_id": "5",
                    "data_odrzucenia": "2013-03-08",
                    "data_start": "2013-02-11",
                    "data_status": "2013-03-08",
                    "dokument_id": "329080",
                    "druki_str": "Druk nr <b>1096</b>.",
                    "faza_id": "4",
                    "id": "1315",
                    "ilosc_podpisow": "132",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy konstruktywnego wotum nieufności dla Rady Ministrów kierowanej przez Pana Donalda Tuska, wskutek przekonania opozycji o szkodliwości dalszego trwania tego rządu.</p>",
                    "opis_skrocony": "<p>Dotyczy konstruktywnego wotum nieufności dla Rady Ministrów kierowanej przez Pana Donalda Tuska, wskutek przekonania opozycji o szkodliwości dalszego trwania tego rządu.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "50",
                    "status_str": "Odrzucony <span class=\"_ds\" value=\"2013-03-08\">8 marca 2013 r.</span>",
                    "typ_id": "5",
                    "tytul": "o wyrażenie wotum nieufności Radzie Ministrów kierowanej przez Prezesa Rady Ministrów Pana Donalda Tuska i wybranie Pana Piotra Tadeusza i wybranie Pana Piotra Tadeusza Glińskiego na Prezesa Rady Ministrów",
                    "tytul_skrocony": "o wyrażenie wotum nieufności Radzie Ministrów kierowanej przez Prezesa Rady Ministrów Pana Donalda Tuska i wybranie Pana Piotra Tadeusza i wybranie Pana Piotra Tadeusza Glińskiego na Prezesa Rady Ministrów",
                    "autor_id": [
                        "8",
                        "135"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106675",
                "dataset": "prawo_projekty",
                "object_id": 1289,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li><li><img alt=\"Solidarna Polska\" src=\"http://resources.sejmometr.pl/podmioty/a/6/238.png\" /> <span>Solidarna Polska</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość, Solidarna Polska",
                    "autor_typ_id": "5",
                    "data_start": "2013-02-05",
                    "data_status": "2013-02-06",
                    "dokument_id": "328023",
                    "druki_str": "Druk nr <b>1073</b>.",
                    "faza_id": "2",
                    "id": "1289",
                    "ilosc_podpisow": "15",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Wniosek ma na celu wybór posła Ludwika Dorna na stanowisko Wicemarszałka Sejmu RP.</p>",
                    "opis_skrocony": "<p>Wniosek ma na celu wybór posła Ludwika Dorna na stanowisko Wicemarszałka Sejmu RP.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-02-06\">6 lutego 2013 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "5",
                    "tytul": "Wniosek w sprawie wyboru posła Ludwika Dorna na stanowisko Wicemarszałka Sejmu RP",
                    "tytul_skrocony": "w sprawie wyboru posła Ludwika Dorna na stanowisko Wicemarszałka Sejmu RP",
                    "autor_id": [
                        "8",
                        "238"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106676",
                "dataset": "prawo_projekty",
                "object_id": 1290,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Sojusz Lewicy Demokratycznej\" src=\"http://resources.sejmometr.pl/podmioty/a/6/132.png\" /> <span>Sojusz Lewicy Demokratycznej</span></li><li><img alt=\"Ruch Palikota\" src=\"http://resources.sejmometr.pl/podmioty/a/6/237.png\" /> <span>Ruch Palikota</span></li></ul>",
                    "autorzy_str": "Sojusz Lewicy Demokratycznej, Ruch Palikota",
                    "autor_typ_id": "5",
                    "data_start": "2013-01-02",
                    "data_status": "2013-02-06",
                    "dokument_id": "328017",
                    "druki_str": "Druk nr <b>1070</b>.",
                    "faza_id": "2",
                    "id": "1290",
                    "ilosc_podpisow": "16",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Powołanie Pani Posłanki Anny Grodzkiej na stanowisko Wicemarszałka Sejmu RP.</p>",
                    "opis_skrocony": "<p>Powołanie Pani Posłanki Anny Grodzkiej na stanowisko Wicemarszałka Sejmu RP.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-02-06\">6 lutego 2013 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "5",
                    "tytul": "Wniosek o powołanie Pani Posłanki Anny Grodzkiej na stanowisko Wicemarszałka Sejmu Rzeczypospolitej Polskiej",
                    "tytul_skrocony": "o powołanie Pani Posłanki Anny Grodzkiej na stanowisko Wicemarszałka Sejmu Rzeczypospolitej Polskiej",
                    "autor_id": [
                        "132",
                        "237"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106674",
                "dataset": "prawo_projekty",
                "object_id": 1288,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Sojusz Lewicy Demokratycznej\" src=\"http://resources.sejmometr.pl/podmioty/a/6/132.png\" /> <span>Sojusz Lewicy Demokratycznej</span></li><li><img alt=\"Ruch Palikota\" src=\"http://resources.sejmometr.pl/podmioty/a/6/237.png\" /> <span>Ruch Palikota</span></li></ul>",
                    "autorzy_str": "Sojusz Lewicy Demokratycznej, Ruch Palikota",
                    "autor_typ_id": "5",
                    "data_odrzucenia": "2013-02-08",
                    "data_start": "2013-01-02",
                    "data_status": "2013-02-08",
                    "dokument_id": "328019",
                    "druki_str": "Druk nr <b>1069</b>.",
                    "faza_id": "4",
                    "id": "1288",
                    "ilosc_podpisow": "15",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Odwołanie Pani Posłanki Wandy Nowickiej ze stanowiska Wicemarszałka Sejmu RP.</p>",
                    "opis_skrocony": "<p>Odwołanie Pani Posłanki Wandy Nowickiej ze stanowiska Wicemarszałka Sejmu RP.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "50",
                    "status_str": "Odrzucony <span class=\"_ds\" value=\"2013-02-08\">8 lutego 2013 r.</span>",
                    "typ_id": "5",
                    "tytul": "Wniosek o odwołanie Pani Posłanki Wandy Nowickiej ze stanowiska Wicemarszałka Sejmu Rzeczypospolitej Polskiej",
                    "tytul_skrocony": "o odwołanie Pani Posłanki Wandy Nowickiej ze stanowiska Wicemarszałka Sejmu Rzeczypospolitej Polskiej",
                    "autor_id": [
                        "132",
                        "237"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=5"
    },
    "sklady_komisji": {
        "pagination": {
            "total": 35
        },
        "dataobjects": [
            {
                "id": "12107057",
                "dataset": "prawo_projekty",
                "object_id": 1910,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-09-27",
                    "data_start": "2013-09-27",
                    "data_status": "2013-09-27",
                    "dokument_id": "419222",
                    "druki_str": "Druk nr <b>1775</b>.",
                    "faza_id": "2",
                    "id": "1910",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-09-27\">27 września 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107012",
                "dataset": "prawo_projekty",
                "object_id": 1859,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-09-13",
                    "data_start": "2013-09-13",
                    "data_status": "2013-09-13",
                    "dokument_id": "415085",
                    "druki_str": "Druk nr <b>1711</b>.",
                    "faza_id": "2",
                    "id": "1859",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-09-13\">13 września 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12107011",
                "dataset": "prawo_projekty",
                "object_id": 1858,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-09-13",
                    "data_start": "2013-09-13",
                    "data_status": "2013-09-13",
                    "dokument_id": "415084",
                    "druki_str": "Druk nr <b>1712</b>.",
                    "faza_id": "2",
                    "id": "1858",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-09-13\">13 września 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie wyboru nowego składu osobowego Komisji do Spraw Unii Europejskiej",
                    "tytul_skrocony": "w sprawie wyboru nowego składu osobowego Komisji do Spraw Unii Europejskiej",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106992",
                "dataset": "prawo_projekty",
                "object_id": 1805,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-08-30",
                    "data_start": "2013-08-30",
                    "data_status": "2013-08-30",
                    "dokument_id": "402218",
                    "druki_str": "Druk nr <b>1680</b>.",
                    "faza_id": "2",
                    "id": "1805",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-08-30\">30 sierpnia 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych.<br/>",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych.<br/>",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106956",
                "dataset": "prawo_projekty",
                "object_id": 1767,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-07-26",
                    "data_start": "2013-07-26",
                    "data_status": "2013-07-26",
                    "dokument_id": "371697",
                    "druki_str": "Druk nr <b>1606</b>.",
                    "faza_id": "2",
                    "id": "1767",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-07-26\">26 lipca 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106898",
                "dataset": "prawo_projekty",
                "object_id": 1638,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-06-21",
                    "data_start": "2013-06-21",
                    "data_status": "2013-06-21",
                    "dokument_id": "368268",
                    "druki_str": "Druk nr <b>1482</b>.",
                    "faza_id": "2",
                    "id": "1638",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-06-21\">21 czerwca 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106885",
                "dataset": "prawo_projekty",
                "object_id": 1625,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-06-14",
                    "data_start": "2013-06-14",
                    "data_status": "2013-06-14",
                    "dokument_id": "367490",
                    "druki_str": "Druk nr <b>1456</b>.",
                    "faza_id": "2",
                    "id": "1625",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-06-14\">14 czerwca 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106864",
                "dataset": "prawo_projekty",
                "object_id": 1598,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-05-24",
                    "data_start": "2013-05-24",
                    "data_status": "2013-05-24",
                    "dokument_id": "363958",
                    "druki_str": "Druk nr <b>1403</b>.",
                    "faza_id": "2",
                    "id": "1598",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-05-24\">24 maja 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych.",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych.",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106822",
                "dataset": "prawo_projekty",
                "object_id": 1520,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prezydium Sejmu\" src=\"http://resources.sejmometr.pl/podmioty/a/6/53.png\" /> <span>Prezydium Sejmu</span></li></ul>",
                    "autorzy_str": "Prezydium Sejmu",
                    "autor_typ_id": "7",
                    "data_przyjecia": "2013-05-10",
                    "data_start": "2013-05-10",
                    "data_status": "2013-05-10",
                    "dokument_id": "360493",
                    "druki_str": "Druk nr <b>1342</b>.",
                    "faza_id": "2",
                    "id": "1520",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-05-10\">10 maja 2013 r.</span>",
                    "typ_id": "100",
                    "tytul": "Wniosek w sprawie zmian w składach osobowych komisji sejmowych",
                    "tytul_skrocony": "w sprawie zmian w składach osobowych komisji sejmowych",
                    "autor_id": [
                        "53"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=100"
    },
    "referenda": {
        "pagination": {
            "total": 4
        },
        "dataobjects": [
            {
                "id": "12106991",
                "dataset": "prawo_projekty",
                "object_id": 1804,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Obywatele\" src=\"http://resources.sejmometr.pl/podmioty/a/6/51.png\" /> <span>Obywatele</span></li></ul>",
                    "autorzy_str": "Obywatele",
                    "autor_typ_id": "2",
                    "data_odrzucenia": "2013-11-08",
                    "data_start": "2013-08-27",
                    "data_status": "2013-11-08",
                    "dokument_id": "402114",
                    "druki_str": "Druk nr <b>1635</b>.",
                    "faza_id": "4",
                    "id": "1804",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Dotyczy wniosku o poddanie pod referendum ogólnokrajowe sprawy o szczególnym znaczeniu dla państwa i obywateli dotyczącej systemu edukacji \"RATUJ MALUCHY I STARSZE DZIECI TEŻ\".</p>",
                    "opis_skrocony": "<p>Dotyczy wniosku o poddanie pod referendum ogólnokrajowe sprawy o szczególnym znaczeniu dla państwa i obywateli dotyczącej systemu edukacji \"RATUJ MALUCHY I STARSZE DZIECI TEŻ\".</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "50",
                    "status_str": "Odrzucony <span class=\"_ds\" value=\"2013-11-08\">8 listopada 2013 r.</span>",
                    "typ_id": "103",
                    "tytul": "Wniosek o poddanie pod referendum ogólnokrajowe sprawy o szczególnym znaczeniu dla państwa i obywateli dotyczącej systemu edukacji, \"Ratuj maluchy i starsze dzieci też\"",
                    "tytul_skrocony": "o poddanie pod referendum ogólnokrajowe sprawy o szczególnym znaczeniu dla państwa i obywateli dotyczącej systemu edukacji, \"Ratuj maluchy i starsze dzieci też\"",
                    "autor_id": [
                        "51"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106108",
                "dataset": "prawo_projekty",
                "object_id": 459,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li><li><img alt=\"Solidarna Polska\" src=\"http://resources.sejmometr.pl/podmioty/a/6/238.png\" /> <span>Solidarna Polska</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość, Solidarna Polska",
                    "autor_typ_id": "5",
                    "data_odrzucenia": "2012-06-15",
                    "data_start": "2012-04-24",
                    "data_status": "2012-06-15",
                    "dokument_id": "153411",
                    "druki_str": "Druk nr <b>423</b>.",
                    "faza_id": "4",
                    "id": "459",
                    "ilosc_podpisow": "101",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>W treści wniosku wyrażono opinię, iż referendum w sprawie renegocjacji pakietu klimatyczno-energetycznego jest uzasadnione przedmiotem regulacji, sytuacji ekonomicznej obywateli, a przede wszystkim gospodarczej kraju, gdyż ustalenia pakietu mogą doprowadzić do spowolnienia rozwoju gospodarczego kraju, obniżenia atrakcyjności Polski dla zagranicznych przedsiębiorstw.</p>",
                    "opis_skrocony": "<p>W treści wniosku wyrażono opinię, iż referendum w sprawie renegocjacji pakietu klimatyczno-energetycznego jest uzasadnione przedmiotem regulacji, sytuacji ekonomicznej obywateli, a przede wszystkim gospodarczej kraju, gdyż ustalenia pakietu mogą doprowadzić do spowolnienia rozwoju...",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "50",
                    "status_str": "Odrzucony <span class=\"_ds\" value=\"2012-06-15\">15 czerwca 2012 r.</span>",
                    "typ_id": "103",
                    "tytul": "Wniosek o zarządzenie ogólnopolskiego referendum w sprawie renegocjacji pakietu klimatyczno-energetycznego.",
                    "tytul_skrocony": "o zarządzenie ogólnopolskiego referendum w sprawie renegocjacji pakietu klimatyczno-energetycznego.",
                    "autor_id": [
                        "8",
                        "238"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12105993",
                "dataset": "prawo_projekty",
                "object_id": 270,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Obywatele\" src=\"http://resources.sejmometr.pl/podmioty/a/6/51.png\" /> <span>Obywatele</span></li></ul>",
                    "autorzy_str": "Obywatele",
                    "autor_typ_id": "2",
                    "data_odrzucenia": "2012-03-30",
                    "data_start": "2012-03-21",
                    "data_status": "2012-03-30",
                    "dokument_id": "141261",
                    "druki_str": "Druk nr <b>254</b>.",
                    "faza_id": "4",
                    "id": "270",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p></p>",
                    "opis_skrocony": "<p></p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "50",
                    "status_str": "Odrzucony <span class=\"_ds\" value=\"2012-03-30\">30 marca 2012 r.</span>",
                    "typ_id": "103",
                    "tytul": "Wniosek o przeprowadzenie referendum - w sprawie o szczególnym znaczeniu dla państwa i obywateli dotyczącej powszechnego wieku emerytalnego kobiet i mężczyzn wraz z wykazem obywateli popierających zgłoszenie wniosku o referendum.",
                    "tytul_skrocony": "o przeprowadzenie referendum - w sprawie o szczególnym znaczeniu dla państwa i obywateli dotyczącej powszechnego wieku emerytalnego kobiet i mężczyzn wraz z wykazem obywateli popierających zgłoszenie wniosku o referendum.",
                    "autor_id": [
                        "51"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12105977",
                "dataset": "prawo_projekty",
                "object_id": 239,
                "data": {
                    "autorzy_html": "<ul class=\"__autorzy_html_ul\"><li><img alt=\"Prawo i Sprawiedliwość\" src=\"http://resources.sejmometr.pl/podmioty/a/6/8.png\" /> <span>Prawo i Sprawiedliwość</span></li><li><img alt=\"Solidarna Polska\" src=\"http://resources.sejmometr.pl/podmioty/a/6/238.png\" /> <span>Solidarna Polska</span></li></ul>",
                    "autorzy_str": "Prawo i Sprawiedliwość, Solidarna Polska",
                    "autor_typ_id": "5",
                    "data_start": "2012-01-27",
                    "data_status": "2012-03-05",
                    "dokument_id": "141165",
                    "druki_str": "Druk nr <b>228</b>.",
                    "faza_id": "2",
                    "id": "239",
                    "ilosc_podpisow": "96",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "<p>Celem wniosku jest przeprowadzenie ogólnopolskiego referendum w kwestii przyjęcia przez Polskę umowy ACTA.</p>",
                    "opis_skrocony": "<p>Celem wniosku jest przeprowadzenie ogólnopolskiego referendum w kwestii przyjęcia przez Polskę umowy ACTA.</p>",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2012-03-05\">5 marca 2012 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "103",
                    "tytul": "Wniosek o pilne zarządzenie ogólnopolskiego referendum w sprawie przyjęcia przez Polskę umowy ACTA.",
                    "tytul_skrocony": "o pilne zarządzenie ogólnopolskiego referendum w sprawie przyjęcia przez Polskę umowy ACTA.",
                    "autor_id": [
                        "8",
                        "238"
                    ]
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=103"
    },
    "inne": {
        "pagination": {
            "total": 9
        },
        "dataobjects": [
            {
                "id": "12106941",
                "dataset": "prawo_projekty",
                "object_id": 1739,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_start": "2013-06-28",
                    "data_status": "2013-07-17",
                    "dokument_id": "370976",
                    "druki_str": "Druk nr <b>1574</b>.",
                    "faza_id": "2",
                    "id": "1739",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2013-07-17\">17 lipca 2013 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "12",
                    "tytul": "Rządowy dokument: Informacja o realizacji zadań \"Programu dla Odry - 2006\" w roku 2012",
                    "tytul_skrocony": "Rządowy dokument: Informacja o realizacji zadań \"Programu dla Odry - 2006\" w roku 2012"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106558",
                "dataset": "prawo_projekty",
                "object_id": 1126,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_start": "2012-12-10",
                    "data_status": "2012-12-11",
                    "dokument_id": "306727",
                    "druki_str": "Druk nr <b>954</b>.",
                    "faza_id": "2",
                    "id": "1126",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2012-12-11\">11 grudnia 2012 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "12",
                    "tytul": "Opinia komisji dotycząca wniosku w sprawie wyboru kandydata na zastępcę przewodniczącego Trybunału Stanu (druk nr 932)",
                    "tytul_skrocony": "Opinia komisji dotycząca wniosku w sprawie wyboru kandydata na zastępcę przewodniczącego Trybunału Stanu (druk nr 932)"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106557",
                "dataset": "prawo_projekty",
                "object_id": 1125,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2013-11-22",
                    "data_start": "2012-12-05",
                    "data_status": "2013-11-22",
                    "dokument_id": "418945",
                    "druki_str": "Druki nr <b>972</b>, <b>1715</b>.",
                    "faza_id": "2",
                    "id": "1125",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-11-22\">22 listopada 2013 r.</span>",
                    "typ_id": "12",
                    "tytul": "Strategia Rozwoju Kraju 2020",
                    "tytul_skrocony": "Strategia Rozwoju Kraju 2020"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106537",
                "dataset": "prawo_projekty",
                "object_id": 1089,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2012-12-12",
                    "data_start": "2012-11-30",
                    "data_status": "2012-12-12",
                    "dokument_id": "305424",
                    "druki_str": "Druk nr <b>923</b>.",
                    "faza_id": "2",
                    "id": "1089",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2012-12-12\">12 grudnia 2012 r.</span>",
                    "typ_id": "12",
                    "tytul": "Informacja Prezesa Rady Ministrów na temat: stanu  negocjacji  pakietu  legislacyjnego  dotyczącego Wspólnej  Polityki  Rolnej  po  2013  roku  z  oceną  realizacji oczekiwań  Rządu  RP  w  zakresie  europejskiej  polityki rolnej; sposobu realizacji  uchwały  Sejmu  z  dnia  25 kwietnia  2012  r.  w  sprawie  wsparcia  Rządu Rzeczypospolitej Polskiej i wezwania Parlamentu Europejskiego  do  aktywnego  działania  na  rzecz uproszczenia  wspólnej  polityki  rolnej,  konkurencyjności  i postępu ora",
                    "tytul_skrocony": "Informacja Prezesa Rady Ministrów na temat: stanu  negocjacji  pakietu  legislacyjnego  dotyczącego Wspólnej  Polityki  Rolnej  po  2013  roku  z  oceną  realizacji oczekiwań  Rządu  RP  w  zakresie  europejskiej  polityki rolnej; sposobu realizacji  uchwały  Sejmu  z  dnia  25 kwietnia  2012  r.  w  sprawie  wsparcia  Rządu Rzeczypospolitej Polskiej i wezwania Parlamentu Europejskiego  do  aktywnego  działania  na  rzecz uproszczenia  wspólnej  polityki  rolnej,  konkurencyjności  i postępu ora"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106264",
                "dataset": "prawo_projekty",
                "object_id": 707,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2012-08-30",
                    "data_start": "2012-08-28",
                    "data_status": "2012-08-30",
                    "dokument_id": "275571",
                    "druki_str": "Druk nr <b>658</b>.",
                    "faza_id": "2",
                    "id": "707",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2012-08-30\">30 sierpnia 2012 r.</span>",
                    "typ_id": "12",
                    "tytul": "Informacja Rządu  na  temat  realizacji zadań  związanych  z  dystrybucją pomocy  społecznej  przez  szkoły  i socjalnej funkcji szkoły",
                    "tytul_skrocony": "Informacja Rządu  na  temat  realizacji zadań  związanych  z  dystrybucją pomocy  społecznej  przez  szkoły  i socjalnej funkcji szkoły"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106276",
                "dataset": "prawo_projekty",
                "object_id": 732,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2013-01-25",
                    "data_start": "2012-07-31",
                    "data_status": "2013-01-25",
                    "dokument_id": "306066",
                    "druki_str": "Druki nr <b>679</b>, <b>953</b>.",
                    "faza_id": "2",
                    "id": "732",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2013-01-25\">25 stycznia 2013 r.</span>",
                    "typ_id": "12",
                    "tytul": "Informacja o funkcjonowaniu Centrów i  Klubów  Integracji  Społecznej dla Sejmu i Senatu Rzeczypospolitej Polskiej",
                    "tytul_skrocony": "Informacja o funkcjonowaniu Centrów i  Klubów  Integracji  Społecznej dla Sejmu i Senatu Rzeczypospolitej Polskiej"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106230",
                "dataset": "prawo_projekty",
                "object_id": 661,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2012-11-23",
                    "data_start": "2012-07-17",
                    "data_status": "2012-11-23",
                    "dokument_id": "279761",
                    "druki_str": "Druki nr <b>616</b>, <b>699</b>.",
                    "faza_id": "2",
                    "id": "661",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2012-11-23\">23 listopada 2012 r.</span>",
                    "typ_id": "12",
                    "tytul": "Stan bezpieczeństwa ruchu drogowego. Działania realizowane w zakresie bezpieczeństwa ruchu drogowego w 2011 roku oraz rekomendacje na rok 2012.",
                    "tytul_skrocony": "Stan bezpieczeństwa ruchu drogowego. Działania realizowane w zakresie bezpieczeństwa ruchu drogowego w 2011 roku oraz rekomendacje na rok 2012."
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12106178",
                "dataset": "prawo_projekty",
                "object_id": 557,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_przyjecia": "2012-07-13",
                    "data_start": "2012-06-26",
                    "data_status": "2012-07-13",
                    "dokument_id": "159396",
                    "druki_str": "Druki nr <b>507</b>, <b>543</b>.",
                    "faza_id": "2",
                    "id": "557",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "30",
                    "status_str": "Przyjęty <span class=\"_ds\" value=\"2012-07-13\">13 lipca 2012 r.</span>",
                    "typ_id": "12",
                    "tytul": "Kandydat na stanowisko sędziego Trybunału Konstytucyjnego",
                    "tytul_skrocony": "Kandydat na stanowisko sędziego Trybunału Konstytucyjnego"
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            },
            {
                "id": "12105940",
                "dataset": "prawo_projekty",
                "object_id": 201,
                "data": {
                    "autorzy_html": "",
                    "autorzy_str": "",
                    "autor_typ_id": "0",
                    "data_start": "2012-01-12",
                    "data_status": "2012-01-26",
                    "dokument_id": "140811",
                    "druki_str": "Druk nr <b>151</b>.",
                    "faza_id": "2",
                    "id": "201",
                    "ilosc_podpisow": "0",
                    "nadrzedny": "0",
                    "nadrzedny_projekt_id": "0",
                    "opis": "",
                    "opis_skrocony": "",
                    "podrzedny": "0",
                    "przebieg_str": "",
                    "status_id": "20",
                    "status_str": "Rozpatrywany. <span class=\"_ds\" value=\"2012-01-26\">26 stycznia 2012 r.</span> dostarczono posłom jako druk sejmowy.",
                    "typ_id": "12",
                    "tytul": "Uchwała w sprawie przedstawienia Sejmowi kandydata na oskarżyciela w postępowaniu przed Trybunałem Stanu w sprawie pociągnięcia do odpowiedzialności konstytucyjnej byłego Ministra Skarbu Państwa Emila Wąsacza.",
                    "tytul_skrocony": "Uchwała w sprawie przedstawienia Sejmowi kandydata na oskarżyciela w postępowaniu przed Trybunałem Stanu w sprawie pociągnięcia do odpowiedzialności konstytucyjnej byłego Ministra Skarbu Państwa Emila Wąsacza."
                },
                "score": {
                    "name": "score",
                    "value": 1,
                    "boost": false
                }
            }
        ],
        "href": "/dane/prawo_projekty?typ_id=12"
    }
}'; die();
	    
	    $chapters = array(
	    	array(
	    		'id' => 'projekty_ustaw',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '1',
	    		),
	    	),
	    	array(
	    		'id' => 'projekty_uchwal',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '2',
	    		),
	    	),
	    	array(
	    		'id' => 'sprawozdania_kontrolne',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '11',
	    		),
	    	),
	    	array(
	    		'id' => 'umowy',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '6',
	    		),
	    	),
	    	array(
	    		'id' => 'powolania_odwolania',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '5',
	    		),
	    	),	    	
	    	array(
	    		'id' => 'sklady_komisji',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '100',
	    		),
	    	),
	    	array(
	    		'id' => 'referenda',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '103',
	    		),
	    	),
	    	array(
	    		'id' => 'inne',
	    		'conditions' => array(
	    			'dataset' => 'prawo_projekty',
	    			'typ_id' => '12',
	    		),
	    	),
	    );
	    
	    $output = array();
	    
	    foreach( $chapters as $chapter ) {
	    	
	    	$data = $this->find('all', array(
	            'conditions' => $chapter['conditions'],
	            'limit' => 9,
	        ));
	        
	        $href = '/dane/' . $chapter['conditions']['dataset'];
	        $conditions = $chapter['conditions'];
	        unset( $conditions['dataset'] );
	        
	        if( !empty($conditions) )
	        	$href .= '?' . http_build_query($conditions);
	        
		    $output[$chapter['id']] = array_merge($data, array(
		    	'href' =>  $href,
		    ));
	        
	    }
		    
	    
	    return $output;
	    
    }
} 