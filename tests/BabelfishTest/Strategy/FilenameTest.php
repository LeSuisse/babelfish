<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Strategy\Filename;
use PHPUnit\Framework\TestCase;

class FilenameTest extends TestCase
{
    public function testSourceFileWithAKnownFilename(): void
    {
        $source_file = $this->createMock(SourceFile::class);
        $source_file->method('getName')->willReturn('composer.lock');

        $strategy = new Filename();
        $languages = $strategy->getLanguages($source_file);

        $this->assertCount(1, $languages);
        $this->assertSame('JSON', $languages[0]->getName());
    }

    public function testSourceFileWithAnUnknownFilename(): void
    {
        $source_file = $this->createMock(SourceFile::class);
        $source_file->method('getName')->willReturn('filename');

        $strategy = new Filename();
        $this->assertEmpty($strategy->getLanguages($source_file));
    }
}
