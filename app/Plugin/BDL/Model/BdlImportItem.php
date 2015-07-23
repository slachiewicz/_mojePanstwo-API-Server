<?php
/**
 * Created by PhpStorm.
 * User: tomaszdrazewski
 * Date: 16/07/15
 * Time: 09:59
 */

class BdlImportItem extends AppModel
{

    public $useTable = 'bdl_wskazniki_imported';



    public $hasMany = array(
        'BdlImportItemParts' =>
            array(
                'className' => 'BDL.BdlImportItemParts'
            )
    );
    public $actsAs = array('Containable');

}