<?php

class FinanseController extends AppController
{

    public function getBudgetSpendings()
    {
        $data = $this->Finanse->getBudgetSpendings();

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
    
    public function getBudgetSections()
    {
        $data = $this->Finanse->getBudgetSections();

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
    
    public function getBudgetData()
    {
    	
    	$gmina_id = isset( $this->request->query['gmina_id'] ) ? $this->request->query['gmina_id'] : false;
    	
        $data = $this->Finanse->getBudgetData($gmina_id);

        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
    

} 