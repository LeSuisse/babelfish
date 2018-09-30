<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;

final class XML implements Strategy
{
    private const SEARCH_SCOPE = 2;

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file): array
    {
        $header = \implode('', \array_slice($file->getLines(), 0, self::SEARCH_SCOPE));

        if (strpos($header, '<?xml version=') !== false) {
            return [Language::findByAlias('XML')];
        }

        return [];
    }
}
