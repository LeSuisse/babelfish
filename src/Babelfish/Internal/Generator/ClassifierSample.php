<?php

declare(strict_types=1);

namespace Babelfish\Internal\Generator;

use Babelfish\Strategy\Classification\TrainableDatabase;
use Babelfish\Strategy\Classification\TrainSampleFromFile;
use Babelfish\Strategy\Tokenizer\Tokenizer;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use function basename;
use function dirname;

final class ClassifierSample implements Generator
{
    use GetContentFromLinguistFile;

    /**
     * @return mixed[]
     */
    public function generate(string $linguist_repository_path) : array
    {
        $samples = [];

        $directory = new RecursiveDirectoryIterator(
            __DIR__ . '/../../../../linguist/samples/',
            RecursiveDirectoryIterator::SKIP_DOTS
        );
        $iterator  = new RecursiveIteratorIterator($directory);
        /** @var SplFileInfo $sample_file */
        foreach ($iterator as $sample_file) {
            $language_name = basename($sample_file->getPath());
            if ($language_name === 'filenames') {
                $language_name = basename(dirname($sample_file->getPath()));
            }
            $samples[] = new TrainSampleFromFile($language_name, $sample_file->getPathname());
        }

        $db = new TrainableDatabase(new Tokenizer(), ...$samples);

        return $db->getRawDatabase();
    }
}
