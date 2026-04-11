<?php

namespace Illimi\Academics\Facades;

use Illuminate\Support\Facades\Facade;

class IllimiAcademics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'illimi-academics';
    }
}
