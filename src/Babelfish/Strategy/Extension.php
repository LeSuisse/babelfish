<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;

use function pathinfo;

use const PATHINFO_EXTENSION;

final class Extension implements Strategy
{
    /** @var OnlyKeepLanguageAlreadyCandidatesFilter */
    private $filter;

    public function __construct(OnlyKeepLanguageAlreadyCandidatesFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file, Language ...$language_candidates): array
    {
        /** @psalm-var array{extension: string, filename: string} $path_information */
        $path_information = pathinfo($file->getName());
        if (! isset($path_information['extension'])) {
            return [];
        }

        $extension             = $path_information['extension'];
        $second_extension_part = pathinfo($path_information['filename'], PATHINFO_EXTENSION);
        if ($second_extension_part !== '') {
            $composite_extension = '.' . $second_extension_part . '.' . $extension;
            $languages           = Language::findLanguagesByExtension($composite_extension);
            if (! empty($languages)) {
                return $this->filter->filter($language_candidates, ...$languages);
            }
        }

        $languages = Language::findLanguagesByExtension('.' . $extension);

        return $this->filter->filter($language_candidates, ...$languages);
    }
}
