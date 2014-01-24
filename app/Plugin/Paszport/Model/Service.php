<?php


class Service extends PaszportAppModel
{
    public $hasAndBelongsToMany = array('Paszport.User');
    public $actsAs = array('Containable');
}