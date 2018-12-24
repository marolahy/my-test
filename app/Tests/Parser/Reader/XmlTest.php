<?php
namespace MindGeekTest\Parser\Reader;

use PHPUnit\Framework\Error\Warning;
use ReflectionProperty;
use XMLReader;
use MindGeek\Parser\Exception;
use MindGeek\Parser\Reader\Xml;

/**
 * @group      Parser
 *
 * @covers \MindGeek\Parser\Reader\Xml
 */
class XmlTest extends AbstractReaderTestCase
{
    public function setUp()
    {
        $this->reader = new Xml();
    }

    public function tearDown()
    {
        restore_error_handler();
    }

    /**
     * getTestAssetPath(): defined by AbstractReaderTestCase.
     *
     * @see    AbstractReaderTestCase::getTestAssetPath()
     * @return string
     */
    protected function getTestAssetPath($name)
    {
        return __DIR__ . '/TestAssets/Xml/' . $name . '.xml';
    }

    /**
     * PHPUnit 5.7 does not namespace error classes; retrieve appropriate one
     * based on what is available.
     *
     * @return string
     */
    protected function getExpectedWarningClass()
    {
        return class_exists(Warning::class) ? Warning::class : \PHPUnit_Framework_Error_Warning::class;
    }

    public function testInvalidXmlFile()
    {
        $this->reader = new Xml();
        $this->expectException(Exception\RuntimeException::class);
        $arrayXml = $this->reader->fromFile($this->getTestAssetPath('invalid'));
    }
    

    public function testFromString()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
</root>

ECS;

        $arrayXml = $this->reader->fromString($xml);
        $this->assertEquals($arrayXml['test'], 'foo');
        $this->assertEquals($arrayXml['bar'][0], 'baz');
        $this->assertEquals($arrayXml['bar'][1], 'foo');
    }

    public function testInvalidString()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <bar>baz</baz>
</root>

ECS;
        $this->expectException(Exception\RuntimeException::class);
        $this->reader->fromString($xml);
    }

    public function testZF300MultipleKeysOfTheSameName()
    {
        $Parser = $this->reader->fromFile($this->getTestAssetPath('array'));

        $this->assertEquals('2a', $Parser['one']['two'][0]);
        $this->assertEquals('2b', $Parser['one']['two'][1]);
        $this->assertEquals('4', $Parser['three']['four'][1]);
        $this->assertEquals('5', $Parser['three']['four'][0]['five']);
    }

    public function testZF300ArraysWithMultipleChildren()
    {
        $Parser = $this->reader->fromFile($this->getTestAssetPath('array'));

        $this->assertEquals('1', $Parser['six']['seven'][0]['eight']);
        $this->assertEquals('2', $Parser['six']['seven'][1]['eight']);
        $this->assertEquals('3', $Parser['six']['seven'][2]['eight']);
        $this->assertEquals('1', $Parser['six']['seven'][0]['nine']);
        $this->assertEquals('2', $Parser['six']['seven'][1]['nine']);
        $this->assertEquals('3', $Parser['six']['seven'][2]['nine']);
    }

    /**
     * @group zf6279
     */
    public function testElementWithBothAttributesAndAStringValueIsProcessedCorrectly()
    {
        $this->reader = new Xml();
        $arrayXml = $this->reader->fromFile($this->getTestAssetPath('attributes'));
        $this->assertArrayHasKey('one', $arrayXml);
        $this->assertInternalType('array', $arrayXml['one']);

        // No attribute + text value == string
        $this->assertArrayHasKey(0, $arrayXml['one']);
        $this->assertEquals('bazbat', $arrayXml['one'][0]);

        // Attribute(s) + text value == array
        $this->assertArrayHasKey(1, $arrayXml['one']);
        $this->assertInternalType('array', $arrayXml['one'][1]);
        // Attributes stored in named array keys
        $this->assertArrayHasKey('foo', $arrayXml['one'][1]);
        $this->assertEquals('bar', $arrayXml['one'][1]['foo']);
        // Element value stored in special key '_'
        $this->assertArrayHasKey('_', $arrayXml['one'][1]);
        $this->assertEquals('bazbat', $arrayXml['one'][1]['_']);
    }

    /**
     * @group 6761
     * @group 6730
     */
    public function testReadNonExistingFilesWillFailWithException()
    {
        $ParserReader = new Xml();

        $this->expectException(Exception\RuntimeException::class);

        $ParserReader->fromFile(sys_get_temp_dir() . '/path/that/does/not/exist');
    }

    /**
     * @group 6761
     * @group 6730
     */
    public function testCloseWhenCallFromFileReaderGetInvalid()
    {
        $ParserReader = new Xml();

        $ParserReader->fromFile($this->getTestAssetPath('attributes'));

        $xmlReader = $this->getInternalXmlReader($ParserReader);

        $this->expectException($this->getExpectedWarningClass());

        // following operation should fail because the internal reader is closed (and expected to be closed)
        $xmlReader->setParserProperty(XMLReader::VALIDATE, true);
    }

    /**
     * @group 6761
     * @group 6730
     */
    public function testCloseWhenCallFromStringReaderGetInvalid()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
</root>

ECS;

        $ParserReader = new Xml();

        $ParserReader->fromString($xml);

        $xmlReader = $this->getInternalXmlReader($ParserReader);

        $this->expectException($this->getExpectedWarningClass());

        // following operation should fail because the internal reader is closed (and expected to be closed)
        $xmlReader->setParserProperty(XMLReader::VALIDATE, true);
    }

    /**
     * Reads the internal XML reader from a given Xml Parser reader
     *
     * @param Xml $xml
     *
     * @return XMLReader
     */
    private function getInternalXmlReader(Xml $xml)
    {
        $reflectionReader = new ReflectionProperty('MindGeek\Parser\Reader\Xml', 'reader');

        $reflectionReader->setAccessible(true);

        $xmlReader = $reflectionReader->getValue($xml);

        $this->assertInstanceOf('XMLReader', $xmlReader);

        return $xmlReader;
    }
}
