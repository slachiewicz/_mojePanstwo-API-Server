<?php

class PowiatyController extends ObszaryAppController
{
    public $uses = array('Obszary.Powiat', 'Obszary.Gmina', 'Obszary.Miejscowosc', 'Obszary.Wojewodztwo');
} 