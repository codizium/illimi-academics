<?php

namespace Illimi\Academics\Exceptions;

use Exception;

class ExamNotFoundException extends Exception
{
    public function __construct(string $examId)
    {
        parent::__construct("Exam not found with ID: {$examId}");
    }
}
