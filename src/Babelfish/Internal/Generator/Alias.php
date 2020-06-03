<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Babelfish\Internal\Parser\Parser;

use function str_replace;
use function strtolower;

final class Alias implements Generator
{
    use GetContentFromLinguistFile;

    /** @var string */
    private $linguist_file;
    /** @var Parser */
    private $parser;

    public function __construct(string $linguist_file, Parser $parser)
    {
        $this->linguist_file = $linguist_file;
        $this->parser        = $parser;
    }

    /**
     * @return string[]
     *
     * @psalm-return array<string|string>
     */
    public function generate(string $linguist_repo_path): array
    {
        /** @psalm-var array<string, array{aliases?: string[]}> $languages */
        $languages = $this->parser->getParsedContent(
            $this->getContent($linguist_repo_path, $this->linguist_file)
        );

        $exported_aliases = [];

        foreach ($languages as $name => $attributes) {
            $exported_aliases[$this->formatKey($name)] = $name;
            if (! isset($attributes['aliases'])) {
                continue;
            }

            foreach ($attributes['aliases'] as $alias) {
                $exported_aliases[$this->formatKey($alias)] = $name;
            }
        }

        return $exported_aliases;
    }

    private function formatKey(string $alias): string
    {
        return str_replace(' ', '-', strtolower($alias));
    }
}
