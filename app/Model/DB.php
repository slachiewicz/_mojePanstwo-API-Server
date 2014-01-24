<?

class DB extends AppModel
{

    public $useTable = false;

    public function __construct($id = false, $table = null, $ds = null)
    {
        parent::__construct($id, $table, $ds);
        $this->query('SET SESSION group_concat_max_len = 10240000');
    }

    public function queryCount($q)
    {

        $rows = $this->query($q);
        $count = $this->query("SELECT FOUND_ROWS() as `count`");

        return array($rows, @$count[0][0]['count']);

    }

    public function selectValues($q)
    {

        $output = array();
        $data = $this->query($q);

        if (is_array($data) && !empty($data))
            foreach ($data as &$d)
                if (is_array($d) && !empty($d)) {
                    $d = array_shift($d);
                    if (is_array($d) && !empty($d))
                        $output[] = array_shift($d);
                }

        return $output;

    }

    public function selectValue($q)
    {

        $output = false;
        $data = $this->query($q);

        if (is_array($data) && !empty($data)) {
            foreach ($data as &$d) {
                if (is_array($d) && !empty($d)) {
                    $d = array_shift($d);
                    if (is_array($d) && !empty($d)) {
                        $output = array_shift($d);
                        break;
                    }
                }
            }
        }

        return $output;

    }

    public function selectAssocs($q)
    {

        $output = array();
        $data = $this->query($q);

        if (is_array($data) && !empty($data))
            foreach ($data as &$d)
                if (is_array($d) && !empty($d)) {
                    $output[] = array_shift($d);
                }


        return $output;

    }

    public function selectAssoc($q)
    {

        $output = false;
        $data = $this->query($q);

        if (is_array($data) && !empty($data))
            foreach ($data as &$d)
                if (is_array($d) && !empty($d)) {
                    $output = array_shift($d);
                    break;
                }


        return $output;

    }


}