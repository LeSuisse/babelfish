<?php

declare(strict_types=1);

namespace BabelfishTest;

use Babelfish\Babelfish;
use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Strategy;
use PHPUnit\Framework\TestCase;

class BabelfishTest extends TestCase
{
    public function testAllLanguagesRetrievedByTheStrategiesAreReported(): void
    {
        $language_1 = $this->createMock(Language::class);
        $language_2 = $this->createMock(Language::class);
        $language_3 = $this->createMock(Language::class);

        $strategy_1 = $this->createMock(Strategy::class);
        $strategy_1->method('getLanguages')->willReturn([$language_1, $language_2]);
        $strategy_2 = $this->createMock(Strategy::class);
        $strategy_2->method('getLanguages')->willReturn([$language_3]);

        $babelfish = new Babelfish($strategy_1, $strategy_2);

        $file = $this->createMock(SourceFile::class);

        $languages = $babelfish->getLanguages($file);

        $this->assertCount(3, $languages);
        $this->assertContains($language_1, $languages);
        $this->assertContains($language_2, $languages);
        $this->assertContains($language_3, $languages);
    }
}
