<?

class Datachannel extends AppModel
{

    public $useTable = 'datachannels';

    public $hasMany = array(
        'Dataset' => array(
            'className' => 'Dane.Dataset',
            'foreignKey' => 'channel_id',
        ),
    );
    public $actsAs = array('Containable');
    public $virtualFields = array(
        'name' => 'nazwa',
    );

    public function find($type = 'first', $queryData = array())
    {
        // $catalog_field = Configure::read('devaccess') ? 'backup_catalog' : 'catalog';
        $catalog_field = 'backup_catalog';
        $this->hasMany['Dataset']['conditions'][$catalog_field] = '1';

        $queryData = array_merge_recursive(array(
            'order' => array('Datachannel.ord' => 'asc'),
            'limit' => 100,
        ), $queryData);

        return parent::find($type, $queryData);
    }

    public function getQueries()
    {
        $dbo = $this->getDatasource();
        $logs = $dbo->getLog();
        debug($logs);
    }

}