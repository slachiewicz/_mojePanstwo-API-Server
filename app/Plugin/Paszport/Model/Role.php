<?php

class Role extends PaszportAppModel {

    public $useTable = 'users_roles';
    public $belongsTo = array('Paszport.UserRole');

}