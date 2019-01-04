<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use RuntimeException;

final class HeuristicMultipleExtension extends RuntimeException
{
    public function __construct(string $extension)
    {
        parent::__construct('The extension ' . $extension . ' is listed for multiple disambiguations');
    }
}
