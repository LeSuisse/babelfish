<?php

declare(strict_types=1);

namespace Babelfish;

use Babelfish\File\SourceFile;
use Babelfish\Strategy\Classification\CachedDatabase;
use Babelfish\Strategy\Classifier;
use Babelfish\Strategy\Extension;
use Babelfish\Strategy\Filename;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;
use Babelfish\Strategy\Heuristic;
use Babelfish\Strategy\Modeline;
use Babelfish\Strategy\Shebang;
use Babelfish\Strategy\Strategy;
use Babelfish\Strategy\Tokenizer\Tokenizer;
use Babelfish\Strategy\XML;
use function count;

class Babelfish
{
    /** @var Strategy[] */
    private $strategies;

    public function __construct(Strategy ...$strategies)
    {
        $this->strategies = $strategies;
    }

    public static function getWithDefaultStrategies() : self
    {
        $only_keep_language_already_candidate_filter = new OnlyKeepLanguageAlreadyCandidatesFilter();
        return new self(
            new Modeline($only_keep_language_already_candidate_filter),
            new Filename($only_keep_language_already_candidate_filter),
            new Shebang($only_keep_language_already_candidate_filter),
            new Extension($only_keep_language_already_candidate_filter),
            new XML(),
            new Heuristic(),
            new Classifier(new Tokenizer(), new CachedDatabase())
        );
    }

    public function getLanguage(SourceFile $file) : ?Language
    {
        $candidates = [];
        foreach ($this->strategies as $strategy) {
            $candidates = $strategy->getLanguages($file, ...$candidates);
            if (count($candidates) === 1) {
                return $candidates[0];
            }
        }

        if (empty($candidates)) {
            return null;
        }

        return $candidates[0];
    }
}
