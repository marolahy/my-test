<?php
namespace MindGeekTest\Parser\Render;

use PHPUnit\Framework\TestCase;
use MindGeek\Parser\Parser;
use MindGeek\Parser\Exception\InvalidArgumentException;
use MindGeek\Parser\Exception\RuntimeException;

/**
 * @group      MindGeek_Parser
 */
abstract class AbstractRenderTestCase extends TestCase
{
    /**
     * @var \MindGeek\Parser\Reader\ReaderInterface
     */
    protected $reader;

    /**
     *
     * @var \MindGeek\Parser\Render\RenderInterface
     */
    protected $render;

    /**
     *
     * @var string
     */
    protected $tmpfile;

    /**
     * Get test asset name for current test case.
     *
     * @return string
     */
    protected function getTestAssetFileName()
    {
        if (empty($this->tmpfile)) {
            $this->tmpfile = tempnam(sys_get_temp_dir(), 'parser-render');
        }
        return $this->tmpfile;
    }

    public function tearDown()
    {
        if (file_exists($this->getTestAssetFileName())) {
            if (! is_writable($this->getTestAssetFileName())) {
                chmod($this->getTestAssetFileName(), 0777);
            }
            @unlink($this->getTestAssetFileName());
        }
    }

    public function testNoFilenameSet()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No file name specified');
        $this->render->toFile('', '');
    }

    public function testFileNotValid()
    {
        $this->expectException(RuntimeException::class);
        $this->render->toFile('.', new Parser([]));
    }

    public function testFileNotWritable()
    {
        $this->expectException(RuntimeException::class);
        chmod($this->getTestAssetFileName(), 0444);
        $this->render->toFile($this->getTestAssetFileName(), new Parser([]));
    }

    public function testWriteAndRead()
    {
        $parser = new Parser(['default' => ['test' => 'foo']]);

        $this->render->toFile($this->getTestAssetFileName(), $parser);

        $parser = $this->reader->fromFile($this->getTestAssetFileName());
        $this->assertEquals('foo', $parser['default']['test']);
    }
}
