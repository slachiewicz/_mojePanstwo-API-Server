<?php

class AdministracjaController extends AppController
{

	public function getData()
	{
	
		$data = $this->Administracja->getData();
		
		$this->set('data', $data);
		$this->set('_serialize', 'data');
	
	}

}