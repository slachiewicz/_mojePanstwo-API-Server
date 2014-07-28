<?php

class ValidationException extends HttpException
{
    protected $validation_errors = array();

    /**
     * @param array $validation_errors Associative array with field name as key and array of errors as value
     */
    public function __construct($validation_errors)
    {
        $this->validation_errors = $validation_errors;

        parent::__construct('Validation failed', 422);
    }

    public function getValidationErrors() {
        return $this->validation_errors;
    }
}