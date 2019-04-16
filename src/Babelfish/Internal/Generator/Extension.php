<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Babelfish\Internal\Parser\Parser;

final class Extension implements Generator
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
     * @return string[][]
     */
    public function generate(string $linguist_repo_path) : array
    {
        /** @psalm-var array<string, array{extensions?: string[]}> $languages */
        $languages = $this->parser->getParsedContent(
            $this->getContent($linguist_repo_path, $this->linguist_file)
        );

        $exported_extension = [];

        foreach ($languages as $name => $attributes) {
            if (! isset($attributes['extensions'])) {
                continue;
            }
            foreach ($attributes['extensions'] as $filename) {
                $exported_extension[$filename][] = $name;
            }
        }

        return $exported_extension;
    }
}
