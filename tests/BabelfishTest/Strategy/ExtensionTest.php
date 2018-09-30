<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Strategy\Extension;
use PHPUnit\Framework\TestCase;

class ExtensionTest extends TestCase
{
    /**
     * @dataProvider filenamesExtensionProvider
     */
    public function testSourceFileExtension(?string $language_name, string $filename): void
    {
        $source_file = $this->createMock(SourceFile::class);
        $source_file->method('getName')->willReturn($filename);

        $strategy = new Extension();
        $languages = $strategy->getLanguages($source_file);

        if ($language_name === null) {
            $this->assertEmpty($languages);
        } else {
            $this->assertCount(1, $languages);
            $this->assertEquals($language_name, $languages[0]->getName());
        }
    }

    public function filenamesExtensionProvider(): array
    {
        return [
            ['PHP', 'test.php3'],
            [null, 'test_no_extension'],
            ['Rust', 'test.rs.in'],
            ['PHP', 'test.rs.php3'],
        ];
    }
}
