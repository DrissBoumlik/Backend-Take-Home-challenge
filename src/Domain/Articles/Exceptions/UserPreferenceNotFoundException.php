<?php

namespace Domain\Articles\Exceptions;

use Exception;

class UserPreferenceNotFoundException extends Exception
{
    public function __construct($message = "User Preferences Not Found", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
