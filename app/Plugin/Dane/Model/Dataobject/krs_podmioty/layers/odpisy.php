<?

return $this->DB->selectAssocs("
    SELECT complete_ts
    FROM krs_files
    WHERE krs_pozycje_id = ".( (int) $id )." AND complete = '1'
    ORDER BY id DESC
    LIMIT 10
");