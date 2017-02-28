<?php

namespace Symfony\Upgrade\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Upgrade\Fixer;

class Application extends BaseApplication
{
    public function __construct()
    {
        parent::__construct('EC-CUBE Upgrade Fixer', Fixer::VERSION);

        $this->add(new Command\FixCommand());
        $this->add(new Command\ReadmeCommand());
    }
}
