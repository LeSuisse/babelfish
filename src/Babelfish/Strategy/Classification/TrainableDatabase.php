<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Classification;

use Babelfish\Strategy\Tokenizer\Tokenizer;

final class TrainableDatabase implements Database
{
    private $db = [
        'tokens_total' => 0,
        'languages_total' => 0,
        'tokens' => [],
        'language_tokens' => [],
        'languages' => []
    ];

    public function __construct(Tokenizer $tokenizer, TrainSample ...$train_samples)
    {
        foreach ($train_samples as $train_sample) {
            $this->train($tokenizer, $train_sample);
        }
    }

    private function train(Tokenizer $tokenizer, TrainSample $train_sample): void
    {
        if (! isset($this->db['languages'][$train_sample->getLanguageName()])) {
            $this->db['languages'][$train_sample->getLanguageName()] = 0;
        }
        $this->db['languages'][$train_sample->getLanguageName()] += 1;
        $this->db['languages_total'] += 1;

        $tokens = $tokenizer->extractTokens($train_sample->getContent());
        foreach ($tokens as $token) {
            $this->db['tokens_total'] += 1;

            if (! isset($this->db['tokens'][$train_sample->getLanguageName()])) {
                $this->db['tokens'][$train_sample->getLanguageName()] = [];
            }
            if (! isset($this->db['tokens'][$train_sample->getLanguageName()][$token])) {
                $this->db['tokens'][$train_sample->getLanguageName()][$token] = 0;
            }
            $this->db['tokens'][$train_sample->getLanguageName()][$token] += 1;

            if (! isset($this->db['language_tokens'][$train_sample->getLanguageName()])) {
                $this->db['language_tokens'][$train_sample->getLanguageName()] = 0;
            }
            $this->db['language_tokens'][$train_sample->getLanguageName()] += 1;
        }
    }

    public function getTokens(string $language_name, string $token): ?int
    {
        return $this->db['tokens'][$language_name][$token] ?? null;
    }

    public function getLanguageTokens(string $language_name): int
    {
        return $this->db['language_tokens'][$language_name];
    }

    public function getTotalTokens(): int
    {
        return $this->db['tokens_total'];
    }

    public function getLanguage(string $language_name): int
    {
        return $this->db['languages'][$language_name];
    }

    public function getTotalLanguages(): int
    {
        return $this->db['languages_total'];
    }

    public function getRawDatabase(): array
    {
        return $this->db;
    }
}
