<?php
App::uses('ExceptionRenderer', 'Error');

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
            'message' => h($message),
            '_serialize' => array('code', // kod błędu, opisany na konkretnym API
                'params', // parametry błędu (niezależne od języka, specyficzne dla danego kodu błędu)
                'message') // Długi opis po angielsku
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

    /**
     */

    /**
     * Overriding _getController to use MPResponse instead of default one
     *
     * @see ExceptionRenderer::_getController
     * @param Exception $exception
     * @return CakeErrorController|Controller
     */
    protected function _getController($exception)
    {
        App::uses('AppController', 'Controller');
        App::uses('CakeErrorController', 'Controller');
        if (!$request = Router::getRequest(true)) {
            $request = new CakeRequest();
        }
        $response = new MPResponse();

        if (method_exists($exception, 'responseHeader')) {
            $response->header($exception->responseHeader());
        }

        if (class_exists('AppController')) {
            try {
                $controller = new CakeErrorController($request, $response);
                $controller->startupProcess();
            } catch (Exception $e) {
                if (!empty($controller) && $controller->Components->enabled('RequestHandler')) {
                    $controller->RequestHandler->startup($controller);
                }
            }
        }
        if (empty($controller)) {
            $controller = new Controller($request, $response);
            $controller->viewPath = 'Errors';
        }
        return $controller;
    }
} 