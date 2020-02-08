<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;
use Babelfish\Strategy\Modeline;
use BabelfishTest\LinguistData;
use PHPUnit\Framework\TestCase;

class ModelineTest extends TestCase
{
    /**
     * @dataProvider modelineTestCaseProvider
     */
    public function testSourceFileWithModeline(string $expected_language_name, string $linguist_fixture_path) : void
    {
        $source_file = LinguistData::getFixtureSourceFile($linguist_fixture_path);

        $pass_out_filter = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);
        /** @psalm-suppress InternalMethod */
        $pass_out_filter->method('filter')->willReturnCallback(
            static function (array $language_candidates, Language ...$found_languages) : array {
                return $found_languages;
            }
        );

        $modeline  = new Modeline($pass_out_filter);
        $languages = $modeline->getLanguages($source_file);

        $this->assertCount(1, $languages);
        $this->assertSame($expected_language_name, $languages[0]->getName());
    }

    public function testSourceFileWithoutModelineInTheHeaderOrFooter() : void
    {
        $filter    = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);
        $modeline  = new Modeline($filter);
        $languages = $modeline->getLanguages(LinguistData::getSampleSourceFile('C/main.c'));

        $this->assertEmpty($languages);
    }

    public function testNoLanguageIsReturnedWhenAliasGivenInTheModelineIsNotFound() : void
    {
        $filter   = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);
        $modeline = new Modeline($filter);

        $file = $this->createMock(SourceFile::class);
        /** @psalm-suppress InternalMethod */
        $file->method('getLines')->willReturn(['/* vim: set filetype=not_known: */']);

        $languages = $modeline->getLanguages($file);

        $this->assertEmpty($languages);
    }

    /**
     * @psalm-return array<array{string, string}>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function modelineTestCaseProvider() : array
    {
        return [
            ['Ruby', 'Data/Modelines/ruby'],
            ['Ruby', 'Data/Modelines/ruby2'],
            ['Ruby', 'Data/Modelines/ruby3'],
            ['Ruby', 'Data/Modelines/ruby4'],
            ['Ruby', 'Data/Modelines/ruby5'],
            ['Ruby', 'Data/Modelines/ruby6'],
            ['Ruby', 'Data/Modelines/ruby7'],
            ['Ruby', 'Data/Modelines/ruby8'],
            ['Ruby', 'Data/Modelines/ruby9'],
            ['Ruby', 'Data/Modelines/ruby10'],
            ['Ruby', 'Data/Modelines/ruby11'],
            ['Ruby', 'Data/Modelines/ruby12'],
            ['C++', 'Data/Modelines/seeplusplus'],
            ['C++', 'Data/Modelines/seeplusplusEmacs1'],
            ['C++', 'Data/Modelines/seeplusplusEmacs2'],
            ['C++', 'Data/Modelines/seeplusplusEmacs3'],
            ['C++', 'Data/Modelines/seeplusplusEmacs4'],
            ['C++', 'Data/Modelines/seeplusplusEmacs5'],
            ['C++', 'Data/Modelines/seeplusplusEmacs6'],
            ['C++', 'Data/Modelines/seeplusplusEmacs7'],
            ['C++', 'Data/Modelines/seeplusplusEmacs8'],
            ['C++', 'Data/Modelines/seeplusplusEmacs9'],
            ['C++', 'Data/Modelines/seeplusplusEmacs10'],
            ['C++', 'Data/Modelines/seeplusplusEmacs11'],
            ['C++', 'Data/Modelines/seeplusplusEmacs12'],
            ['Text', 'Data/Modelines/fundamentalEmacs.c'],
            ['Prolog', 'Data/Modelines/not_perl.pl'],
            ['Smalltalk', 'Data/Modelines/example_smalltalk.md'],
            ['JavaScript', 'Data/Modelines/iamjs.pl'],
            ['JavaScript', 'Data/Modelines/iamjs2.pl'],
            ['PHP', 'Data/Modelines/iamphp.inc'],
        ];
    }
}
