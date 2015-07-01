<?php
App::uses('ExceptionRenderer', 'Error');
App::uses('MPResponse', 'Lib');

class MPExceptionRenderer extends ExceptionRenderer {
    public function api($error)
    {
        $message = $error->getMessage();
        $url = $this->controller->request->here();
        $this->controller->response->statusCode($error->getCode());

        $this->controller->set(array(
            'name' => h($message),
            'url' => h($url),
            'error' => $error,
            'code' => $error->getApiCode(),
            'params' => $error->getParams(),
            'error_description' => h($message),
            '_serialize' => array('code', // kod błędu, opisany na konkretnym API
                'params', // parametry błędu (niezależne od języka, specyficzne dla danego kodu błędu)
                'error_description') // Długi opis po angielsku
        ));

        $this->_outputMessage('error400');
    }

    public function validation($error)
    {
        $message = $error->getMessage();
        $url = $this->controller->request->here();
        $this->controller->response->statusCode($error->getCode());

        $this->controller->set(array(
            'name' => h($message),
            'url' => h($url),
            'errors' => $error->getValidationErrors(),
            'error' => $error,
            '_serialize' => array('errors')
        ));

        $this->_outputMessage('error400');
    }
} 