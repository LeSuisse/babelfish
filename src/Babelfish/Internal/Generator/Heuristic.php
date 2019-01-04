<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Babelfish\Internal\Parser\Parser;
use function array_filter;
use function implode;
use function is_array;
use function preg_match;

final class Heuristic implements Generator
{
    use GetContentFromLinguistFileTrait;

    private const REGEX_DELIMITER               = '/';
    private const HARDCODED_PATTERN_REPLACEMENT = [
        '/\A\s*[{\[]/' => '\A\s*[{\[]',
        '/\*'          => '\/\*',
    ];

    /** @var string */
    private $linguist_file;
    /** @var Parser */
    private $parser;

    public function __construct(string $linguist_file, Parser $parser)
    {
        $this->linguist_file = $linguist_file;
        $this->parser        = $parser;
    }

    /**
     * @return mixed[]
     *
     * @psalm-return array<mixed, array<mixed, array{positive?:string, negative?:string, and?:array<int, array{positive?:string, negative?:string}>}>>
     */
    public function generate(string $linguist_repo_path) : array
    {
        /**
         * @param array{
         *      named_patterns: array<string, string|string[]>,
         *      disambiguations: array{rules: string[], extensions: string[]}
         * } $heuristics
         */
        $heuristics = $this->parser->getParsedContent(
            $this->getContent($linguist_repo_path, $this->linguist_file)
        );

        /** @psalm-var array<string, string> $existing_named_patterns */
        $existing_named_patterns = [];
        /**
         * @var string $name
         * @var string|string[] $pattern
         */
        foreach ($heuristics['named_patterns'] as $name => $pattern) {
            $existing_named_patterns[$name] = $this->getPattern($pattern);
        }

        $disambiguations_by_extension = [];
        /** @psalm-var array{rules: array<string, string[][]>, extensions: string[]} $disambiguation */
        foreach ($heuristics['disambiguations'] as $disambiguation) {
            $parsed_rules_by_language = [];
            foreach ($disambiguation['rules'] as $rule) {
                /** @psalm-suppress InvalidArgument */
                $parsed_rule = $this->getParsedPatterns($rule, $existing_named_patterns);
                if (isset($rule['and'])) {
                    $parsed_rule['and'] = [];
                    foreach ($rule['and'] as $and_rule) {
                        /** @psalm-suppress InvalidArgument */
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

            /** @var string $extension */
            foreach ($disambiguation['extensions'] as $extension) {
                if (isset($disambiguations_by_extension[$extension])) {
                    throw new HeuristicMultipleExtension($extension);
                }
                $disambiguations_by_extension[$extension] = $parsed_rules_by_language;
            }
        }
        return $disambiguations_by_extension;
    }

    /**
     * @param string[] $rule
     * @param string[] $existing_named_patterns
     *
     * @return string[][]
     *
     * @psalm-return array{positive?: string, negative?: string}
     */
    private function getParsedPatterns(array $rule, array $existing_named_patterns) : array
    {
        $parsed_patterns = [];

        $positive_pattern = $this->getPositivePattern($rule, $existing_named_patterns);
        if ($positive_pattern !== null) {
            $parsed_patterns['positive'] = $positive_pattern;
        }
        if (isset($rule['negative_pattern'])) {
            if ($positive_pattern !== null) {
                throw new HeuristicRuleMultiplePatterns($rule);
            }
            $parsed_patterns['negative'] = $this->addRegexDelimiterToPattern($this->getPattern($rule['negative_pattern']));
        }

        return $parsed_patterns;
    }

    /**
     * @param string[] $rule
     * @param string[] $existing_named_patterns
     */
    private function getPositivePattern(array $rule, array $existing_named_patterns) : ?string
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
     */
    private function getPattern($pattern_rule) : string
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
            throw new HeuristicPatternDoNotCompile($pattern);
        }

        return $pattern;
    }

    private function addRegexDelimiterToPattern(string $pattern) : string
    {
        return self::REGEX_DELIMITER . $pattern . self::REGEX_DELIMITER . 'm';
    }
}
