<?

    return $this->DB->selectValues("SELECT dokument_id FROM krakow_rady_prawo_pliki WHERE `prawo_id`='" . addslashes( $id ) . "'");
