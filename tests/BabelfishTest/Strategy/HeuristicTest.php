<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Heuristic;
use BabelfishTest\LinguistData;
use PHPUnit\Framework\TestCase;

class HeuristicTest extends TestCase
{
    public function testNoMatch() : void
    {
        $heuristic = new Heuristic();
        $languages = $heuristic->getLanguages(
            LinguistData::getSampleSourceFile('JavaScript/namespace.js'),
            ...[]
        );

        $this->assertEmpty($languages);
    }

    /**
     * @param string[] $language_candidate_names
     *
     * @dataProvider heuristicDataProvider
     */
    public function testHeuristic(SourceFile $file, array $language_candidate_names) : void
    {
        $language_candidates = [];
        foreach ($language_candidate_names as $language_candidate_name) {
            $language_candidates[] = Language::findByAlias($language_candidate_name);
        }
        $heuristic = new Heuristic();
        $this->assertSame($language_candidates, $heuristic->getLanguages($file, ...$language_candidates));
    }

    /**
     * @return <SourceFile|string[]>[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableReturnTypeHintSpecification
     */
    public function heuristicDataProvider() : array
    {
        $raw_data = [
            'as' => [
                'ActionScript' => ['ActionScript'],
                'AngelScript' => ['AngelScript'],
            ],
            'asc' => [
                'AsciiDoc' => ['AsciiDoc'],
                'AGS Script' => ['AGS Script'],
                'Public Key' => ['Public Key'],
            ],
            'bb' => [
                'BitBake' => ['BitBake'],
                'BlitzBasic' => ['BlitzBasic'],
            ],
            'builds' => [
                'Text' => ['Text'],
                'XML' => ['XML'],
            ],
            'ch' => [
                'xBase' => ['xBase'],
            ],
            'cl' => [
                'Common Lisp' => ['Common Lisp'],
                'OpenCL' => ['OpenCL'],
            ],
            'cls' => [
                'TeX' => ['TeX'],
                'Apex' => [],
                'OpenEdge ABL' => [],
                'Visual Basic' => [],
            ],
            'cs' => [
                'C#' => ['C#'],
                'Smalltalk' => ['Smalltalk'],
            ],
            'd' => [
                'D' => ['D'],
                'DTrace' => ['DTrace'],
                'Makefile' => ['Makefile'],
            ],
            'ecl' => [
                'ECL' => ['ECL'],
                'Eclipse' => ['Eclipse'],
            ],
            'es' => [
                'Erlang' => ['Erlang'],
                'JavaScript' => ['JavaScript'],
            ],
            'f' => [
                'Fortran' => ['Fortran'],
                'Forth' => ['Forth'],
            ],
            'for' => [
                'Fortran' => ['Fortran'],
                'Forth' => ['Forth'],
            ],
            'fr' => [
                'Frege' => ['Frege'],
                'Forth' => ['Forth'],
                'Text' => ['Text'],
            ],
            'fs' => [
                'F#' => ['F#'],
                'Forth' => ['Forth'],
                'GLSL' => ['GLSL'],
            ],
            'gml' => [
                'Game Maker Language' => ['Game Maker Language'],
                'Graph Modeling Language' => ['Graph Modeling Language'],
                'XML' => ['XML'],
            ],
            'gs' => [
                'Gosu' => ['Gosu'],
            ],
            'h' => [
                'Objective-C' => ['Objective-C'],
                'C' => [],
            ],
            'hh' => [
                'Hack' => ['Hack'],
            ],
            'ice' => [
                'Slice' => ['Slice'],
                'JSON' => ['JSON'],
            ],
            'inc' => [
                'PHP' => ['PHP'],
                'POV-Ray SDL' => ['POV-Ray SDL'],
            ],
            'l' => [
                'Common Lisp' => ['Common Lisp'],
                'Lex' => ['Lex'],
                'Roff' => ['Roff'],
                'PicoLisp' => ['PicoLisp'],
            ],
            'ls' => [
                'LiveScript' => ['LiveScript'],
                'LoomScript' => ['LoomScript'],
            ],
            'lsp' => [
                'Common Lisp' => ['Common Lisp'],
                'NewLisp' => ['NewLisp'],
            ],
            'lisp' => [
                'Common Lisp' => ['Common Lisp'],
                'NewLisp' => ['NewLisp'],
            ],
            'm' => [
                'Objective-C' => ['Objective-C'],
                'Mercury' => ['Mercury'],
                'MUF' => ['MUF'],
                'Mathematica' => ['Mathematica'],
                'Limbo' => ['Limbo'],
            ],
            'md' => [
                'Markdown' => ['Markdown'],
                'GCC Machine Description' => ['GCC Machine Description'],
            ],
            'ml' => [
                'OCaml' => ['OCaml'],
                'Standard ML' => ['Standard ML'],
            ],
            'mod' => [
                'Modula-2' => ['Modula-2'],
                'XML' => ['XML'],
                'Linux Kernel Module' => ['Linux Kernel Module', 'AMPL'],
                'AMPL' => ['Linux Kernel Module', 'AMPL'],
            ],
            'ms' => [
                'Roff' => ['Roff'],
                'Unix Assembly' => ['Unix Assembly'],
                'MAXScript' => ['MAXScript'],
            ],
            'n' => [
                'Roff' => ['Roff'],
                'Nemerle' => ['Nemerle'],
            ],
            'ncl' => [
                'Roff' => ['NCL'],
                'XML' => ['XML'],
                'Text' => ['Text'],
            ],
            'nl' => [
                'NewLisp' => ['NewLisp'],
                'NL' => ['NL'],
            ],
            'php' => [
                'Hack' => ['Hack'],
                'PHP' => ['PHP'],
            ],
            'pl' => [
                'Prolog' => ['Prolog'],
                'Perl 6' => ['Perl 6'],
            ],
            'pm' => [
                'Perl' => ['Perl'],
                'Perl 6' => ['Perl 6'],
                'XPM' => ['XPM'],
            ],
            'pp' => [
                'Pascal' => ['Pascal'],
                'Puppet' => ['Puppet'],
            ],
            'pro' => [
                'Prolog' => ['Prolog'],
                'IDL' => ['IDL'],
                'INI' => ['INI'],
                'QMake' => ['QMake'],
            ],
            'properties' => [
                'INI' => ['INI'],
                'Java Properties' => ['Java Properties'],
            ],
            'props' => [
                'INI' => ['INI'],
                'XML' => ['XML'],
            ],
            'q' => [
                'q' => ['q'],
                'HiveQL' => ['HiveQL'],
            ],
            'r' => [
                'R' => ['R'],
                'Rebol' => ['Rebol'],
            ],
            'R' => [
                'R' => ['R'],
            ],
            'rno' => [
                'RUNOFF' => ['RUNOFF'],
                'Roff' => ['Roff'],
            ],
            'rpy' => [
                'Python' => ['Python'],
                'Ren\'Py' => ['Ren\'Py'],
            ],
            'rs' => [
                'Rust' => ['Rust'],
                'RenderScript' => ['RenderScript'],
            ],
            'sc' => [
                'SuperCollider' => ['SuperCollider'],
                'Scala' => ['Scala'],
            ],
            'sql' => [
                'PLpgSQL' => ['PLpgSQL'],
                'PLSQL' => ['PLSQL'],
            ],
            'srt' => [
                'SubRip Text' => ['SubRip Text'],
            ],
            't' => [
                'Turing' => ['Turing'],
                'Perl' => ['Perl'],
            ],
            'toc' => [
                'TeX' => ['TeX'],
                'World of Warcraft Addon Data' => ['World of Warcraft Addon Data'],
            ],
            'ts' => [
                'TypeScript' => ['TypeScript'],
                'XML' => ['XML'],
            ],
            'tst' => [
                'GAP' => ['GAP'],
                'Scilab' => ['Scilab'],
            ],
            'tsx' => [
                'TypeScript' => ['TypeScript'],
                'XML' => ['XML'],
            ],
            'w' => [
                'CWeb' => ['CWeb'],
                'OpenEdge ABL' => ['OpenEdge ABL'],
            ],
            'x' => [
                'Linked Script' => ['Linked Script'],
                'RPC' => ['RPC'],
            ],
            'yy' => [
                'JSON' => ['JSON'],
                'Yacc' => ['Yacc'],
            ],
        ];

        $provided_data = [];
        foreach ($raw_data as $extension => $languages) {
            foreach ($languages as $language_used_for_sample => $language_candidates) {
                $samples = LinguistData::getLanguageSampleSourceFiles($language_used_for_sample, '*.' . $extension);
                foreach ($samples as $sample) {
                    if (isset($this->getAmbiguousFile()[$extension][$language_used_for_sample][$sample->getName()])) {
                        continue;
                    }
                    $provided_data[] = [$sample, $language_candidates];
                }
            }
        }

        $provided_data[] = [LinguistData::getSampleSourceFile('C++/scanner.h'), ['C++']];
        $provided_data[] = [LinguistData::getSampleSourceFile('C++/protocol-buffer.pb.h'), ['C++']];
        $provided_data[] = [LinguistData::getSampleSourceFile('C++/v8.h'), ['C++']];
        $provided_data[] = [LinguistData::getSampleSourceFile('C++/gdsdbreader.h'), ['C++']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl/oo1.pl'), ['Perl']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl/oo2.pl'), ['Perl']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl/oo3.pl'), ['Perl']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl/fib.pl'), ['Perl']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl/use5.pl'), ['Perl']];
        $provided_data[] = [LinguistData::getSampleSourceFile('M/MDB.m'), ['M']];
        $provided_data[] = [LinguistData::getSampleSourceFile('MATLAB/create_ieee_paper_plots.m'), ['MATLAB']];
        $provided_data[] = [LinguistData::getSampleSourceFile('SQL/create_stuff.sql'), ['SQL']];
        $provided_data[] = [LinguistData::getSampleSourceFile('SQL/db.sql'), ['SQL']];
        $provided_data[] = [LinguistData::getSampleSourceFile('SQL/dual.sql'), ['SQL']];
        $provided_data[] = [LinguistData::getSampleSourceFile('SQLPL/trigger.sql'), ['SQLPL']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/01-dash-uppercase-i.t'), ['Perl 6']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/01-parse.t'), ['Perl 6']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/advent2009-day16.t'), ['Perl 6']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/basic-open.t'), ['Perl 6']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/calendar.t'), ['Perl 6']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/for.t'), ['Perl 6']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/hash.t'), ['Perl 6']];
        $provided_data[] = [LinguistData::getSampleSourceFile('Perl 6/listquote-whitespace.t'), ['Perl 6']];

        return $provided_data;
    }

    /**
     * @dataProvider ambiguousFileDataProvider
     */
    public function testAmbiguousFileAreNotWronglyDetected(SourceFile $file, string $language_candidate_name) : void
    {
        $heuristic = new Heuristic();
        $this->assertEmpty($heuristic->getLanguages($file, Language::findByAlias($language_candidate_name)));
    }

    /**
     * @return <SourceFile|string>[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableReturnTypeHintSpecification
     */
    public function ambiguousFileDataProvider() : array
    {
        $files_to_provide = [];
        foreach ($this->getAmbiguousFile() as $extension => $languages) {
            foreach ($languages as $language => $files) {
                foreach ($files as $file => $v) {
                    $files_to_provide[] = [LinguistData::getSampleSourceFile($language . '/' . $file), $language];
                }
            }
        }

        return $files_to_provide;
    }

    /**
     * @return <string|string<string|bool>[]>[]
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingTraversableReturnTypeHintSpecification
     */
    private function getAmbiguousFile() : array
    {
        return [
            'm' => ['Mathematica' => ['Problem12.m' => true]],
            'ml' => ['OCaml' => ['date.ml' => true, 'common.ml' => true, 'sigset.ml' => true]],
            'ncl' => ['Text' => ['LIDARLite.ncl' => true]],
            'pp' => ['Puppet' => ['stages-example.pp' => true, 'hiera_include.pp' => true]],
        ];
    }
}
