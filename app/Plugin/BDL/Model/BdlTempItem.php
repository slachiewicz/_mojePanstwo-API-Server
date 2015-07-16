<?

class BdlTempItem extends AppModel
{

    public $useTable = 'panel_wskazniki';

    public $hasAndBelongsToMany = array(
        'WymiaryKombinacje' =>
            array(
                'className' => 'BDL.WymiaryKombinacje',
                'joinTable' => 'BDL_kombiancje_user_item',
                'with' => 'BDL.BDL_kombiancje_user_item',
                'foreignKey' => 'useritem_id',
                'associationForeignKey' => 'dim_id',
            )
    );
    public $actsAs = array('Containable');

}