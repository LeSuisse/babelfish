<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;

interface Strategy
{
    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file): array;
}
