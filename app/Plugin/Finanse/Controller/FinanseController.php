<?php

class FinanseController extends AppController
{

    public function getBudgetSpendings()
    {
        $data = $this->Finanse->getBudgetSpendings();

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

} 