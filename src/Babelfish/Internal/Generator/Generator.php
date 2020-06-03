<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

interface Generator
{
    /**
     * @return mixed[]
     */
    public function generate(string $linguist_repository_path): array;
}
