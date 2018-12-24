<?php
namespace MindGeekTest\Parser\Reader;

use PHPUnit\Framework\TestCase;
use MindGeek\Parser\Exception;
use MindGeek\Parser\Reader\ReaderInterface;

/**
 * @group      Parser
 */
abstract class AbstractReaderTestCase extends TestCase
{
    /**
     * @var ReaderInterface
     */
    protected $reader;

    /**
     * Get test asset name for current test case.
     *
     * @param  string $name
     * @return string
     */
    abstract protected function getTestAssetPath($name);

    public function testMissingFile()
    {
        $filename = $this->getTestAssetPath('no-file');
        $this->expectException(Exception\RuntimeException::class);
        $this->expectExceptionMessage("doesn't exist or not readable");
        $Parser = $this->reader->fromFile($filename);
    }

    public function testFromFile()
    {
        $Parser = $this->reader->fromFile($this->getTestAssetPath('include-base'));
        $this->assertEquals('foo', $Parser['foo']);
    }

    public function testFromEmptyString()
    {
        $Parser = $this->reader->fromString('');
        $this->assertEmpty($Parser);
    }
}
