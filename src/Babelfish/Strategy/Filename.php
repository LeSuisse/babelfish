<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;

final class Filename implements Strategy
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
    public function getLanguages(SourceFile $file, Language ...$language_candidates) : array
    {
        $language = Language::findByFilename($file->getName());

        if ($language === null) {
            return [];
        }

        return $this->filter->filter($language_candidates, $language);
    }
}
