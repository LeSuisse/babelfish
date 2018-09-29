<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

interface Generator
{
    public function linguistInputFile(): string;
    public function generate(array $values): array;
}
