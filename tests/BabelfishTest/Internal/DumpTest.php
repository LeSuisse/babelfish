<?php

declare(strict_types=1);

namespace BabelfishTest\Internal;

use Babelfish\Internal\Dump;
use Babelfish\Internal\Generator\Generator;
use PHPUnit\Framework\TestCase;

class DumpTest extends TestCase
{
    public function testDumpHoldValues(): void
    {
        $path      = 'mypath';
        $generator = $this->createMock(Generator::class);

        $dump = new Dump($path, $generator);

        $this->assertSame($path, $dump->getOutputPath());
        $this->assertSame($generator, $dump->getGenerator());
    }
}
