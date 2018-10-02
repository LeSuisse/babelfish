<?php

declare(strict_types=1);

namespace BabelfishTest\Internal;

use Babelfish\Internal\DataDumper;
use Babelfish\Internal\Dump;
use PHPUnit\Framework\TestCase;

class DataDumperTest extends TestCase
{
    /**
     * @expectedException \Babelfish\Internal\FileDoesNotExistException
     */
    public function testADataSourceNotFoundStopsTheDumpProcess(): void
    {
        $dump = $this->createMock(Dump::class);
        $data_dumper = new DataDumper($dump);

        $data_dumper->dump('not_existing_path');
    }
}