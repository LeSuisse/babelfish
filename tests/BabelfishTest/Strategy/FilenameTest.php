<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Filename;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;
use PHPUnit\Framework\TestCase;

class FilenameTest extends TestCase
{
    public function testSourceFileWithAKnownFilename() : void
    {
        $source_file = $this->createMock(SourceFile::class);
        $source_file->method('getName')->willReturn('composer.lock');

        $pass_out_filter = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);
        $pass_out_filter->method('filter')->willReturnCallback(
            static function (array $language_candidates, Language ...$found_languages) {
                return $found_languages;
            }
        );

        $strategy  = new Filename($pass_out_filter);
        $languages = $strategy->getLanguages($source_file);

        $this->assertCount(1, $languages);
        $this->assertSame('JSON', $languages[0]->getName());
    }

    public function testSourceFileWithAnUnknownFilename() : void
    {
        $source_file = $this->createMock(SourceFile::class);
        $source_file->method('getName')->willReturn('filename');

        $filter = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);

        $strategy = new Filename($filter);
        $this->assertEmpty($strategy->getLanguages($source_file));
    }
}
