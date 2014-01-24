<?

class Application extends AppModel
{

    var $belongsTo = array(
        'Folder' => array(
            'className' => 'Application',
            'foreignKey' => 'folder_id',
            'type' => 'LEFT',
            'order' => array('Folder.ord' => 'asc'),
            'conditions' => array(
                'Folder.type' => 'folder',
            ),
        ),
    );

    function find($type = 'first', $queryData = array())
    {

        $enabledField = 'Application.';
        $enabledField .= Configure::read('devaccess') ? 'dev_enabled' : 'enabled';

        $this->belongsTo['Folder']['conditions'][$enabledField] = '1';


        $fields = array('Application.id', 'Application.slug', 'Application.name', 'Application.plugin', 'Application.type',);

        if (Configure::read('devaccess'))
            $fields = array_merge($fields, array(
                'Folder.id', 'Folder.slug', 'Folder.name', 'Application.folder_id',
            ));


        $queryData = array_merge(array(
            'fields' => $fields,
            'order' => array(
                array(
                    'Application.ord' => 'asc'
                )
            ),
            'limit' => 100,
            'conditions' => array(
                $enabledField => '1',
            ),
        ), $queryData);

        return parent::find($type, $queryData);
    }

}