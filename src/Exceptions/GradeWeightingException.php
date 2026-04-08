<?php

namespace Illimi\Academics\Exceptions;

use Exception;

class GradeWeightingException extends Exception
{
    public function __construct(string $message = 'Grade component weightings must sum to 100%.')
    {
        parent::__construct($message);
    }
}
