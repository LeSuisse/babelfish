<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use function pathinfo;
use function preg_match;
use function strtolower;
use function substr;

final class Heuristic implements Strategy
{
    private const HEURISTICS_CONSIDER_BYTES = 50 * 1024;

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file, Language ...$language_candidates) : array
    {
        static $heuristics_indexed_by_extension = null;
        if ($heuristics_indexed_by_extension === null) {
            $heuristics_indexed_by_extension = include __DIR__ . '/../Data/Heuristics.php';
        }

        $path_information = pathinfo($file->getName());
        $file_extension   = null;
        if (isset($path_information['extension'])) {
            $file_extension = '.' . strtolower($path_information['extension']);
        }
        if ($file_extension === null || ! isset($heuristics_indexed_by_extension[$file_extension])) {
            return [];
        }
        $heuristics = $heuristics_indexed_by_extension[$file_extension];

        $languages = [];
        $data      = $this->getDataToAnalyze($file);
        foreach ($heuristics as $language_name => $rules) {
            foreach ($language_candidates as $language_candidate) {
                if ($language_name !== $language_candidate->getName() || ! $this->validateRules($data, $rules)) {
                    continue;
                }

                $languages[] = $language_candidate;
            }
        }

        return $languages;
    }

    private function getDataToAnalyze(SourceFile $file) : string
    {
        $data = '';
        foreach ($file->getLines() as $line) {
            $data .= $line . "\n";
            if ($data >= self::HEURISTICS_CONSIDER_BYTES) {
                break;
            }
        }
        return substr($data, 0, self::HEURISTICS_CONSIDER_BYTES);
    }

    /**
     * @param string[] $rules
     */
    private function validateRules(string $data, array $rules) : bool
    {
        if (! isset($rules['and'])) {
            return $this->validateSimpleRule($data, $rules);
        }

        foreach ($rules['and'] as $rule) {
            $is_rule_valid = $this->validateSimpleRule($data, $rule);
            if (! $is_rule_valid) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[] $rule
     */
    private function validateSimpleRule(string $data, array $rule) : bool
    {
        if (isset($rule['positive'])) {
            return preg_match($rule['positive'], $data) === 1;
        }

        if (isset($rules['negative'])) {
            return preg_match($rule['positive'], $data) === 0;
        }

        return true;
    }
}
