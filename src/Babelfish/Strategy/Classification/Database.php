<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Classification;

interface Database
{
    public function getTokens(string $language_name, string $token): ?int;

    public function getLanguageTokens(string $language_name): int;

    public function getTotalTokens(): int;

    public function getLanguage(string $language_name): int;

    public function getTotalLanguages(): int;
}
