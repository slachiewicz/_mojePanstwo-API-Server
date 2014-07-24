<?php

class DocsController extends AppController
{
    public $components = array(
        'S3',
    );

    /**
     * @return array
     */
    public function info()
    {
    
        $document_id = $this->request->params['id'];
        $document = $this->Doc->find('first', array(
            'fields' => array('id', 'url', 'filename', 'fileextension', 'pages_count', 'packages_count', 'filesize', 'version'),
            'conditions' => array('id' => $document_id),
        ));

        $this->set(array(
            'document' => $document,
            '_serialize' => array('document'),
        ));

        return $document;
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