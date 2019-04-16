<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Classification\Database;
use Babelfish\Strategy\Tokenizer\Tokenizer;
use function key;
use function log;
use function reset;
use function substr;
use function uasort;

final class Classifier implements Strategy
{
    private const CLASSIFIER_CONSIDER_BYTES = 50 * 1024;
    /** @var Tokenizer */
    private $tokenizer;
    /** @var Database */
    private $database;

    public function __construct(Tokenizer $tokenizer, Database $database)
    {
        $this->tokenizer = $tokenizer;
        $this->database  = $database;
    }

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file, Language ...$language_candidates) : array
    {
        if (empty($language_candidates)) {
            return [];
        }

        $language_names              = [];
        $language_candidates_by_name = [];
        foreach ($language_candidates as $language_candidate) {
            $language_names[]                                            = $language_candidate->getName();
            $language_candidates_by_name[$language_candidate->getName()] = $language_candidate;
        }

        $data_to_analyze = $this->getDataToAnalyze($file);

        $sorted_language_names = $this->classify($data_to_analyze, $language_names);

        reset($sorted_language_names);

        return [$language_candidates_by_name[key($sorted_language_names)]];
    }

    private function getDataToAnalyze(SourceFile $file) : string
    {
        $data = '';
        foreach ($file->getLines() as $line) {
            $data .= $line . "\n";
            if ($data >= self::CLASSIFIER_CONSIDER_BYTES) {
                break;
            }
        }

        return substr($data, 0, self::CLASSIFIER_CONSIDER_BYTES);
    }

    /**
     * @param string[] $language_names
     *
     * @return float[]
     *
     * @psalm-return array<string, float>
     */
    private function classify(string $data, array $language_names) : array
    {
        $tokens = $this->tokenizer->extractTokens($data);
        $scores = [];

        foreach ($language_names as $language_name) {
            $scores[$language_name] = $this->getTokensProbability($language_name, $tokens) +
                $this->getLanguageProbability($language_name);
        }

        uasort(
            $scores,
            static function (float $a, float $b) : int {
                return ($a <=> $b) * -1;
            }
        );

        /** @psalm-var array<string, float> $scores */
        return $scores;
    }

    /**
     * @param string[] $tokens
     */
    private function getTokensProbability(string $language_name, array $tokens) : float
    {
        $sum = 0;

        foreach ($tokens as $token) {
            $sum += log($this->getTokenProbability($language_name, $token));
        }

        return $sum;
    }

    private function getTokenProbability(string $language_name, string $token) : float
    {
        $token_nb = $this->database->getTokens($language_name, $token);

        if ($token_nb === null) {
            return 1 / $this->database->getTotalTokens();
        }

        return $token_nb / $this->database->getLanguageTokens($language_name);
    }

    private function getLanguageProbability(string $language_name) : float
    {
        return log($this->database->getLanguage($language_name) / $this->database->getTotalLanguages());
    }
}
