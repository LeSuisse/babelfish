<?php

declare(strict_types=1);

namespace BabelfishTest;

use Babelfish\Babelfish;
use Babelfish\File\ContentFile;
use Babelfish\Language;
use Babelfish\Strategy\Strategy;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function array_key_exists;
use function basename;
use function dirname;
use function file_get_contents;

class BabelfishTest extends TestCase
{
    private const INCORRECT_CLASSIFICATION = [
        'block-sync-counter8.ice' => 'Slice',
        'Git Commit.JSON-tmLanguage' => null,
        'videodb.ddl' => null,
        'drop_stuff.sql' => null,
        'AvailableInSearchSel.prc' => null,
        'hostcache_set_state.inc' => null,
        'benchmark_nbody.t' => null,
        'arith.t' => null,
        'arrayt.t' => null,
        'manos.spec' => null,
        'erlang-erlydtl.spec' => null,
        'apache.spec' => null,
        'ciaaConector.sch' => null,
        'buttons.sch' => null,
        'gedda-junk.sch' => null,
        'Volume.sch' => null,
        'ultimate-temp-controller.sch' => null,
        'buzzer.sch' => null,
        'stages-example.pp' => null,
        'hiera_include.pp' => null,
        'Foo.ML' => 'OCaml',
        'op4.j' => null,
        'op3.j' => null,
        'if4.j' => null,
        'if1.j' => null,
        'if2.j' => null,
        'op1.j' => null,
        'if3.j' => null,
        'op2.j' => null,
        'sboyer.sch' => null,
        'lambdastar.sls' => null,
        'JsNumber.v' => null,
        'Imp.v' => null,
        'Stlc.v' => null,
        'Lists.v' => null,
        'Computation.v' => null,
        'JsCorrectness.v' => null,
        'Rel.v' => null,
        'Poly.v' => null,
        'Spec.v' => null,
        'Smallstep.v' => null,
        'JsPrettyInterm.v' => null,
        'Main.v' => null,
        'JsInterpreterExtraction.v' => null,
        'MolecularClock.bf' => null,
        'CodonModelCompare.bf' => null,
        'profile_test.bf' => null,
        'dNdSDistributionComparison.bf' => null,
        'MFPositiveSelection.bf' => null,
        'MatrixIndexing.bf' => null,
        'AAModelComparison.bf' => null,
        'hyphy_cmds.bf' => null,
        'tc14badge.brd' => null,
        'macros.inc' => null,
        'fp_sqr32_160_comba.inc' => null,
        'lib.inc' => null,
        'cocoa_monitor.m' => null,
        'interface.h' => null,
        'GLKMatrix4.h' => null,
        'bitmap.h' => null,
        'bootstrap.h' => null,
        'elf.h' => null,
        'http_parser.h' => null,
        'syscalls.h' => null,
        'driver.h' => null,
        'asm.h' => null,
        'portio.h' => null,
        'syscalldefs.h' => null,
        'rf_io.h' => null,
        'rfc_string.h' => null,
        'pqiv.h' => null,
        'ip4.h' => null,
        'NWMan.h' => null,
        'cpuid.h' => null,
        'commit.h' => null,
        'ntru_encrypt.h' => null,
        'hello.h' => null,
        'info.h' => null,
        'multiboot.h' => null,
        'scheduler.h' => null,
        'vmem.h' => null,
        'exception.zep.h' => null,
        'color.h' => null,
        'jni_layer.h' => null,
        'blob.h' => null,
        'Nightmare.h' => null,
        '2D.H' => null,
        'wglew.h' => null,
        'filter.h' => null,
        'ArrowLeft.h' => null,
        'vfs.h' => null,
        'array.h' => null,
        'recurse1.frag' => null,
        'SyLens.shader' => null,
        'SimpleLighting.gl2.frag' => null,
        'islandScene.shader' => null,
        'run' => null,
        'js' => null,
        'outro.js.frag' => null,
        'chart_composers.gs' => null,
        'intro.js.frag' => null,
        'itau.gs' => null,
        'js2' => null,
        'cnokw.re' => null,
        'instances.inc' => null,
        'program.cp' => null,
        'ClasspathVMSystemProperties.inc' => null,
        'bug1163046.--skeleton.re' => null,
        'initClasses.inc' => null,
        'rpc.h' => null,
        'cvsignore.re' => null,
        'bar.hh' => null,
        'simple.re' => null,
        'Field.h' => null,
        'PackageInfo.g' => null,
        'matlab_class.m' => null,
        'normalize.m' => null,
        'Check_plot.m' => null,
        'Example.cp' => null,
        'prefix.fcgi' => null,
        'rot13.bf' => null,
        'helloworld.bf' => null,
        'hello.bf' => null,
        'fib100.bf' => null,
        'factor.b' => null,
        'hex_display.v' => null,
        'sha-256-functions.v' => null,
        'sign_extender.v' => null,
        'button_debounce.v' => null,
        'ps2_mouse.v' => null,
        't_button_debounce.v' => null,
        'control.v' => null,
        'mux.v' => null,
        't_div_pipelined.v' => null,
        't_sqrt_pipelined.v' => null,
        'vga.v' => null,
        'pipeline_registers.v' => null,
        'sqrt_pipelined.v' => null,
        'mbittorrent.fx' => null,
        'test.fx' => null,
        'imageserver.fx' => null,
        'gameserver.fx' => null,
        'example.com.vhost' => null,
        'haproxy2.cfg' => null,
        'haproxy4.cfg' => null,
        'haproxy3.cfg' => null,
        'fixed.inc' => null,
        'y_testing.inc' => null,
        'mfile.inc' => null,
        'Check.inc' => null,
        'MiscCalculations2.nb' => null,
        'MiscCalculations.nb' => null,
        'Problem12.m' => null,
        'cApplication.cls' => null,
        'build.cake' => null,
        'EventHandlerMac.mm' => null,
        'objsql.mm' => null,
        'Uber.shader' => null,
        'Fog.shader' => null,
        'DepthOfField.shader' => null,
        'vmops_impl.inc' => null,
        'image_url.inc' => null,
        'libc.inc' => null,
        'Regex.mqh' => null,
        'check_reorg.sql' => null,
        'sleep.sql' => null,
        'runstats.sql' => null,
        'Class.gs' => null,
        'Hello.gs' => null,
        'header-sample.mqh' => null,
        'print_bool.prc' => null,
        'BooleanUtils.cls' => null,
        'ArrayUtils.cls' => null,
        'LanguageUtils.cls' => null,
        'GeoUtils.cls' => null,
        'EmailUtils.cls' => null,
        'TwilioAPI.cls' => null,
        'hello.moo' => null,
        'moocode_toolkit.moo' => null,
        'toy.moo' => null,
        'Machine.re' => null,
        'Syntax.re' => null,
        'SuperMerlin.re' => null,
        'JSX.re' => null,
        'Layout.re' => null,
        'HITSP_C32.sch' => null,
        'oasis-table.sch' => null,
        'XmlIO.pluginspec' => null,
        'Case.workflow' => null,
        'some-ideas.mm' => null,
        'namespace-strict.sch' => null,
        'list.m4' => null,
        'ax_ruby_devel.m4' => null,
        'linguist.srt' => null,
        'long_seq.for' => null,
        'wksst8110.for' => null,
        'Util.cls' => null,
        'Email.cls' => null,
        'SendEmailAlgorithm.cls' => null,
        'tailDel.inc' => null,
        'rpanel.inc' => null,
        'ApiOverviewPage.st' => null,
        'fixes.inc' => null,
        'sigwait.3qt' => 'Roff',
        'sched_yield.2' => 'Roff',
        'wcsftime.3' => 'Roff',
        'wsconsctl.8' => 'Roff',
        'zforce.1x' => 'Roff',
        'zip_file_add.mdoc' => 'Roff',
        'tan.3m' => 'Roff',
        'vwakeup.9' => 'Roff',
        'sched_get_priority_min.3x' => 'Roff',
        'tls_config_ocsp_require_stapling.3in' => 'Roff',
        'uname.1m' => 'Roff',
        'sensor_attach.mdoc' => 'Roff',
        'vio.4' => 'Roff',
        'wsconsctl.conf.5' => 'Roff',
        'spec.linux.spec' => null,
        'main.workflow' => null,
        'Eagle.brd' => null,
        'Eagle.sch' => null,
        'duettest.g' => null,
        'square.g' => null,
        'LightsOff.j' => null,
        'AppController.j' => null,
        'iTunesLayout.j' => null,
        'top.sls' => null,
        'openoffice.sls' => null,
        'gpg4win-light.sls' => null,
        'gimp.sls' => null,
        'eval.sls' => null,
        'truecrypt.sls' => null,
        'jellyfish.fx' => null,
        'corridor.fx' => null,
        'noise.fx' => null,
        'ms.cfg' => null,
        'ifelse.m' => null,
        'forloop.m' => null,
        'arrays.m' => null,
        'fibonacci.m' => null,
        'indirectfunctions.m' => null,
        'mileage.m' => null,
        'helloworld.m' => null,
        'nesting.m' => null,
        'scriptWithPragma.st' => null,
        'testSimpleChainMatches.st' => null,
        'Dinner.st' => null,
        'smallMethod.st' => null,
        'baselineDependency.st' => null,
        'renderSeasideExampleOn..st' => null,
        'TestBasic.st' => null,
        'categories.st' => null,
        'aptitude-defaults.nb' => null,
        'tutor.nb' => null,
        'LIDARLite.ncl' => null,
        'example.ch' => null,
        'Hacl.HKDF.fst' => 'F*',
        'Hacl.Spec.Bignum.Fmul.fst' => 'F*',
        'expr.moo' => null,
        'string1.x' => null,
        'htmlgen.m4' => null,
        'jenkinsci.pluginspec' => null,
        'any.spec' => null,
        'feedgnuplot' => null,
        'perl' => null,
        'test-perl2.pl' => null,
        'exception_handler.pl' => null,
        'Functions.E' => 'Eiffel',
        'minChat.E' => 'Eiffel',
        'Extends.E' => 'Eiffel',
        'Promises.E' => 'Eiffel',
        'IO.E' => 'Eiffel',
        'Guards.E' => 'Eiffel',
        'gsn_csm_xy2_time_series_inputs.ncl' => null,
        'weather_sym_6.ncl' => null,
        'WRF_track_1.ncl' => null,
        'WRF_static_2.ncl' => null,
        'viewport_4.ncl' => null,
        'hdf4sds_7.ncl' => null,
        'PrnOscPat_driver.ncl' => null,
        'traj_3.ncl' => null,
        'unique_9.ncl' => null,
        'primero.ncl' => null,
        'mcsst_1.ncl' => null,
        'cru_8.ncl' => null,
        'topo_9.ncl' => null,
        'mask_12.ncl' => null,
        'xy_29.ncl' => null,
        'tsdiagram_1.ncl' => null,
        'lock.b' => null,
        'cat.b' => null,
        'apache.vhost' => null,
    ];

