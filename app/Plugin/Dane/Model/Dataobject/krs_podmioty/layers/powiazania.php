<?

class TreeBuilder {

    private $depth;
    private $data;
    private $id;

    public function __construct($results, $id, $depth = 1) {
        $this->depth = (int) $depth;
        $this->data = $this->build($results);
        $this->id = (int) $id;
    }

    public function build($results)
    {
        $data = array(
            array(
                'id' => $this->id,
                'osoby' => array() // tutaj wrzucamy wszystkie osoby z _0 na końcu
            ),
            // tutaj wrzucamy jako nowe elementy tablicy wszystkie pozycje (unikalne)
            // i do pozycji dorzucamy osoby pogrupowane wedlug stanowisk
        );

        foreach($results as $row) {

        }

        return $data;
    }

    public function getData() {
        return $this->data;
    }

}

class QueryBuilder {

    private $depth;
    private $id;
    private $query;

    private $tables = array(
        'osoby' => 'krs_osoby',
        'osoby_pozycje' => 'krs_osoby-pozycje',
        'pozycje' => 'krs_pozycje'
    );

    private $fields = array(
        'reprezentat',
        'reprezentat_funkcja',
        'wspolnik',
        'akcjonariusz',
        'prokurent',
        'nadzorca',
        'zalozyciel'
    );

    public function __construct($depth = 1, $id) {
        $this->depth = (int) $depth;
        $this->id = (int) $id;
        $this->build();
    }

    private function alias($alias, $i = 0) {
        return $alias . '_' . $i;
    }

    private function name($alias) {
        return $this->tables[$alias];
    }

    private function build()
    {
        $select = "SELECT ";
        $from = "FROM `".$this->name('osoby_pozycje')."` AS `".$this->alias('osoby_pozycje')."` ";
        $joins = "";
        $where = "WHERE `".$this->alias('osoby_pozycje')."`.`pozycja_id` = ".$this->id;

        for($i = 0; $i < $this->depth; $i++)
        {
            $isFirst = (bool) ($i == 0);
            $isLast = (bool) ($i + 1 == $this->depth);
            $type = $i % 2 == 0 ? 'osoby' : 'pozycje';

            if($isFirst) {
                $joins .= "INNER JOIN `".$this->name('osoby')."` as `".$this->alias('osoby', $i)."` ON (`".$this->alias('osoby_pozycje', $i)."`.`osoba_id` = `".$this->alias('osoby', $i)."`.`id`) ";
            }

            if($type == 'osoby') {

                $select .= "`".$this->alias('osoby', $i)."`.`id` AS '".$this->alias('osoby', $i).".id', ";
                $select .= "`".$this->alias('osoby', $i)."`.`imie_nazwisko` AS '".$this->alias('osoby', $i).".imie_nazwisko', ";

                foreach($this->fields as $field) {
                    $select .= "`".$this->alias('osoby_pozycje', $i)."`.`".$field."` AS '".$this->alias('osoby_pozycje', $i).".".$field."', ";
                }

                if($isLast)
                    $select = substr($select, 0, -2)." ";

                if(!$isFirst) {
                    $joins .= "LEFT JOIN `".$this->name('osoby_pozycje')."` as `".$this->alias('osoby_pozycje', $i)."` ON (`".$this->alias('osoby_pozycje', $i - 1)."`.`pozycja_id` = `".$this->alias('osoby_pozycje', $i)."`.`pozycja_id` AND `".$this->alias('osoby_pozycje', $i)."`.`deleted` = '0') ";
                    $joins .= "LEFT JOIN `".$this->name('osoby')."` as `".$this->alias('osoby', $i)."` ON (`".$this->alias('osoby_pozycje', $i)."`.`osoba_id` = `".$this->alias('osoby', $i)."`.`id`) ";
                }

            } elseif($type == 'pozycje') {

                $select .= "`".$this->alias('pozycje', $i)."`.`id` AS '".$this->alias('pozycje', $i).".id', ";
                $select .= "`".$this->alias('pozycje', $i)."`.`nazwa` AS '".$this->alias('pozycje', $i).".nazwa'";
                $select .= $isLast ? " " : ", ";

                $joins .= "LEFT JOIN `".$this->name('osoby_pozycje')."` as `".$this->alias('osoby_pozycje', $i)."` ON (`".$this->alias('osoby_pozycje', $i - 1)."`.`osoba_id` = `".$this->alias('osoby_pozycje', $i)."`.`osoba_id` AND `".$this->alias('osoby_pozycje', $i)."`.`deleted` = '0') ";
                $joins .= "LEFT JOIN `".$this->name('pozycje')."` as `".$this->alias('pozycje', $i)."` ON (`".$this->alias('osoby_pozycje', $i)."`.`pozycja_id` = `".$this->alias('pozycje', $i)."`.`id`) ";

            }
        }

        $this->query = $select . $from . $joins . $where;
    }

    public function getQuery() {
        return $this->query;
    }

}

$depth = 3;
$queryBuilder = new QueryBuilder($depth, $id);
$query = $queryBuilder->getQuery();
$results = $this->DB->selectAssocs($query);
$treeBuilder = new TreeBuilder($results, $id, $depth);
return $treeBuilder->getData();