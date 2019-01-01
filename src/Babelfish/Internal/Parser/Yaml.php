<?php

declare(strict_types=1);

namespace Babelfish\Internal\Parser;

final class Yaml implements Parser
{
    public function getParsedContent(string $content): array
    {
        return \Symfony\Component\Yaml\Yaml::parse($this->removeYamlMultiDocumentMarker($content));
    }

    private function removeYamlMultiDocumentMarker(string $content): string
    {
        return preg_replace('/^---$/m', '', $content);
    }
}
