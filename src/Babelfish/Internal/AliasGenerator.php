<?php

declare(strict_types=1);

namespace Babelfish\Internal;

final class AliasGenerator implements Generator
{
    public function linguistInputFile(): string
    {
        return 'lib/linguist/languages.yml';
    }

    public function generate(array $languages): array
    {
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