    public function testGetLanguage(): void
    {
        $babelfish = Babelfish::getWithDefaultStrategies();

        $directory = new RecursiveDirectoryIterator(__DIR__ . '/../../linguist/samples/', RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator  = new RecursiveIteratorIterator($directory);
        // phpcs:ignore SlevomatCodingStandard.PHP.RequireExplicitAssertion.RequiredExplicitAssertion
        /** @var SplFileInfo $sample_file */
        foreach ($iterator as $sample_file) {
            if (array_key_exists($sample_file->getFilename(), self::INCORRECT_CLASSIFICATION)) {
                continue;
            }

            $expected_language_name = basename($sample_file->getPath());
            if ($expected_language_name === 'filenames') {
                $expected_language_name = basename(dirname($sample_file->getPath()));
            }

            $source_file = new ContentFile($sample_file->getFilename(), file_get_contents($sample_file->getPathname()));
            $language    = $babelfish->getLanguage($source_file);

            $this->assertNotNull($language);
            $this->assertEquals($expected_language_name, $language->getName());
        }
    }

    public function testLanguagesCanNotBeFound(): void
    {
        $babelfish = new Babelfish();

        $source_file = new ContentFile('mytestfile.test', 'content');

        $this->assertNull($babelfish->getLanguage($source_file));
    }

    public function testOnlyReturnsTheMostProbableLanguage(): void
    {
        $strategy   = $this->createMock(Strategy::class);
        $language_0 = $this->createMock(Language::class);
        /** @psalm-suppress InternalMethod */
        $strategy->method('getLanguages')->willReturn([$language_0, $this->createMock(Language::class)]);
        $babelfish = new Babelfish($strategy);

        $source_file = new ContentFile('mytestfile.test', 'content');

        $this->assertSame($language_0, $babelfish->getLanguage($source_file));
    }
}
