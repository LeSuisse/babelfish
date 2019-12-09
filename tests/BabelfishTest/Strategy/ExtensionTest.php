<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Extension;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;
use PHPUnit\Framework\TestCase;

class ExtensionTest extends TestCase
{
    /**
     * @dataProvider filenamesExtensionProvider
     */
    public function testSourceFileExtension(?string $language_name, string $filename) : void
    {
        $source_file = $this->createMock(SourceFile::class);
        /** @psalm-suppress InternalMethod */
        $source_file->method('getName')->willReturn($filename);

        $pass_out_filter = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);
        /** @psalm-suppress InternalMethod */
        $pass_out_filter->method('filter')->willReturnCallback(
            static function (array $language_candidates, Language ...$found_languages) : array {
                return $found_languages;
            }
        );

        $strategy  = new Extension($pass_out_filter);
        $languages = $strategy->getLanguages($source_file);

        if ($language_name === null) {
            $this->assertEmpty($languages);
        } else {
            $this->assertCount(1, $languages);
            $this->assertEquals($language_name, $languages[0]->getName());
        }
    }

    /**
     * @psalm-return array<array{?string, string}>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function filenamesExtensionProvider() : array
    {
        return [
            ['PHP', 'test.php3'],
            [null, 'test_no_extension'],
            ['Rust', 'test.rs.in'],
            ['PHP', 'test.rs.php3'],
        ];
    }
}
