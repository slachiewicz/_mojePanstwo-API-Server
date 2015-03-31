<?php

class DocsController extends AppController
{
    public $components = array(
        'S3',
    );

    /**
     * @return array
     */
    public function view()
    {
    
        $document_id = $this->request->params['id'];
        
        if( $document = $this->Doc->find('first', array(
            'fields' => array('id', 'url', 'filename', 'fileextension', 'pages_count', 'packages_count', 'filesize', 'version'),
            'conditions' => array('id' => $document_id),
        )) ) {
	        
	        $_serialize = array('Document');
	        $document = $document['Doc'];
	        
	        if(
	        	isset($this->request->query['package']) && 
	        	( is_numeric( $this->request->query['package'] ) ) && 
	        	( $package = $this->request->query['package'] ) && 
	        	( $s3_response = @$this->S3->getObject('docs.sejmometr.pl', preg_replace('/{id}/', $document_id, '/htmlex/{id}/{id}_' . $package . '.html')) ) && 
	        	( $html = $s3_response->body )
	        ) {
		        		        
		        $this->set('Package', $html);
		        $_serialize[] = 'Package';
		        
	        }
	        
	        $this->set(array(
	            'Document' => $document,
	            '_serialize' => $_serialize,
	        ));
	        
	        
        } else {
	        throw new NotFoundException();
        }

    }

    /**
     * Pobiera dokument
     */
    public function html()
    {
        $document_id = $this->request->params['id'];
        $package = $this->request->params['package'];
        $html = @$this->S3->getObject('docs.sejmometr.pl', preg_replace('/{id}/', $document_id, '/htmlex/{id}/{id}_' . $package . '.html'));
        $this->set(array(
            'html' => $html->body,
            '_serialize' => array('html'),
        ));
    }
} 