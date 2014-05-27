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

        $this->belongsTo['Folder']['conditions']['Application.enabled'] = '1';


        $fields = array('Application.id', 'Application.slug', 'Application.name', 'Application.plugin', 'Application.type', 'Application.home',
            'Folder.id', 'Folder.slug', 'Folder.name', 'Application.folder_id');

        $queryData = array_merge(array(
            'fields' => $fields,
            'order' => array(
                array(
                    'Application.ord' => 'asc'
                )
            ),
            'limit' => 100,
            'conditions' => array(
                'Application.enabled' => '1',
            ),
        ), $queryData);

        return parent::find($type, $queryData);
    }

}