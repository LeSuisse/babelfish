<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy\Tokenizer;

use Babelfish\Strategy\Tokenizer\Tokenizer;
use BabelfishTest\LinguistData;
use PHPUnit\Framework\TestCase;

use function array_slice;
use function implode;

class TokenizerTest extends TestCase
{
    /**
     * @param string[] $expected
     *
     * @dataProvider tokensDataProvider
     */
    public function testTokenization(string $input, array $expected, bool $only_first_key = false): void
    {
        $tokenizer = new Tokenizer();

        $tokens = $tokenizer->extractTokens($input);
        if ($only_first_key) {
            $tokens = array_slice($tokens, 0, 1);
        }

        $this->assertEqualsCanonicalizing($expected, $tokens);
    }

    /**
     * @psalm-return array<array{string, string[]}>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
     */
    public function tokensDataProvider(): array
    {
        return [
            ['print ""', ['print']],
            ['print "Josh"', ['print']],
            ['print \'Josh\'', ['print']],
            ['print "Hello \"Josh\""', ['print']],
            ["print 'Hello \\'Josh\\''", ['print']],
            ['print "Hello", "Josh"', ['print']],
            ["print 'Hello', 'Josh'", ['print']],
            ['print "Hello", "", "Josh"', ['print']],
            ["print 'Hello', '', 'Josh'", ['print']],
            ['1 + 1', ['+']],
            ['1+1', ['+']],
            ['add(123, 456)', ['add', '(', ')']],
            ['0x01 | 0x10', ['|']],
            ['500.42 * 1.0', ['*']],
            ['1.23e-04', []],
            ['1.0f', []],
            ['1234ULL', []],
            ['G1 X55 Y5 F2000', ['G1', 'X55', 'Y5', 'F2000']],
            ["foo\n# Comment", ['foo']],
            ["foo\n# Comment\nbar", ['foo', 'bar']],
            ["foo\n// Comment", ['foo']],
            ["foo\n-- Comment", ['foo']],
            ["foo\n\" Comment", ['foo']],
            ['foo /* Comment */', ['foo']],
            ["foo /* \nComment\n */", ['foo']],
            ['foo <!-- Comment -->', ['foo']],
            ['foo {- Comment -}', ['foo']],
            ['foo (* Comment *)', ['foo']],
            ["2 % 10\n% Comment", ['%']],
            ["foo\n\"\"\"\nComment\n\"\"\"\nbar", ['foo', 'bar']],
            ["foo\n'''\nComment\n'''\nbar", ['foo', 'bar']],
            ['<html></html>', ['<html>', '</html>']],
            ['<div id></div>', ['<div>', 'id', '</div>']],
            ['<div id=foo></div>', ['<div>', 'id=', '</div>']],
            ['<div id class></div>', ['<div>', 'id', 'class', '</div>']],
            ['<div id="foo bar"></div>', ['<div>', 'id=', '</div>']],
            ["<div id='foo bar'></div>", ['<div>', 'id=', '</div>']],
            ['<?xml version="1.0"?>', ['<?xml>', 'version=']],
            ['1 - 1', ['-']],
            ['1 * 1', ['*']],
            ['1 / 1', ['/']],
            ['2 % 5', ['%']],
            ['1 & 1', ['&']],
            ['1 && 1', ['&&']],
            ['1 | 1', ['|']],
            ['1 || 1', ['||']],
            ['1 < 0x01', ['<']],
            ['1 << 0x01', ['<<']],
            [
                implode("\n", LinguistData::getSampleSourceFile('C/hello.h')->getLines()),
                ['#ifndef', 'HELLO_H', '#define', 'HELLO_H', 'void', 'hello', '(', ')', ';', '#endif'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('C/hello.c')->getLines()),
                ['#include', '<stdio.h>', 'int', 'main', '(', ')', '{', 'printf', '(', ')', ';', 'return', ';', '}'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('C++/bar.h')->getLines()),
                ['class', 'Bar', '{', 'protected', 'char', '*name', ';', 'public', 'void', 'hello', '(', ')', ';', '}'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('C++/hello.cpp')->getLines()),
                ['#include', '<iostream>', 'using', 'namespace', 'std', ';', 'int', 'main', '(', ')', '{', 'cout', '<<', '<<', 'endl', ';', '}'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('Objective-C/Foo.h')->getLines()),
                ['#import', '<Foundation/Foundation.h>', '@interface', 'Foo', 'NSObject', '{', '}', '@end'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('Objective-C/Foo.m')->getLines()),
                ['#import', '@implementation', 'Foo', '@end'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('Objective-C/hello.m')->getLines()),
                ['#import', '<Cocoa/Cocoa.h>', 'int', 'main', '(', 'int', 'argc', 'char', '*argv', '[', ']', ')', '{', 'NSLog', '(', '@', ')', ';', 'return', ';', '}'],
            ],
            [implode("\n", LinguistData::getSampleSourceFile('Shell/sh')->getLines()), ['SHEBANG#!sh'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Shell/bash')->getLines()), ['SHEBANG#!bash'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Shell/zsh')->getLines()), ['SHEBANG#!zsh'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Perl/perl')->getLines()), ['SHEBANG#!perl'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Python/python')->getLines()), ['SHEBANG#!python'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Ruby/ruby')->getLines()), ['SHEBANG#!ruby'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Ruby/ruby2')->getLines()), ['SHEBANG#!ruby'], true],
            [implode("\n", LinguistData::getSampleSourceFile('JavaScript/js')->getLines()), ['SHEBANG#!node'], true],
            [implode("\n", LinguistData::getSampleSourceFile('PHP/php')->getLines()), ['SHEBANG#!php'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Erlang/factorial')->getLines()), ['SHEBANG#!escript'], true],
            [implode("\n", LinguistData::getSampleSourceFile('Shell/invalid-shebang.sh')->getLines()), ['echo'], true],
            [
                implode("\n", LinguistData::getSampleSourceFile('JavaScript/hello.js')->getLines()),
                ['(', 'function', '(', ')', '{', 'console.log', '(', ')', ';', '}', ')', '.call', '(', 'this', ')', ';'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('JSON/product.json')->getLines()),
                ['{', '[', ']', '{', '}', '}'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('Ruby/foo.rb')->getLines()),
                ['module', 'Foo', 'end'],
            ],
            [
                implode("\n", LinguistData::getSampleSourceFile('Ruby/filenames/Rakefile')->getLines()),
                ['task', 'default', 'do', 'puts', 'end'],
            ],
        ];
    }
}
