<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;

final class Extension implements Strategy
{
    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file, Language ...$language_candidates): array
    {
        $path_information = \pathinfo($file->getName());
        if (! isset($path_information['extension'])) {
            return [];
        }

        $extension = $path_information['extension'];
        $second_extension_part = \pathinfo($path_information['filename'], \PATHINFO_EXTENSION);
        if ($second_extension_part !== '') {
            $composite_extension = ".$second_extension_part.$extension";
            $languages = Language::findLanguagesByExtension($composite_extension);
            if (! empty($languages)) {
                return $languages;
            }
        }

        return Language::findLanguagesByExtension(".$extension");
    }
}
