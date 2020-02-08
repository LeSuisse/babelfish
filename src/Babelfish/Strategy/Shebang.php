<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;
use function array_slice;
use function array_values;
use function explode;
use function implode;
use function preg_match;
use function preg_replace;
use function strpos;

final class Shebang implements Strategy
{
    private const SEARCH_SCOPE_FOR_MULTILINE_EXEC = 5;
    /** @var OnlyKeepLanguageAlreadyCandidatesFilter */
    private $filter;

    public function __construct(OnlyKeepLanguageAlreadyCandidatesFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file, Language ...$language_candidates) : array
    {
        $lines = $file->getLines();
        if (empty($lines)) {
            return [];
        }

        $first_line = $lines[0];
        if (strpos($first_line, '#!') !== 0) {
            return [];
        }

        if (preg_match('/^#!\s*(\S+)(.*)/', $first_line, $shebang_matches) !== 1) {
            return [];
        }

        $script = array_values(array_slice(explode('/', $shebang_matches[1]), -1))[0];
        if ($script === 'env') {
            $env_options = implode(' ', array_slice($shebang_matches, 2));
            preg_match('/(\s+.*=[^\s]+)*\s+(\S+)/', $env_options, $env_options_matches);
            $script = $env_options_matches[2] ?? '';
        }

        if ($script === '') {
            return [];
        }

        $script = array_values(array_slice(explode('/', $script), -1))[0];

        $script = preg_replace('/(\.\d+)$/', '', $script);
        $script = preg_replace('/^#!\s*/', '', $script);

        if ($script === 'sh' &&
            preg_match(
                '/exec (\w+).+\$0.+\$@/',
                $this->getHeaderForMultilineExec($file),
                $exec_matches
            ) === 1
        ) {
            $script = $exec_matches[1];
        }

        if ($script === 'osascript' && preg_match('/osascript\s+-l/', $first_line) === 1) {
            return [];
        }

        return $this->filter->filter($language_candidates, ...Language::findLanguagesByInterpreter($script));
    }

    private function getHeaderForMultilineExec(SourceFile $file) : string
    {
        return implode("\n", array_slice($file->getLines(), 0, self::SEARCH_SCOPE_FOR_MULTILINE_EXEC));
    }
}
