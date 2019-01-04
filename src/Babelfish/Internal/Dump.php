<?php

declare(strict_types=1);

namespace Babelfish\Internal;

use Babelfish\Internal\Generator\Generator;

class Dump
{
    /** @var string */
    private $output_path;
    /** @var Generator */
    private $generator;

    public function __construct(string $output_path, Generator $generator)
    {
        $this->output_path = $output_path;
        $this->generator   = $generator;
    }

    public function getOutputPath() : string
    {
        return $this->output_path;
    }

    public function getGenerator() : Generator
    {
        return $this->generator;
    }
}
