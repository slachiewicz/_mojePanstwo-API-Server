<?php

class Key extends PaszportAppModel
{
    public $belongsTo = array('Paszport.User');
    public $actsAs = array('Containable');
}