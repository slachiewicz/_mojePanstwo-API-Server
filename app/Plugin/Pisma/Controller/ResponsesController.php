<?php

class ResponsesController extends AppController {

    public $uses = array('Pisma.Response', 'Pisma.ResponseFile');

    public function save($letter_id) {
        try {
            if($this->Auth->user('type') != 'account')
                throw new ForbiddenException;

            $data = $this->request->data;
            $this->Response->save(array(
                'Response' => array(
                    'letter_id' => $letter_id,
                    'user_id' => $this->Auth->user('id'),
                    'title' => $data['name'],
                    'content' => $data['content'],
                    'date' => $data['date'],
                )
            ));

            if(isset($data['files']) && is_array($data['files'])) {
                $letter_response_id = $this->Response->getLastInsertID();
                foreach($data['files'] as $file) {
                    $this->ResponseFile->save(array(
                        'ResponseFile' => array(
                            'letter_response_id' => $letter_response_id,
                            'filename' => $file['filename'],
                            'src_filename' => $file['src_filename']
                        )
                    ));
                }
            }

            $this->setSerialized('response', true);
        } catch(Exception $e) {
            $this->setSerialized('error', $e->getMessage());
        }
    }

    public function getByLetter($letter_id) {
        $this->setSerialized('responses', $this->Response->find('all', array(
            /* 'fields' => array('Response.*', 'ResponseFile.*'),
            'joins' => array(
                array(
                    'table' => 'letters_responses_files',
                    'alias' => 'ResponseFile',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'ResponseFile.letter_response_id = Response.id'
                    )
                )
            ), */
            'conditions' => array(
                'Response.letter_id' => $letter_id,
                'Response.user_id' => $this->Auth->user('id'),
            ),
        )));
    }

    public function get($letter_id, $response_id)
    {
        $response = $this->Response->find('first', array(
            'conditions' => array(
                'Response.letter_id' => $letter_id,
                'Response.user_id' => $this->Auth->user('id'),
                'Response.id' => $response_id
            )
        ));


        if($response) {

            $response['Response']['files'] = $this->ResponseFile->find('all', array(
                'conditions' => array(
                    'ResponseFile.letter_response_id' => $response_id
                )
            ));

        }

        $this->setSerialized('response', $response);
    }

}