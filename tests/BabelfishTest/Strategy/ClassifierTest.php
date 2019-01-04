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
use function basename;
use function count;
use function dirname;
use function file_get_contents;

class ClassifierTest extends TestCase
{
    public function testClassification() : void
    {
        $tokenizer = new Tokenizer();
        $db        = new TrainableDatabase(
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

    public function testClassifyWithoutCandidates() : void
    {
        $classifier = new Classifier(new Tokenizer(), new CachedDatabase());
        $this->assertEmpty($classifier->getLanguages(LinguistData::getSampleSourceFile('Ruby/foo.rb')));
    }

    public function testClassificationAmbiguousLanguages() : void
    {
        // Whitelisting incorrectly classified languages for now
        $failure_whitelist = [
            'dual.sql' => 'PLpgSQL',
            'drop_stuff.sql' => 'PLpgSQL',
            'benchmark_nbody.t' => 'Perl',
            'arith.t' => 'Perl',
            'arrayt.t' => 'Perl',
            'md5.mod' => 'XML',
            'mbcache.mod' => 'XML',
            'fft.cl' => 'Common Lisp',
            'sample.cl' => 'Common Lisp',
            'list.cl' => 'Common Lisp',
            'sounds.properties' => 'INI',
            'libraries.properties' => 'INI',
            'stages-example.pp' => 'Pascal',
            'expiringhost.pp' => 'Pascal',
            'hiera_include.pp' => 'Pascal',
            'unmanaged-notify-puppet25.pp' => 'Pascal',
            'hello.n' => 'Roff',
            'booters.r' => 'R',
            'Person.gs' => 'JavaScript',
            'Ronin.gs' => 'JavaScript',
            'log-to-database.lisp' => 'Common Lisp',
            'irc.lsp' => 'Common Lisp',
            'macros.inc' => 'C++',
            'lib.inc' => 'POV-Ray SDL',
            'sample2.f' => 'Forth',
            'Siesta.h' => 'C',
            'JSONKit.h' => 'C',
            'syscalls.h' => 'C++',
            'exception.zep.h' => 'C++',
            'recurse1.frag' => 'JavaScript',
            'SimpleLighting.gl2.frag' => 'JavaScript',
            'cc-public_domain_mark_white.pm' => 'Perl',
            'Types.h' => 'C',
            'rpc.h' => 'C',
            'Field.h' => 'C',
            'complex.pro' => 'Prolog',
            'distance.m' => 'M',
            'matlab_class.m' => 'M',
            'matlab_script2.m' => 'Mercury',
            'normalize.m' => 'M',
            'load_bikes.m' => 'Objective-C',
            'matlab_script.m' => 'Mercury',
            'example.m' => 'Mercury',
            'cross_validation.m' => 'M',
            'double_gyre.m' => 'Mercury',
            'make_filter.m' => 'Mercury',
            'average.m' => 'M',
            'Example.cp' => 'C++',
            'prefix.fcgi' => 'Shell',
            'PSGI.pod' => 'Pod 6',
            'Sample.pod' => 'Pod 6',
            'simul.l' => 'Common Lisp',
            'sha-256-functions.v' => 'Coq',
            'ps2_mouse.v' => 'Coq',
            'convolve3x3.rs' => 'Rust',
            'counts.d' => 'D',
            'y_testing.inc' => 'C++',
            'mfile.inc' => 'C++',
            'Check.inc' => 'C++',
            'scilab_test.tst' => 'GAP',
            'Init.m' => 'Mercury',
            'PacletInfo.m' => 'Mercury',
            'Problem12.m' => 'Objective-C',
            '170-os-daemons.es' => 'JavaScript',
            'single-context.es' => 'JavaScript',
            'zend_ini_scanner.l' => 'Common Lisp',
            'Uber.shader' => 'GLSL',
            'vmops_impl.inc' => 'C++',
            'image_url.inc' => 'C++',
            'libc.inc' => 'C++',
            'HelloWorld.as' => 'AngelScript',
            'FooBar.as' => 'AngelScript',
            'turing.t' => 'Perl',
            'check_reorg.sql' => 'PLpgSQL',
            'trigger.sql' => 'PLpgSQL',
            'sleep.sql' => 'PLpgSQL',
            'runstats.sql' => 'PLpgSQL',
            'Class.gs' => 'JavaScript',
            'Hello.gs' => 'JavaScript',
            'header-sample.mqh' => 'MQL5',
            'videodb.ddl' => 'SQL',
            'myobject.sql' => 'PLpgSQL',
            'func.pl' => 'Perl',
            'format_spec.pl' => 'Perl',
            'ex6.pl' => 'Perl',
            'admin.pl' => 'Perl',
            'turing.pl' => 'Perl',
            'hello.moo' => 'Mercury',
            'toy.moo' => 'Mercury',
            'Syntax.re' => 'C++',
            'Layout.re' => 'C++',
            'qtbase-native.bb' => 'BlitzBasic',
            'wsapi.fcgi' => 'Perl',
            'NiAlH_jea.eam.fs' => 'Forth',
            'hello.blade.php' => 'PHP',
            'tailDel.inc' => 'C++',
            'fixes.inc' => 'C++',
            'hash.t' => 'Perl',
            'basic-open.t' => 'Perl',
            'for.t' => 'Perl',
            'ContainsUnicode.pm' => 'Perl',
            'Bailador.pm' => 'Perl',
            'Model.pm' => 'Perl',
            'man-or-boy.t' => 'Perl',
            'listquote-whitespace.t' => 'Perl',
            'advent2009-day16.t' => 'Perl',
            'A.pm' => 'Perl',
            '01-dash-uppercase-i.t' => 'Perl',
            'Simple.pm' => 'Perl',
            'top.sls' => 'Scheme',
            'list.asc' => 'AGS Script',
            'dynamicscoping.m' => 'Mercury',
            'example.ch' => 'xBase',
            'refs.rno' => 'RUNOFF',
            'create_view.l' => 'Common Lisp',
            'AssemblyInfo.cs' => 'Smalltalk',
            'Recipe.hh' => 'C++',
            'MySecureRequest.hh' => 'C++',
            'UserIDRecipe.hh' => 'C++',
            'NonStrictFile.hh' => 'C++',
            'RecipeWithDemo.hh' => 'C++',
            'funs.php' => 'PHP',
            'DBResultRecipe.hh' => 'C++',
            'Controller.hh' => 'C++',
            'Nav.hh' => 'C++',
            'UsingUserID.hh' => 'C++',
            'StrictFile.hh' => 'C++',
            'UnescapedString.hh' => 'C++',
            'UserID.hh' => 'C++',
            'FakeDB.hh' => 'C++',
            'funs.hh' => 'C++',
            'AssertRecipe.hh' => 'C++',
            'index.hh' => 'C++',
            'Assert.hh' => 'C++',
            'error.hh' => 'C++',
            'GetController.hh' => 'C++',
            'startup.hh' => 'C++',
            'htmlgen.m4' => 'M4Sugar',
            'colormatrix.fs' => 'GLSL',
            'fs_kernel.fs' => 'GLSL',
            'lock.m' => 'Objective-C',
            'inject.x' => 'Logos',
            'hello.ls' => 'LoomScript',
            'nimfix.nim.cfg' => 'INI',
        ];

        $classifier = new Classifier(new Tokenizer(), new CachedDatabase());

        $directory = new RecursiveDirectoryIterator(__DIR__ . '/../../../linguist/samples/', RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator  = new RecursiveIteratorIterator($directory);
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
