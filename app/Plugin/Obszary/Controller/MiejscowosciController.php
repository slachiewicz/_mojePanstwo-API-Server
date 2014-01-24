<?php

class MiejscowosciController extends ObszaryAppController
{
    public $uses = array('Obszary.Miejscowosc', 'Obszary.Gmina', 'Obszary.Powiat', 'Obszary.Wojewodztwo');
} 