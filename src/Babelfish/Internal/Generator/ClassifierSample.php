<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Babelfish\Strategy\Classification\TrainableDatabase;
use Babelfish\Strategy\Classification\TrainSampleFromFile;
use Babelfish\Strategy\Tokenizer\Tokenizer;

final class ClassifierSample implements Generator
{
    use GetContentFromLinguistFileTrait;

    public function generate(string $linguist_repository_path): array
    {
        $samples = [];

        $directory = new \RecursiveDirectoryIterator(
            __DIR__ . '/../../../../linguist/samples/',
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $iterator = new \RecursiveIteratorIterator($directory);
        foreach ($iterator as $sample_file) {
            $language_name = basename($sample_file->getPath());
            $samples[] = new TrainSampleFromFile($language_name, $sample_file->getPathname());
        }

        $db = new TrainableDatabase(new Tokenizer, ...$samples);
        return $db->getRawDatabase();
    }
}
