<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Throwable;

final class HeuristicRuleMultiplePatternsException extends \RuntimeException
{
    public function __construct(array $rule)
    {
        parent::__construct('A pattern and negative pattern have been defined for the same rule ' . print_r($rule, true));
    }
}