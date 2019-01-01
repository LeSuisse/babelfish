<?php

declare(strict_types=1);

namespace Babelfish\Internal\Parser;

interface Parser
{
    public function getParsedContent(string $content): array;
}
