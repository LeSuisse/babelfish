<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Filter;

use Babelfish\Language;

class OnlyKeepLanguageAlreadyCandidatesFilter
{
    /**
     * @param Language[] $language_candidates
     * @param Language   ...$found_languages
     *
     * @return Language[]
     */
    public function filter(array $language_candidates, Language ...$found_languages) : array
    {
        if (empty($language_candidates)) {
            return $found_languages;
        }

        $language_candidate_names = [];
        foreach ($language_candidates as $language_candidate) {
            $language_candidate_names[$language_candidate->getName()] = true;
        }

        $filtered_languages = [];
        foreach ($found_languages as $found_language) {
            if (! isset($language_candidate_names[$found_language->getName()])) {
                continue;
            }

            $filtered_languages[] = $found_language;
        }
        return $filtered_languages;
    }
}
