<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\ContentFile;
use Babelfish\Language;
use Babelfish\Strategy\Classification\CachedDatabase;
use Babelfish\Strategy\Classification\TrainableDatabase;
use Babelfish\Strategy\Classification\TrainSampleFromFile;
use Babelfish\Strategy\Classifier;
use Babelfish\Strategy\Tokenizer\Tokenizer;
use BabelfishTest\LinguistData;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function basename;
use function count;
use function dirname;
use function file_get_contents;

class ClassifierTest extends TestCase
{
    public function testClassification(): void
    {
        $tokenizer = new Tokenizer();
        $db        = new TrainableDatabase(
            $tokenizer,
            new TrainSampleFromFile('Ruby', __DIR__ . '/../../../linguist/samples/Ruby/foo.rb'),
            new TrainSampleFromFile('Objective-C', __DIR__ . '/../../../linguist/samples/Objective-C/Foo.h'),
            new TrainSampleFromFile('Objective-C', __DIR__ . '/../../../linguist/samples/Objective-C/Foo.m')
        );

        $classifier = new Classifier($tokenizer, $db);

        $objective_c_language = Language::findByAlias('objc');
        $this->assertNotNull($objective_c_language);
        $found_languages = $classifier->getLanguages(
            LinguistData::getSampleSourceFile('Objective-C/hello.m'),
            $objective_c_language
        );
        $this->assertCount(1, $found_languages);
        $this->assertEquals('Objective-C', $found_languages[0]->getName());

        $ruby_language = Language::findByAlias('ruby');
        $this->assertNotNull($ruby_language);
        $found_languages = $classifier->getLanguages(
            LinguistData::getSampleSourceFile('Objective-C/hello.m'),
            $ruby_language
        );
        $this->assertCount(1, $found_languages);
        $this->assertEquals('Ruby', $found_languages[0]->getName());
    }

    public function testClassifyWithoutCandidates(): void
    {
        $classifier = new Classifier(new Tokenizer(), new CachedDatabase());
        $this->assertEmpty($classifier->getLanguages(LinguistData::getSampleSourceFile('Ruby/foo.rb')));
    }

    public function testClassificationAmbiguousLanguages(): void
    {
        // Whitelisting incorrectly classified languages for now
        $failure_whitelist = [
            'block-sync-counter8.ice' => 'JSON',
            'unmanaged-notify-puppet25.pp' => 'Puppet',
            'NiAlH_jea.eam.fs' => 'Formatted',
            'hello.blade.php' => 'Blade',
            'fixes.inc' => 'Pawn',
            'nimfix.nim.cfg' => 'Nim',
            'tan.3m' => 'Roff Manpage',
        ];

        $classifier = new Classifier(new Tokenizer(), new CachedDatabase());

        $directory = new RecursiveDirectoryIterator(__DIR__ . '/../../../linguist/samples/', RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator  = new RecursiveIteratorIterator($directory);
        // phpcs:ignore SlevomatCodingStandard.PHP.RequireExplicitAssertion.RequiredExplicitAssertion
        /** @var SplFileInfo $sample_file */
        foreach ($iterator as $sample_file) {
            $filename = $sample_file->getFilename();

            if (isset($failure_whitelist[$filename])) {
                continue;
            }

            $language = Language::findByFilename($filename);
            if ($language !== null) {
                continue;
            }

            $languages = Language::findLanguagesByExtension('.' . $sample_file->getExtension());
            if (count($languages) <= 1) {
                continue;
            }

            $classified_languages = $classifier->getLanguages(
                new ContentFile(
                    $filename,
                    file_get_contents($sample_file->getPathname())
                ),
                ...$languages
            );

            $expected_language_name = basename($sample_file->getPath());
            if ($expected_language_name === 'filename') {
                $expected_language_name = basename(dirname($sample_file->getPath()));
            }

            $this->assertEquals($expected_language_name, $classified_languages[0]->getName());
        }
    }
}
