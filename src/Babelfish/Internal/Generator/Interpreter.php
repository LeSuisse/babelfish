<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Babelfish\Internal\Parser\Parser;

final class Interpreter implements Generator
{
    use GetContentFromLinguistFileTrait;

    private $linguist_file;
    private $parser;

    public function __construct(string $linguist_file, Parser $parser)
    {
        $this->linguist_file = $linguist_file;
        $this->parser = $parser;
    }

    public function generate(string $linguist_repo_path): array
    {
        $languages = $this->parser->getParsedContent(
            $this->getContent($linguist_repo_path, $this->linguist_file)
        );

        $exported_interpreter = [];

        foreach ($languages as $name => $attributes) {
            if (! isset($attributes['interpreters'])) {
                continue;
            }
            foreach ($attributes['interpreters'] as $interpreter) {
                $exported_interpreter[$interpreter][] = $name;
            }
        }

        return $exported_interpreter;
    }
}
