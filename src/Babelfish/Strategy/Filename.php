<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;

final class Filename implements Strategy
{
    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file, Language ...$language_candidates): array
    {
        $language = Language::findByFilename($file->getName());

        if ($language === null) {
            return [];
        }

        return [$language];
    }
}
