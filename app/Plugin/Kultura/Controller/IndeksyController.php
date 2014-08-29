<?php

class IndeksyController extends AppController
{
	
	public $uses = array('Kultura.Indeks');
	
	public function view()
    {
		$data = $this->Indeks->get( $this->request->params['id'] );
		
		$this->set('title_for_layout', $data['details']['nazwa']);
		
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }
    
}
