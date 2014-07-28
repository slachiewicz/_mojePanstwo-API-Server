<?php

class MPResponse extends CakeResponse {
    public function __construct(array $options = array()) {
        parent::__construct($options);

        # additional status codes
        $this->_statusCodes[418] = 'MPApiException';
        $this->_statusCodes[422] = 'MPValidationException';
    }
} 