<?php

declare(strict_types=1);

namespace Babelfish\Strategy\Tokenizer;

use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function strrchr;
use function substr;

final class Tokenizer
{
    private const SHEBANG_TOKEN = 'SHEBANG#!';

    private const REGEX_SHEBANG_WITH_ENV      = '/^#![ \t]*(?:[[:alnum:]_\/]*\/)?env(?:[ \t]+(?:[^ \t=]*=[^ \t]*))*[ \t]+[[:alpha:]_]+/';
    private const REGEX_SHEBANG               = '/^#![ \t]*[[:alpha:]_\/]+/';
    private const REGEX_SGML                  = '/(<\/?[^\s<>=\d"\']+)(\s.*?\/?>|>)/s';
    private const REGEX_SGML_COMMENT          = '/<!--.*?-->/s';
    private const REGEX_SGML_ATTRIBUTE        = '/\s+(\w+=)|\s+([^\s>]+)/';
    private const REGEX_SGML_LONE_ATTRIBUTE   = '/^\w+$/';
    private const REGEX_LITERAL_STRING_QUOTES = '/".*?(?<!\\\\)(?:"|$)|\'.*?(?<!\\\\)(?:\'|$)/s';
    private const REGEX_MULTILINE_COMMENT     = '~/\*.*?\*/|<!--.*?-->|\{-.*?-\}|\(\*.*?\*\)|""".*?"""|\'\'\'.*?\'\'\'~s';
    private const REGEX_SINGLE_LINE_COMMENT   = '~^(?://|--|#|%|")\s(?:[^\n]*$)~m';
    private const REGEX_PUNCTUATION           = '/;|\{|\}|\(|\)|\[|\]/';
    private const REGEX_LITERAL_NUMBER        = '/(^|\h|<<?|\+|\-|\*|\/|%|:|&&?|\|\|?)(?:0x[0-9A-Fa-f](?:[0-9A-Fa-f]|\.)*|\d(?:\d|\.)*)(?:[uU][lL]{0,2}|(?:[eE][-+]\d*)?[fFlL]*)/';
    private const REGEX_REGULAR_TOKEN         = '/[0-9A-Za-z_\.@#\/\*]+/';
    private const REGEX_OPERATOR              = '/<<?|\+|\-|\*|\/|%|&&?|\|\|?/';

    /**
     * @return string[]
     */
    public function extractTokens(string $content) : array
    {
        $tokens = new class {
            /** @var string[] $storage */
            private $storage = [];

            public function append(string $token) : void
            {
                $this->storage[] = $token;
            }

            /** @return string[] */
            public function getStorage() : array
            {
                return $this->storage;
            }
        };
        // Shebang
        $content = (string) preg_replace_callback(
            self::REGEX_SHEBANG_WITH_ENV,
            static function (array $matches) use ($tokens) : string {
                /** @var string[] $matches */
                $match = strrchr($matches[0], ' ');
                if ($match === false) {
                    $match = $matches[0];
                } else {
                    $match = substr($match, 1);
                }
                $tokens->append(self::SHEBANG_TOKEN . $match);

                return ' ';
            },
            $content
        );
        $content = (string) preg_replace_callback(
            self::REGEX_SHEBANG,
            static function (array $matches) use ($tokens) : string {
                /** @var string[] $matches */
                $match = strrchr($matches[0], '/');
                if ($match === false) {
                    $match = $matches[0];
                } else {
                    $match = substr($match, 1);
                }
                if ($match !== 'env') {
                    $tokens->append(self::SHEBANG_TOKEN . $match);
                }

                return ' ';
            },
            $content
        );

        // SGML
        $content = (string) preg_replace_callback(
            self::REGEX_SGML,
            static function (array $matches) use ($tokens) : string {
                if (preg_match(self::REGEX_SGML_COMMENT, (string) $matches[0]) === 1) {
                    return ' ';
                }
                $tokens->append((string) $matches[1] . '>');

                // Attributes
                preg_replace_callback(
                    self::REGEX_SGML_ATTRIBUTE,
                    static function (array $matches) use ($tokens) : string {
                        if ($matches[1] !== '') {
                            $tokens->append((string) $matches[1]);
                        }
                        if (isset($matches[2]) &&
                            preg_match(self::REGEX_SGML_LONE_ATTRIBUTE, (string) $matches[2], $lone_attribute) === 1) {
                            $tokens->append($lone_attribute[0]);
                        }

                        return '';
                    },
                    (string) $matches[0]
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
        $match_and_replace_callback = static function (array $matches) use ($tokens) : string {
            $tokens->append((string) $matches[0]);

            return ' ';
        };
        $content                    = (string) preg_replace_callback(
            self::REGEX_PUNCTUATION,
            $match_and_replace_callback,
            $content
        );

        // Skip literal number
        $content = (string) preg_replace_callback(
            self::REGEX_LITERAL_NUMBER,
            static function (array $matches) : string {
                /** @var string[] $matches */
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

        return $tokens->getStorage();
    }
}
