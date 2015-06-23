<?php

class UserExpand extends PaszportAppModel
{
    public $belongsTo = array('Paszport.User');
    public $useDbConfig = 'default';
}
