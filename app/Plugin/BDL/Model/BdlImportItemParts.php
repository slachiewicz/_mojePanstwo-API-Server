<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 16/07/15
 * Time: 10:00
 */

class ImportedWskaznikiCzesci extends AppModel
{

    public $useTable = 'bdl_wskazniki_imported_czesci';

    public $belongsTo = array(
        'BdlImportItem' =>
            array(
                'className' => 'BDL.BdlImportItem',
                'foreignKey' => 'wskaznik_id'
            )
    );
    public $actsAs = array('Containable');

}