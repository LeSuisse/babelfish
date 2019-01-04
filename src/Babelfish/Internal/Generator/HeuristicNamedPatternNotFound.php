<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use RuntimeException;

final class HeuristicNamedPatternNotFound extends RuntimeException
{
    public function __construct(string $pattern_name)
    {
        parent::__construct('Named pattern ' . $pattern_name . ' does not exist');
    }
}
