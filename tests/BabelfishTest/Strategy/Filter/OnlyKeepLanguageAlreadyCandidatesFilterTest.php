<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy\Filter;

use Babelfish\Language;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;
use PHPUnit\Framework\TestCase;

class OnlyKeepLanguageAlreadyCandidatesFilterTest extends TestCase
{
    public function testFoundLanguagesNotAlreadyCandidatesAreFilteredOut() : void
    {
        $language_candidate_a = $this->createMock(Language::class);
        $language_candidate_a->method('getName')->willReturn('A');
        $language_candidate_b = $this->createMock(Language::class);
        $language_candidate_b->method('getName')->willReturn('B');

        $language_found_a = $this->createMock(Language::class);
        $language_found_a->method('getName')->willReturn('A');
        $language_found_b = $this->createMock(Language::class);
        $language_found_b->method('getName')->willReturn('B');
        $language_found_c = $this->createMock(Language::class);
        $language_found_c->method('getName')->willReturn('C');

        $filter             = new OnlyKeepLanguageAlreadyCandidatesFilter();
        $filtered_languages = $filter->filter(
            [$language_candidate_a, $language_candidate_b],
            $language_found_a,
            $language_found_c,
            $language_found_b
        );

        $this->assertSame([$language_found_a, $language_found_b], $filtered_languages);
    }

    public function testAllFoundLanguagesAreReturnedWhenNoCandidate() : void
    {
        $language_found_a = $this->createMock(Language::class);
        $language_found_a->method('getName')->willReturn('A');
        $language_found_b = $this->createMock(Language::class);
        $language_found_b->method('getName')->willReturn('B');

        $filter             = new OnlyKeepLanguageAlreadyCandidatesFilter();
        $filtered_languages = $filter->filter([], $language_found_a, $language_found_b);

        $this->assertSame([$language_found_a, $language_found_b], $filtered_languages);
    }
}
