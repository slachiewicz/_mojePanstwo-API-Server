<?php

class ErrorReport extends PaszportAppModel
{
    public $belongsTo = array('User' => array('User' => 'Paszport.User'));
}