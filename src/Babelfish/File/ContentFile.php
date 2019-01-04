<?php

declare(strict_types=1);

namespace Babelfish\File;

use function explode;

final class ContentFile implements SourceFile
{
    /** @var string */
    private $name;
    /** @var string */
    private $content;

    public function __construct(string $name, string $content)
    {
        $this->name    = $name;
        $this->content = $content;
    }

    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getLines() : array
    {
        return explode("\n", $this->content);
    }
}
