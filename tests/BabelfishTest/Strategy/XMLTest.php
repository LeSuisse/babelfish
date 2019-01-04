<?php

declare(strict_types=1);

namespace BabelfishTest\Strategy;

use Babelfish\File\SourceFile;
use Babelfish\Language;
use Babelfish\Strategy\XML;
use BabelfishTest\LinguistData;
use PHPUnit\Framework\TestCase;

class XMLTest extends TestCase
{
    /**
     * @dataProvider XMLFileProviders
     */
    public function testSourceFileXML(bool $is_xml, SourceFile $file) : void
    {
        $strategy  = new XML();
        $languages = $strategy->getLanguages($file);

        if ($is_xml) {
            $this->assertCount(1, $languages);
            $this->assertSame('XML', $languages[0]->getName());
        } else {
            $this->assertEmpty($languages);
        }
    }

    /**
     * @return <bool|SourceFile>[]
     */
    public function XMLFileProviders()
    {
        return [
            [false, LinguistData::getSampleSourceFile('XML/libsomething.dll.config')],
            [false, LinguistData::getSampleSourceFile('XML/real-estate.mjml')],
            [false, LinguistData::getSampleSourceFile('XML/XmlIO.pluginspec')],
            [false, LinguistData::getSampleSourceFile('XML/MainView.ux')],
            [false, LinguistData::getSampleSourceFile('XML/MyApp.ux')],
            [false, LinguistData::getSampleSourceFile('XML/xhtml-struct-1.mod')],
            [false, LinguistData::getSampleSourceFile('XML/wixdemo.wixproj')],
            [false, LinguistData::getSampleSourceFile('XML/msbuild-example.proj')],
            [false, LinguistData::getSampleSourceFile('XML/sample.targets')],
            [false, LinguistData::getSampleSourceFile('XML/Default.props')],
            [false, LinguistData::getSampleSourceFile('XML/racoon.mjml')],
            [false, LinguistData::getSampleSourceFile('XML/some-ideas.mm')],
            [false, LinguistData::getSampleSourceFile('XML/GMOculus.project.gmx')],
            [false, LinguistData::getSampleSourceFile('XML/obj_control.object.gmx')],
            [true, LinguistData::getFixtureSourceFile('XML/app.config')],
            [true, LinguistData::getFixtureSourceFile('XML/AssertionIDRequestOptionalAttributes.xml.svn-base')],
        ];
    }

    public function testDoNotTryToDetectXMLWhenLanguageCandidatesExist() : void
    {
        $language_candidates = [$this->createMock(Language::class), $this->createMock(Language::class)];
        $strategy            = new XML();
        $detected_languages  = $strategy->getLanguages(
            $this->createMock(SourceFile::class),
            ...$language_candidates
        );

        $this->assertSame($language_candidates, $detected_languages);
    }
}
