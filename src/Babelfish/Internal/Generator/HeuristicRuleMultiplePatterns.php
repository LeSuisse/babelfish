<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use RuntimeException;
use function print_r;

final class HeuristicRuleMultiplePatterns extends RuntimeException
{
    /**
     * @param string[] $rule
     *
     * @psalm-param array{named_pattern?: string, pattern?: string|string[], negative_pattern?: string|string[]} $rule
     */
    public function __construct(array $rule)
    {
        parent::__construct('A pattern and negative pattern have been defined for the same rule ' . print_r($rule, true));
    }
}
