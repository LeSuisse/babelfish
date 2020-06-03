<?php

declare(strict_types=1);

namespace Babelfish\Internal\Parser;

use function preg_replace;

final class Yaml implements Parser
{
    /**
     * @return mixed[]
     */
    public function getParsedContent(string $content): array
    {
        return (array) \Symfony\Component\Yaml\Yaml::parse($this->removeYamlMultiDocumentMarker($content));
    }

    private function removeYamlMultiDocumentMarker(string $content): string
    {
        return preg_replace('/^---$/m', '', $content);
    }
}
