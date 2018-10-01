<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;

class Shebang implements Strategy
{
    private const SEARCH_SCOPE_FOR_MULTILINE_EXEC = 5;

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file): array
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

        $script = \array_values(\array_slice(explode('/', $shebang_matches[1]), -1))[0];
        if ($script === 'env') {
            $env_options = implode(' ', \array_slice($shebang_matches, 2));
            preg_match('/(\s+.*=[^\s]+)*\s+(\S+)/', $env_options, $env_options_matches);
            $script = $env_options_matches[2] ?? '';
        }

        if ($script === '') {
            return [];
        }

        $script = \array_values(\array_slice(explode('/', $script), -1))[0];

        $script = preg_replace('/(\.\d+)$/', '', $script);
        $script = preg_replace('/^#!\s*/', '', $script);

        if ($script === 'sh' &&
            preg_match(
                '/exec (\w+).+\$0.+\$@/',
                $this->getHeaderForMultilineExec($file),
                $exec_matches) === 1
        ) {
            $script = $exec_matches[1];
        }

        $language = Language::findByInterpreter($script);

        if ($language === null) {
            return [];
        }

        return [$language];
    }

    private function getHeaderForMultilineExec(SourceFile $file): string
    {
        return \implode("\n", \array_slice($file->getLines(), 0, self::SEARCH_SCOPE_FOR_MULTILINE_EXEC));
    }
}