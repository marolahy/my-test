<?php
namespace MindGeekTest\Parser\Render;

use MindGeek\Parser\Parser;
use MindGeek\Parser\Reader\Json as JsonReader;
use MindGeek\Parser\Render\Json as JsonRender;

/**
 * @group      MindGeek_parser
 */
class JsonTest extends AbstractRenderTestCase
{
    public function setUp()
    {
        $this->reader = new JsonReader();
        $this->render = new JsonRender();
    }

    public function testNoSection()
    {
        $parser = new Parser(['test' => 'foo', 'test2' => ['test3' => 'bar']]);

        $this->render->toFile($this->getTestAssetFileName(), $parser);
        \var_dump($this->getTestAssetFileName());
        $parser = $this->reader->fromFile($this->getTestAssetFileName());

        $this->assertEquals('foo', $parser['test']);
        $this->assertEquals('bar', $parser['test2']['test3']);
    }

    public function testWriteAndReadOriginalFile()
    {
        $parser = $this->reader->fromFile(__DIR__ . '/_files/allsections.json');

        $this->render->toFile($this->getTestAssetFileName(), $parser);

        $parser = $this->reader->fromFile($this->getTestAssetFileName());

        $this->assertEquals('multi', $parser['all']['one']['two']['three']);
    }
}
