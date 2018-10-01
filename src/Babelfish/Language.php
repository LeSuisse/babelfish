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

    /**
     * @return self[]
     */
    public static function findLanguagesByInterpreter(string $interpreter): array
    {
        static $languages_indexed_by_interpreter = null;
        if ($languages_indexed_by_interpreter === null) {
            $languages_indexed_by_interpreter = include __DIR__ . '/Data/Interpreters.php';
        }
        $languages_name = $languages_indexed_by_interpreter[$interpreter] ?? [];
        $languages = [];
        foreach ($languages_name as $language_name) {
            $languages[] = new self($language_name);
        }
        return $languages;
    }

    /**
     * @return self[]
     */
    public static function findLanguagesByExtension(string $extension): array
    {
        static $languages_indexed_by_extension = null;
        if ($languages_indexed_by_extension === null) {
            $languages_indexed_by_extension = include __DIR__ . '/Data/Extensions.php';
        }
        $languages_name = $languages_indexed_by_extension[strtolower($extension)] ?? [];
        $languages = [];
        foreach ($languages_name as $language_name) {
            $languages[] = new self($language_name);
        }
        return $languages;
    }
}
