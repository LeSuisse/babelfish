<?php

declare(strict_types=1);

namespace BabelfishTest;

use Babelfish\File\ContentFile;
use Babelfish\File\SourceFile;

final class LinguistData
{
    public static function getFixtureSourceFile(string $fixture_path): SourceFile
    {
        return new ContentFile(
            basename($fixture_path),
            file_get_contents(__DIR__ . "/../../linguist/test/fixtures/$fixture_path")
        );
    }

    public static function getSampleSourceFile(string $sample_path): SourceFile
    {
        return new ContentFile(
            basename($sample_path),
            file_get_contents(__DIR__ . "/../../linguist/samples/$sample_path")
        );
    }

    /**
     * @return SourceFile[]
     */
    public static function getLanguageSampleSourceFiles(string $language_name, string $glob_pattern_file): array
    {
        $samples = [];

        foreach (glob(__DIR__ . "/../../linguist/samples/$language_name/$glob_pattern_file") as $sample_path) {
            if (! is_link($sample_path)) {
                $samples[] = new ContentFile(basename($sample_path), file_get_contents($sample_path));
            }
        }

        return $samples;
    }
}
