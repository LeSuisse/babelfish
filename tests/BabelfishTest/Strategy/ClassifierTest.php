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

class ClassifierTest extends TestCase
{
    public function testClassification(): void
    {
        $tokenizer = new Tokenizer();
        $db = new TrainableDatabase(
            $tokenizer,
            new TrainSampleFromFile('Ruby', __DIR__ . '/../../../linguist/samples/Ruby/foo.rb'),
            new TrainSampleFromFile('Objective-C', __DIR__ . '/../../../linguist/samples/Objective-C/Foo.h'),
            new TrainSampleFromFile('Objective-C', __DIR__ . '/../../../linguist/samples/Objective-C/Foo.m')
        );

        $classifier = new Classifier($tokenizer, $db);

        $found_languages = $classifier->getLanguages(
            LinguistData::getSampleSourceFile('Objective-C/hello.m'),
            Language::findByAlias('objc')
        );
        $this->assertCount(1, $found_languages);
        $this->assertEquals('Objective-C', $found_languages[0]->getName());

        $found_languages = $classifier->getLanguages(
            LinguistData::getSampleSourceFile('Objective-C/hello.m'),
            Language::findByAlias('ruby')
        );
        $this->assertCount(1, $found_languages);
        $this->assertEquals('Ruby', $found_languages[0]->getName());
    }

    public function testClassifyWithoutCandidates(): void
    {
        $classifier = new Classifier(new Tokenizer, new CachedDatabase);
        $this->assertEmpty($classifier->getLanguages(LinguistData::getSampleSourceFile('Ruby/foo.rb')));
    }

    public function testClassificationAmbiguousLanguages(): void
    {
        // Whitelisting incorrectly classified languages for now
        $failure_whitelist = [
            'dual.sql' => true,
            'drop_stuff.sql' => true,
            'arith.t' => true,
            'arrayt.t' => true,
            'md5.mod' => true,
            'mbcache.mod' => true,
            'fft.cl' => true,
            'sample.cl' => true,
            'list.cl' => true,
            'sounds.properties' => true,
            'libraries.properties' => true,
            'stages-example.pp' => true,
            'expiringhost.pp' => true,
            'hiera_include.pp' => true,
            'unmanaged-notify-puppet25.pp' => true,
            'hello.n' => true,
            'Person.gs' => true,
            'Ronin.gs' => true,
            'log-to-database.lisp' => true,
            'irc.lsp' => true,
            'macros.inc' => true,
            'lib.inc' => true,
            'sample2.f' => true,
            'TUITableView.h' => true,
            'MainMenuViewController.h' => true,
            'SBJsonParser.h' => true,
            'Siesta.h' => true,
            'ASIHTTPRequest.h' => true,
            'JSONKit.h' => true,
            'syscalls.h' => true,
            'asm.h' => true,
            'syscalldefs.h' => true,
            'NWMan.h' => true,
            'hello.h' => true,
            'exception.zep.h' => true,
            'ArrowLeft.h' => true,
            'recurse1.frag' => true,
            'cc-public_domain_mark_white.pm' => true,
            'Types.h' => true,
            'Field.h' => true,
            'complex.pro' => true,
            'distance.m' => true,
            'matlab_class.m' => true,
            'matlab_script2.m' => true,
            'normalize.m' => true,
            'example.m' => true,
            'double_gyre.m' => true,
            'average.m' => true,
            'Example.cp' => true,
            'prefix.fcgi' => true,
            'PSGI.pod' => true,
            'Sample.pod' => true,
            'simul.l' => true,
            'ps2_mouse.v' => true,
            'convolve3x3.rs' => true,
            'counts.d' => true,
            'y_testing.inc' => true,
            'mfile.inc' => true,
            'Check.inc' => true,
            'scilab_test.tst' => true,
            'Init.m' => true,
            'PacletInfo.m' => true,
            'MiscCalculations2.nb' => true,
            'Problem12.m' => true,
            '170-os-daemons.es' => true,
            'single-context.es' => true,
            'zend_ini_scanner.l' => true,
            'Uber.shader' => true,
            'vmops_impl.inc' => true,
            'image_url.inc' => true,
            'libc.inc' => true,
            'HelloWorld.as' => true,
            'FooBar.as' => true,
            'check_reorg.sql' => true,
            'trigger.sql' => true,
            'sleep.sql' => true,
            'runstats.sql' => true,
            'Class.gs' => true,
            'Hello.gs' => true,
            'videodb.ddl' => true,
            'myobject.sql' => true,
            'func.pl' => true,
            'format_spec.pl' => true,
            'admin.pl' => true,
            'Syntax.re' => true,
            'Layout.re' => true,
            'qtbase-native.bb' => true,
            'wsapi.fcgi' => true,
            'pdp10.md' => true,
            'NiAlH_jea.eam.fs' => true,
            'hello.blade.php' => true,
            'tailDel.inc' => true,
            'fixes.inc' => true,
            'hash.t' => true,
            'AppController.j' => true,
            'top.sls' => true,
            'list.asc' => true,
            'php.fcgi' => true,
            'KeyboardMovement_102.asc' => true,
            'characterStepEvent.gml' => true,
            'rmMonAnnCycLLT-help.ncl' => true,
            'min-help.ncl' => true,
            'zonalAve-help.ncl' => true,
            'example.ch' => true,
            'refs.rno' => true,
            'create_view.l' => true,
            'options.m' => true,
            'rot13_ralph.m' => true,
            'switch_detection_bug.m' => true,
            'polymorphism.m' => true,
            'code_info.m' => true,
            'AssemblyInfo.cs' => true,
            'Recipe.hh' => true,
            'MySecureRequest.hh' => true,
            'UserIDRecipe.hh' => true,
            'NonStrictFile.hh' => true,
            'RecipeWithDemo.hh' => true,
            'DBResultRecipe.hh' => true,
            'Controller.hh' => true,
            'Nav.hh' => true,
            'UsingUserID.hh' => true,
            'StrictFile.hh' => true,
            'UnescapedString.hh' => true,
            'UserID.hh' => true,
            'FakeDB.hh' => true,
            'funs.hh' => true,
            'AssertRecipe.hh' => true,
            'index.hh' => true,
            'Assert.hh' => true,
            'error.hh' => true,
            'GetController.hh' => true,
            'startup.hh' => true,
            'colormatrix.fs' => true,
            'fs_kernel.fs' => true,
            'lock.m' => true,
            'inject.x' => true,
            'hello.ls' => true,
            'nimfix.nim.cfg' => true,
        ];

        $classifier = new Classifier(new Tokenizer, new CachedDatabase);

        $directory = new RecursiveDirectoryIterator(__DIR__ . '/../../../linguist/samples/', RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directory);
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
                new ContentFile($filename,
                    file_get_contents($sample_file->getPathname())
                ),
                ...$languages
            );

            $expected_language_name = basename($sample_file->getPath());

            $this->assertEquals($expected_language_name, $classified_languages[0]->getName());
        }
    }
}
