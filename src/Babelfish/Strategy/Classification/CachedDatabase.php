<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Classification;

final class CachedDatabase implements Database
{
    /**
     * @return int[]|mixed[]
     *
     * @psalm-return array{
     *      tokens_total: int,
     *      languages_total: int,
     *      tokens: array<string, array<string, int>>,
     *      language_tokens: array<string, int>,
     *      languages: array<string, int>
     * }
     */
    private function getDB() : array // phpcs:ignore
    {
        static $db = null;
        if ($db === null) {
            $db = (array) include __DIR__ . '/../../Data/ClassifierSamples.php';
        }

        /**
         * @psalm-var array{
         *      tokens_total: int,
         *      languages_total: int,
         *      tokens: array<string, array<string, int>>,
         *      language_tokens: array<string, int>,
         *      languages: array<string, int>
         * } $db
         */
        return $db;
    }

    public function getTokens(string $language_name, string $token): ?int
    {
        return $this->getDB()['tokens'][$language_name][$token] ?? null;
    }

    public function getLanguageTokens(string $language_name): int
    {
        return $this->getDB()['language_tokens'][$language_name];
    }

    public function getTotalTokens(): int
    {
        return $this->getDB()['tokens_total'];
    }

    public function getLanguage(string $language_name): int
    {
        return $this->getDB()['languages'][$language_name];
    }

    public function getTotalLanguages(): int
    {
        return $this->getDB()['languages_total'];
    }
}
