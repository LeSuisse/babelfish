<?php

declare(strict_types=1);

namespace BabelfishTest;

use Babelfish\Language;
use PHPUnit\Framework\TestCase;

class LanguageTest extends TestCase
{
    /**
     * @dataProvider aliasesProvider
     */
    public function testFindByAlias(?string $expected_language_name, string $alias): void
    {
        $language = Language::findByAlias($alias);
        $this->assertSame($expected_language_name, $language !== null ? $language->getName() : null);
    }

    /**
     * @dataProvider filenamesProvider
     */
    public function testFindByFilename(?string $expected_language_name, string $filename): void
    {
        $language = Language::findByFilename($filename);
        $this->assertSame($expected_language_name, $language !== null ? $language->getName() : null);
    }

    /**
     * @dataProvider interpretersProvider
     */
    public function testFindByInterpreter(?string $expected_language_name, string $interpreter): void
    {
        $language = Language::findByInterpreter($interpreter);
        $this->assertSame($expected_language_name, $language !== null ? $language->getName() : null);
    }

    public function aliasesProvider(): array
    {
        return [
            ['ASP', 'asp'],
            ['ASP', 'aspx'],
            ['ASP', 'aspx-vb'],
            ['ActionScript', 'as3'],
            ['ApacheConf', 'apache'],
            ['Assembly', 'nasm'],
            ['Batchfile', 'bat'],
            ['C#', 'c#'],
            ['C#', 'csharp'],
            ['C', 'c'],
            ['C++', 'c++'],
            ['C++', 'cpp'],
            ['Chapel', 'chpl'],
            ['CoffeeScript', 'coffee'],
            ['CoffeeScript', 'coffee-script'],
            ['ColdFusion', 'cfm'],
            ['Common Lisp', 'common-lisp'],
            ['Common Lisp', 'lisp'],
            ['Darcs Patch', 'dpatch'],
            ['Dart', 'dart'],
            ['Emacs Lisp', 'elisp'],
            ['Emacs Lisp', 'emacs'],
            ['Emacs Lisp', 'emacs-lisp'],
            ['Gettext Catalog', 'pot'],
            ['HTML', 'html'],
            ['HTML', 'xhtml'],
            ['HTML+ERB', 'html+erb'],
            ['HTML+ERB', 'erb'],
            ['IRC log', 'irc'],
            ['JSON', 'json'],
            ['Java Server Pages', 'jsp'],
            ['Java', 'java'],
            ['JavaScript', 'javascript'],
            ['JavaScript', 'js'],
            ['Literate Haskell', 'lhs'],
            ['Literate Haskell', 'literate-haskell'],
            ['Objective-C', 'objc'],
            ['OpenEdge ABL', 'openedge'],
            ['OpenEdge ABL', 'progress'],
            ['OpenEdge ABL', 'abl'],
            ['Parrot Internal Representation', 'pir'],
            ['PowerShell', 'posh'],
            ['Puppet', 'puppet'],
            ['Pure Data', 'pure-data'],
            ['Raw token data', 'raw'],
            ['Ruby', 'rb'],
            ['Ruby', 'ruby'],
            ['R', 'r'],
            ['Scheme', 'scheme'],
            ['Shell', 'bash'],
            ['Shell', 'sh'],
            ['Shell', 'shell'],
            ['Shell', 'zsh'],
            ['SuperCollider', 'supercollider'],
            ['TeX', 'tex'],
            ['TypeScript', 'ts'],
            ['Vim script', 'vim'],
            ['Vim script', 'viml'],
            ['reStructuredText', 'rst'],
            ['X BitMap', 'xbm'],
            ['X PixMap', 'xpm'],
            ['YAML', 'yml'],
            [null, 'do_not_exist']
        ];
    }

    public function filenamesProvider(): array
    {
        return [
            ['Shell', 'PKGBUILD'],
            ['Ruby', 'Rakefile'],
            ['ApacheConf', 'httpd.conf'],
            ['ApacheConf', '.htaccess'],
            ['Nginx', 'nginx.conf'],
            [null, 'foo.rb'],
            [null, 'rb'],
            [null, '.null'],
            ['Shell', '.bashrc'],
            ['Shell', 'bash_profile'],
            ['Shell', '.zshrc'],
            ['Clojure', 'riemann.config'],
        ];
    }

    public function interpretersProvider(): array
    {
        return [
            ['Ruby', 'ruby'],
            ['R', 'Rscript'],
            ['Shell', 'sh'],
            ['Shell', 'bash'],
            ['Python', 'python'],
            ['Python', 'python2'],
            ['Python', 'python3'],
            ['Common Lisp', 'sbcl'],
            ['SuperCollider', 'sclang'],
            [null, 'unknown_interpreter'],
        ];
    }
}
