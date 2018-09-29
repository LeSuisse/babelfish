<?php

declare(strict_types=1);

namespace Babelfish;

use Babelfish\File\SourceFile;
use Babelfish\Strategy\Strategy;

class Babelfish
{
    /**
     * @var Strategy[]
     */
    private $strategies;

    public function __construct(Strategy ...$strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file): array
    {
        $languages = [];

        foreach ($this->strategies as $strategy) {
            $languages[] = $strategy->getLanguages($file);
        }

        return array_merge(...$languages);
    }
}