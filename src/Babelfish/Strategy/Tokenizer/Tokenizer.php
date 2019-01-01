<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Tokenizer;

final class Tokenizer
{
    private const SHEBANG_TOKEN = 'SHEBANG#!';

    private const REGEX_SHEBANG_WITH_ENV = '/^#![ \t]*([[:alnum:]_\/]*\/)?env([ \t]+([^ \t=]*=[^ \t]*))*[ \t]+[[:alpha:]_]+/';
    private const REGEX_SHEBANG = '/^#![ \t]*[[:alpha:]_\/]+/';
    private const REGEX_SGML = '/(<\/?[^\s<>=\d"\']+)(?:\s(.|\n)*?\/?>|>)/';
    private const REGEX_SGML_COMMENT = '<!--(.|\n)*?-->';
    private const REGEX_SGML_ATTRIBUTE = '/\s+(\w+=)|\s+([^\s>]+)/';
    private const REGEX_SGML_LONE_ATTRIBUTE = '/^\w+$/';
    private const REGEX_LITERAL_STRING_QUOTES = '/"(?:.|\n)*?(?<!\\\\)(?:"|$)|\'(?:.|\n)*?(?<!\\\\)(?:\'|$)/';
    private const REGEX_MULTILINE_COMMENT = '~/\*(.|\n)*?\*/|<!--(.|\n)*?-->|\{-(.|\n)*?-\}|\(\*(.|\n)*?\*\)|"""(.|\n)*?"""|\'\'\'(.|\n)*?\'\'\'~';
    private const REGEX_SINGLE_LINE_COMMENT = '~^(//|--|#|%|")\s([^\n]*$)~m';
    private const REGEX_PUNCTUATION = '/;|\{|\}|\(|\)|\[|\]/';
    private const REGEX_LITERAL_NUMBER = '/(^|\h|<<?|\+|\-|\*|\/|%|:|&&?|\|\|?)(0x[0-9A-Fa-f]([0-9A-Fa-f]|\.)*|\d(\d|\.)*)([uU][lL]{0,2}|([eE][-+]\d*)?[fFlL]*)/';
    private const REGEX_REGULAR_TOKEN = '/[0-9A-Za-z_\.@#\/\*]+/';
    private const REGEX_OPERATOR = '/<<?|\+|\-|\*|\/|%|&&?|\|\|?/';

    /**
     * @return string[]
     */
    public function extractTokens(string $content): array
    {
        $tokens = [];
        // Shebang
        $content = (string) preg_replace_callback(
            self::REGEX_SHEBANG_WITH_ENV,
            function ($matches) use (&$tokens) {
                $match = strrchr($matches[0], ' ');
                if ($match === false) {
                    $match = $matches[0];
                } else {
                    $match = substr($match, 1);
                }
                $tokens[] = self::SHEBANG_TOKEN . $match;
                return ' ';
            },
            $content
        );
        $content = (string) preg_replace_callback(
            self::REGEX_SHEBANG,
            function ($matches) use (&$tokens) {
                $match = strrchr($matches[0], '/');
                if ($match === false) {
                    $match = $matches[0];
                } else {
                    $match = substr($match, 1);
                }
                if ($match !== 'env') {
                    $tokens[] = self::SHEBANG_TOKEN . $match;
                }
                return ' ';
            },
            $content
        );

        // SGML
        $content = (string) preg_replace_callback(
            self::REGEX_SGML,
            function ($matches) use (&$tokens) {
                if (preg_match(self::REGEX_SGML_COMMENT, $matches[0]) === 1) {
                    return ' ';
                }
                $tokens[] = $matches[1] . '>';

                // Attributes
                preg_replace_callback(
                    self::REGEX_SGML_ATTRIBUTE,
                    function ($matches) use (&$tokens) {
                        if ($matches[1] !== '') {
                            $tokens[] = $matches[1];
                        }
                        if (isset($matches[2]) &&
                            preg_match(self::REGEX_SGML_LONE_ATTRIBUTE, $matches[2], $lone_attribute) === 1) {
                            $tokens[] = $lone_attribute[0];
                        }

                        return '';
                    },
                    $matches[0]
                );
                return ' ';
            },
            $content
        );
        // Skip comments and literals
        $content = (string) preg_replace(
            [
                self::REGEX_LITERAL_STRING_QUOTES,
                self::REGEX_MULTILINE_COMMENT,
                self::REGEX_SINGLE_LINE_COMMENT,
            ],
            ' ',
            $content
        );

        // Punctuations
        $match_and_replace_callback = function (array $matches) use (&$tokens): string {
            $tokens[] = $matches[0];
            return ' ';
        };
        $content = (string) preg_replace_callback(
            self::REGEX_PUNCTUATION,
            $match_and_replace_callback,
            $content
        );

        // Skip literal number
        $content = (string) preg_replace_callback(
            self::REGEX_LITERAL_NUMBER,
            function ($matches) {
                return $matches[1] . ' ';
            },
            $content
        );

        // Regulars
        $content = (string) preg_replace_callback(
            self::REGEX_REGULAR_TOKEN,
            $match_and_replace_callback,
            $content
        );

        // Operators
        preg_replace_callback(
            self::REGEX_OPERATOR,
            $match_and_replace_callback,
            $content
        );

        return $tokens;
    }
}
