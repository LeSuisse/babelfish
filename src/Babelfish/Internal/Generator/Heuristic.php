<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

final class Heuristic implements Generator
{
    private const REGEX_DELIMITER = '/';
    private const HARDCODED_PATTERN_REPLACEMENT = [
        '/\A\s*[{\[]/' => '\A\s*[{\[]',
        '/\*'          => '\/\*',
    ];

    public function linguistInputFile(): string
    {
        return 'lib/linguist/heuristics.yml';
    }

    public function generate(array $heuristics): array
    {
        $existing_named_patterns = [];
        foreach ($heuristics['named_patterns'] as $name => $pattern) {
            $existing_named_patterns[$name] = $this->getPattern($pattern);
        }

        $disambiguations_by_extension = [];
        foreach ($heuristics['disambiguations'] as $disambiguation) {
            $parsed_rules_by_language = [];
            foreach ($disambiguation['rules'] as $rule) {
                $parsed_rule = $this->getParsedPatterns($rule, $existing_named_patterns);
                if (isset($rule['and'])) {
                    $parsed_rule['and'] = [];
                    foreach ($rule['and'] as $and_rule) {
                        $parsed_rule['and'][] = $this->getParsedPatterns($and_rule, $existing_named_patterns);
                    }
                }

                if (is_array($rule['language'])) {
                    $languages = $rule['language'];
                } else {
                    $languages = [$rule['language']];
                }

                foreach ($languages as $language) {
                    $parsed_rules_by_language[$language] = $parsed_rule;
                }
            }

            foreach ($disambiguation['extensions'] as $extension) {
                if (isset($disambiguations_by_extension[$extension])) {
                    throw new HeuristicMultipleExtensionException($extension);
                }
                $disambiguations_by_extension[$extension] = $parsed_rules_by_language;
            }
        }
        return $disambiguations_by_extension;
    }

    private function getParsedPatterns(array $rule, array $existing_named_patterns): array
    {
        $parsed_patterns = [];

        $positive_pattern = $this->getPositivePattern($rule, $existing_named_patterns);
        if ($positive_pattern !== null) {
            $parsed_patterns['positive'] = $positive_pattern;
        }
        if (isset($rule['negative_pattern'])) {
            if ($positive_pattern !== null) {
                throw new HeuristicRuleMultiplePatternsException($rule);
            }
            $parsed_patterns['negative'] = $this->addRegexDelimiterToPattern($this->getPattern($rule['negative_pattern']));
        }

        return $parsed_patterns;
    }

    private function getPositivePattern(array $rule, array $existing_named_patterns): ?string
    {
        $named_pattern = '';
        if (isset($rule['named_pattern'])) {
            if (! isset($existing_named_patterns[$rule['named_pattern']])) {
                throw new HeuristicNamedPatternNotFound($rule['named_pattern']);
            }
            $named_pattern = $this->getPattern($existing_named_patterns[$rule['named_pattern']]);
        }
        if (isset($rule['pattern'])) {
            return $this->addRegexDelimiterToPattern(
                $this->getPattern([$this->getPattern($rule['pattern']), $named_pattern])
            );
        }
        return null;
    }

    /**
     * @param string|string[] $pattern_rule
     * @return string
     */
    private function getPattern($pattern_rule): string
    {
        if (is_array($pattern_rule)) {
            $pattern = implode('|', array_filter($pattern_rule));
        } else {
            $pattern = $pattern_rule;
        }
        if (isset(self::HARDCODED_PATTERN_REPLACEMENT[$pattern])) {
            $pattern = self::HARDCODED_PATTERN_REPLACEMENT[$pattern];
        }

        if (@preg_match($this->addRegexDelimiterToPattern($pattern), '') === false) {
            throw new HeuristicPatternDoNotCompileException($pattern);
        }

        return $pattern;
    }

    private function addRegexDelimiterToPattern(string $pattern): string
    {
        return self::REGEX_DELIMITER . $pattern . self::REGEX_DELIMITER . 'm';
    }
}