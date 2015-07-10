<?php

class UserRole extends PaszportAppModel {

    public $useTable = 'user_role';
    public $belongsTo = array('Paszport.Role');

}