<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

final class Extension implements Generator
{
    public function linguistInputFile(): string
    {
        return 'lib/linguist/languages.yml';
    }

    public function generate(array $languages): array
    {
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