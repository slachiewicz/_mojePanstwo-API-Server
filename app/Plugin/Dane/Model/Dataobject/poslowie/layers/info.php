<?


$mowca_id = $this->DB->selectValue("SELECT mowca_id FROM mowcy_poslowie WHERE posel_id='$id'");


$output = array(

    'stanowiska' => $this->DB->query("SELECT mowcy_funkcje.funkcja_id, wypowiedzi_funkcje.nazwa, mowcy_funkcje.liczba FROM mowcy_funkcje JOIN wypowiedzi_funkcje ON mowcy_funkcje.funkcja_id=wypowiedzi_funkcje.id WHERE mowcy_funkcje.mowca_id='$mowca_id'"),

    'komisje_stanowiska' => $this->DB->query("SELECT s_poslowie_komisje.id, s_poslowie_komisje.komisja_id, s_poslowie_komisje.od, s_poslowie_komisje.do, s_komisje.nazwa, s_komisje_funkcje.nazwa as 'stanowisko' FROM s_poslowie_komisje JOIN s_komisje ON s_poslowie_komisje.komisja_id=s_komisje.id JOIN s_komisje_funkcje ON s_poslowie_komisje.funkcja_id=s_komisje_funkcje.id WHERE s_poslowie_komisje.posel_id='$id' AND s_poslowie_komisje.deleted='0' AND s_poslowie_komisje.komisja_id!=0 GROUP BY komisja_id, funkcja_id ORDER BY s_poslowie_komisje.aktywny DESC"),

    'wspolpracownicy' => $this->DB->query("SELECT s_poslowie_wsp.id, s_poslowie_wsp.nazwa_str as 'nazwa', s_poslowie_wsp.funkcja_str as 'funkcja' FROM s_poslowie_wsp WHERE s_poslowie_wsp.posel_id='$id' AND s_poslowie_wsp.`data`!='0000-00-00'"),

    'oswiadczenia_majatkowe' => $this->DB->query("SELECT s_poslowie_oswmaj.id, s_poslowie_oswmaj.posel_id, s_poslowie_oswmaj.label, s_poslowie_oswmaj.data_str as 'data' FROM s_poslowie_oswmaj WHERE s_poslowie_oswmaj.posel_id='$id' ORDER BY s_poslowie_oswmaj.data_str DESC"),

    'rejestr_korzysci' => $this->DB->query("SELECT s_poslowie_korzysci.id, s_poslowie_korzysci.label, s_poslowie_korzysci.data FROM s_poslowie_korzysci WHERE s_poslowie_korzysci.posel_id='$id' ORDER BY s_poslowie_korzysci.data DESC"),

);

return $output;