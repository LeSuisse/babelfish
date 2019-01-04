<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Filter\OnlyKeepLanguageAlreadyCandidatesFilter;
use Babelfish\Strategy\Shebang;
use PHPUnit\Framework\TestCase;
use function array_map;
use function explode;

class ShebangTest extends TestCase
{
    /**
     * @param string[] $expected_language_names
     *
     * @dataProvider shebangFileContentProvider
     */
    public function testSourceFileWithShebang(array $expected_language_names, string $file_content) : void
    {
        $file = $this->createMock(SourceFile::class);
        $file->method('getLines')->willReturn(explode("\n", $file_content));

        $pass_out_filter = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);
        $pass_out_filter->method('filter')->willReturnCallback(
            static function (array $language_candidates, Language ...$found_languages) {
                return $found_languages;
            }
        );

        $strategy  = new Shebang($pass_out_filter);
        $languages = $strategy->getLanguages($file);

        $this->assertSameSize($expected_language_names, $languages);
        $this->assertSame(
            $expected_language_names,
            array_map(
                static function (Language $language) {
                    return $language->getName();
                },
                $languages
            )
        );
    }

    public function testAFileWithoutAnyLinesDoesNotFindAnyLanguage() : void
    {
        $file = $this->createMock(SourceFile::class);
        $file->method('getLines')->willReturn([]);

        $filter = $this->createMock(OnlyKeepLanguageAlreadyCandidatesFilter::class);

        $strategy  = new Shebang($filter);
        $languages = $strategy->getLanguages($file);

        $this->assertEmpty($languages);
    }

    /**
     * @return <string[]|string>[]
     */
    public function shebangFileContentProvider() : array
    {
        return [
            [[], ''],
            [[], 'foo'],
            [[], '#bar'],
            [[], '#baz'],
            [[], '///'],
            [[], "\n\n\n\n\n"],
            [[], ' #!/usr/sbin/ruby'],
            [[], "\n#!/usr/sbin/ruby"],
            [[], '#!'],
            [[], '#! '],
            [[], '#!/usr/bin/env'],
            [['Ruby'], "#!/usr/sbin/ruby\n# bar"],
            [['Ruby'], "#!/usr/bin/ruby\n# foo"],
            [['Ruby'], '#!/usr/sbin/ruby'],
            [['Ruby'], "#!/usr/sbin/ruby foo bar baz\n"],
            [['R'], "#!/usr/bin/env Rscript\n# example R script\n#\n"],
            [['Crystal'], '#!/usr/bin/env bin/crystal'],
            [['Ruby'], "#!/usr/bin/env ruby\n# baz"],
            [['Shell'], "#!/usr/bin/bash\n"],
            [['Shell'], '#!/bin/sh'],
            [['Python'], "#!/bin/python\n# foo\n# bar\n# baz"],
            [['Python'], "#!/usr/bin/python2.7\n\n\n\n"],
            [['Common Lisp'], "#!/usr/bin/sbcl --script\n\n"],
            [['Perl', 'Pod'], '#! perl'],
            [['Ruby'], "#!/bin/sh\n\n\nexec ruby $0 $@"],
            [['Shell'], '#! /usr/bin/env A=003 B=149 C=150 D=xzd E=base64 F=tar G=gz H=head I=tail sh'],
        ];
    }
}
