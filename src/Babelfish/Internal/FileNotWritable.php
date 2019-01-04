<?php

declare(strict_types=1);

namespace Babelfish\Internal;

use RuntimeException;

class FileNotWritable extends RuntimeException
{
    public function __construct(string $path)
    {
        parent::__construct('File "' . $path . '" is not writable');
    }
}
