<?php

namespace Illimi\Academics;

use Illimi\Academics\Managers\AcademicsModuleManager;

class IllimiAcademics
{
    public function ping(): string
    {
        return 'illimi-academics installed';
    }

    public function moduleManager(): AcademicsModuleManager
    {
        return new AcademicsModuleManager();
    }

    public function menu(): array
    {
        return $this->moduleManager()->sideMenu();
    }
}
