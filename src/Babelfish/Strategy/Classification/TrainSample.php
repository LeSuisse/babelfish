<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Classification;

interface TrainSample
{
    public function getLanguageName(): string;
    public function getContent(): string;
}
