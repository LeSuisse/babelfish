<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use RuntimeException;

final class FileDoesNotExist extends RuntimeException
{
    public function __construct(string $path)
    {
        parent::__construct('File "' . $path . '" does not exist');
    }
}
