<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\Shebang;
use PHPUnit\Framework\TestCase;

class ShebangTest extends TestCase
{
    /**
     * @dataProvider shebangFileContentProvider
     */
    public function testSourceFileWithShebang(array $expected_language_names, string $file_content): void
    {
        $file = $this->createMock(SourceFile::class);
        $file->method('getLines')->willReturn(explode("\n", $file_content));

        $strategy = new Shebang();
        $languages = $strategy->getLanguages($file);

        $this->assertSameSize($expected_language_names, $languages);
        $this->assertSame($expected_language_names,
            array_map(
                function (Language $language) {
                    return $language->getName();
                },
                $languages
            )
        );
    }

    public function shebangFileContentProvider(): array
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
            [['Pod'], '#! perl'], // Could be Perl too
            [['Ruby'], "#!/bin/sh\n\n\nexec ruby $0 $@"],
            [['Shell'], '#! /usr/bin/env A=003 B=149 C=150 D=xzd E=base64 F=tar G=gz H=head I=tail sh'],
        ];
    }
}
