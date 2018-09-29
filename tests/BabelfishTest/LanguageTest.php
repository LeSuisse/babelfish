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
    public function testFindByAliases(string $expected_language_name, string $alias): void
    {
        $language = Language::findByAlias($alias);
        $this->assertSame($expected_language_name, $language->getName());
    }

    public function aliasesProvider()
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
        ];
    }

    public function testNotExistingAliasDoesNotFindALanguage(): void
    {
        $this->assertNull(Language::findByAlias('do_not_exist'));
    }
}
