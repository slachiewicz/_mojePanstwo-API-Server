<?php

class ApiException extends HttpException
{
    /**
     * @var kod błędu, opisany na konkretnym API
     */
    protected $apiCode = null;

    /**
     * @var parametry błędu (niezależne od języka, specyficzne dla danego kodu błędu)
     */
    protected $params = null;

    public function __construct($apiCode, $params = array(), $message = null)
    {
        $this->apiCode = $apiCode;
        $this->params = $params;

        if (empty($message)) {
            $message = 'API exception occurred';
        }
        parent::__construct($message, 418);
    }

    public function getApiCode() {
        return $this-> apiCode;
    }

    public function getParams() {
        return $this->params;
    }
}