<?php

class WojewodztwaController extends ObszaryAppController
{
    public $uses = array('Obszary.Wojewodztwo', 'Obszary.Gmina', 'Obszary.Miejscowosc', 'Obszary.Powiat');
} 