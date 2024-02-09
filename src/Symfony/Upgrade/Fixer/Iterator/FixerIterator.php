<?php

namespace Symfony\Upgrade\Fixer\Iterator;

use Symfony\Component\Finder\Finder;

class FixerIterator implements \IteratorAggregate
{
    protected $fixers = [];

    public function __construct()
    {
        foreach (Finder::create()->files()->in(__DIR__.'/..')->sortByName()->depth(0) as $file) {
            $class = 'Symfony\\Upgrade\\Fixer\\'.$file->getBasename('.php');
            var_dump($class."が書き換え対象かを判定");
            if ((new \ReflectionClass($class))->isAbstract()) {
                var_dump("abstractなので対象ではない");
                continue;
            }else{
                var_dump("書き換え対象です");
            }

            $this->fixers[] = new $class();
            //var_dump("書き換え対象は".$this->fixers);
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->fixers);
    }
}
