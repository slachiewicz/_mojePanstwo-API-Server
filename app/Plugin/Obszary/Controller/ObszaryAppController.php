<?php

class ObszaryAppController extends AppController
{
    public function index()
    {
        $obj = $this->{$this->modelClass}->find($this->params->type, $this->data);
        $this->set(array(
            $this->{$this->modelClass}->alias => $obj,
            '_serialize' => $this->{$this->modelClass}->alias,
        ));
    }
} 