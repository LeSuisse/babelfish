<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

final class Interpreter implements Generator
{
    public function linguistInputFile(): string
    {
        return 'lib/linguist/languages.yml';
    }

    public function generate(array $languages): array
    {
        $exported_interpreter = [];

        foreach ($languages as $name => $attributes) {
            if (! isset($attributes['interpreters'])) {
                continue;
            }
            foreach ($attributes['interpreters'] as $interpreter) {
                $exported_interpreter[$interpreter] = $name;
            }
        }

        return $exported_interpreter;
    }
}
