<?php

declare(strict_types=1);

namespace Babelfish;

class Language
{
    /**
     * @var string
     */
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function findByAlias(string $alias): ?self
    {
        static $languages_indexed_by_alias = null;
        if ($languages_indexed_by_alias === null) {
            $languages_indexed_by_alias = include __DIR__ . '/Data/Aliases.php';
        }
        $language_name = $languages_indexed_by_alias[str_replace(' ', '-', strtolower($alias))] ?? null;
        if ($language_name === null) {
            return null;
        }
        return new self($language_name);
    }

    public static function findByFilename(string $filename): ?self
    {
        static $languages_indexed_by_filename = null;
        if ($languages_indexed_by_filename === null) {
            $languages_indexed_by_filename = include __DIR__ . '/Data/Filenames.php';
        }
        $language_name = $languages_indexed_by_filename[$filename] ?? null;
        if ($language_name === null) {
            return null;
        }
        return new self($language_name);
    }
}
