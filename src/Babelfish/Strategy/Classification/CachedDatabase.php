<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Classification;

final class CachedDatabase implements Database
{
    /**
     * @return <string|<int|mixed>[]
     */
    private function getDB() : array // phpcs:ignore
    {
        static $db = null;
        if ($db === null) {
            $db = include __DIR__ . '/../../Data/ClassifierSamples.php';
        }
        return $db;
    }

    public function getTokens(string $language_name, string $token) : ?int
    {
        return $this->getDB()['tokens'][$language_name][$token] ?? null;
    }

    public function getLanguageTokens(string $language_name) : int
    {
        return $this->getDB()['language_tokens'][$language_name];
    }

    public function getTotalTokens() : int
    {
        return $this->getDB()['tokens_total'];
    }

    public function getLanguage(string $language_name) : int
    {
        return $this->getDB()['languages'][$language_name];
    }

    /**
     * @return <string|<float|mixed>[]
     */
    public function getTotalLanguages() : int
    {
        return $this->getDB()['languages_total'];
    }
}
