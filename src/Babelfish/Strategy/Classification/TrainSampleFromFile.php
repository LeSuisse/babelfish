<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Classification;

use function file_get_contents;

final class TrainSampleFromFile implements TrainSample
{
    /** @var string */
    private $language_name;
    /** @var string */
    private $file_path;

    public function __construct(string $language_name, string $file_path)
    {
        $this->language_name = $language_name;
        $this->file_path     = $file_path;
    }

    public function getLanguageName(): string
    {
        return $this->language_name;
    }

    public function getContent(): string
    {
        return file_get_contents($this->file_path);
    }
}
