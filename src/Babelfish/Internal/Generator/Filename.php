<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Babelfish\Internal\Parser\Parser;

final class Filename implements Generator
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
     */
    public function generate(string $linguist_repo_path) : array
    {
        /** @psalm-var array<string, array{filenames?: string[]}> $languages */
        $languages = $this->parser->getParsedContent(
            $this->getContent($linguist_repo_path, $this->linguist_file)
        );

        $exported_filename = [];

        foreach ($languages as $name => $attributes) {
            if (! isset($attributes['filenames'])) {
                continue;
            }
            foreach ($attributes['filenames'] as $filename) {
                $exported_filename[$filename] = $name;
            }
        }

        return $exported_filename;
    }
}
