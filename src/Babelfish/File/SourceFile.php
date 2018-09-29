<?php

declare(strict_types=1);

namespace Babelfish\File;

interface SourceFile
{
    public function getName(): string;

    /**
     * @return string[]
     */
    public function getLines(): array;
}
