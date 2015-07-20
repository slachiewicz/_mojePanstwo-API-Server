<?php

class AnalyzerController extends AppController
{

    public $uses = array(
        'Admin.Analyzer',
        'Admin.AnalyzerExecution',
    );

    public $components = array(
        'RequestHandler'
    );

    public function view()
    {

        $id = $this->request->params['name'];

        $analyzer = $this->Analyzer->find('first', array(
            'conditions' => array(
                'Analyzer.id' => $id,
            ),
        ));

        $this->set('_serialize', array('analyzer'));

        $this->setSerialized('object', $analyzer);
    }
}