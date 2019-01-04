<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use LogicException;

final class ExpectedLanguageNotFound extends LogicException
{
    public function __construct(string $language_alias_not_found)
    {
        parent::__construct('Language alias ' . $language_alias_not_found . ' should exist, check the data generation');
    }
}
