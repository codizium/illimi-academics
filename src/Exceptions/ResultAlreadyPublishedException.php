<?php

namespace Illimi\Academics\Exceptions;

use Exception;

class ResultAlreadyPublishedException extends Exception
{
    public function __construct(string $resultId)
    {
        parent::__construct("Result already published and cannot be modified: {$resultId}");
    }
}
