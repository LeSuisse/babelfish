<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use RuntimeException;

final class HeuristicPatternDoNotCompile extends RuntimeException
{
    public function __construct(string $pattern)
    {
        parent::__construct('Not able to compile ' . $pattern);
    }
}
