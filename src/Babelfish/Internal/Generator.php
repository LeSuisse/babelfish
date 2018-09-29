<?php

declare(strict_types=1);

namespace Babelfish\Internal;

interface Generator
{
    public function linguistInputFile(): string;
    public function generate(array $values): array;
}
