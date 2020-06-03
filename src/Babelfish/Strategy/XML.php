<?php

declare(strict_types=1);

namespace Babelfish\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;

use function array_slice;
use function assert;
use function implode;
use function strpos;

final class XML implements Strategy
{
    private const SEARCH_SCOPE = 2;

    /**
     * @return Language[]
     */
    public function getLanguages(SourceFile $file, Language ...$language_candidates): array
    {
        if (! empty($language_candidates)) {
            return $language_candidates;
        }

        $header = implode('', array_slice($file->getLines(), 0, self::SEARCH_SCOPE));

        if (strpos($header, '<?xml version=') !== false) {
            /**
             * @see \BabelfishTest\Strategy\XMLTest::testXMLLanguageIsPresent
             */
            $xml_language = Language::findByAlias('XML');
            assert($xml_language instanceof Language);

            return [$xml_language];
        }

        return [];
    }
}
