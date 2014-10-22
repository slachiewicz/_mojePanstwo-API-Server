<?php

class PrawoController extends AppController
{
		
    public function data()
    {
		
		$data = array(
			'keywords' => $this->Prawo->keywords(),
			'popular' => $this->Prawo->popular(),
			'types' => $this->Prawo->types(),
		);
		
        $this->set(array(
	    	'tags' => $data,
	    	'_serialize' => 'tags',
	    ));

    }

} 