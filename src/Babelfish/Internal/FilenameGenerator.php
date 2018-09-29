<?php

declare(strict_types=1);

namespace Babelfish\Internal;

final class FilenameGenerator implements Generator
{
    public function linguistInputFile(): string
    {
        return 'lib/linguist/languages.yml';
    }

    public function generate(array $languages): array
    {
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
