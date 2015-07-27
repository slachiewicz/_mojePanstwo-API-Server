<?

$features = array(

    'mailings' => $this->DB->selectAssocs("
        SELECT *
        FROM organizacje_dzialania_pisma
        WHERE dzialanie_id = ".( (int) $id )."
    ")

);