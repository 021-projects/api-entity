<?php

namespace O21\ApiEntity\Exception;

use Exception;

class InvalidJsonException extends Exception
{
    public function __construct(
        public string $response,
    ) {
        parent::__construct('Failed to get JSON props from response');
    }
}
