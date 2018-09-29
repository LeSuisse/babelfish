<?php

declare(strict_types=1);

namespace Babelfish\Internal;

class FileDoesNotExistException extends \RuntimeException
{
    public function __construct(string $path)
    {
        parent::__construct('File "' . $path . '" does not exist');
    }
}
