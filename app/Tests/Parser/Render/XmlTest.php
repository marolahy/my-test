<?php
namespace MindGeekTest\Parser\Render;

use MindGeek\Parser\Parser;
use MindGeek\Parser\Reader\Xml as XmlReader;
use MindGeek\Parser\Render\xml as XmlRender;


/**
 * @group      MindGeek_Parser
 */
class XmlTest extends AbstractRenderTestCase
{
    public function setUp()
    {
        $this->render = new XmlRender();
        $this->reader = new XmlReader();
    }

    public function testToString()
    {
        $parser = new Parser(['test' => 'foo', 'bar' => [0 => 'baz', 1 => 'foo']]);

        $parseString = $this->render->toString($parser,'root',null);

        $expected = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
</root>

ECS;

        $this->assertEquals($parseString, $expected);
    }

    public function testSectionsToString()
    {
        $parser = new Parser([], true);
        $parser->production = [];

        $parser->production->webhost = 'www.example.com';
        $parser->production->database = [];
        $parser->production->database->params = [];
        $parser->production->database->params->host = 'localhost';
        $parser->production->database->params->username = 'production';
        $parser->production->database->params->password = 'secret';
        $parser->production->database->params->dbname = 'dbproduction';

        $parseString = $this->render->toString($parser,'root',null);

        $expected = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <production>
        <webhost>www.example.com</webhost>
        <database>
            <params>
                <host>localhost</host>
                <username>production</username>
                <password>secret</password>
                <dbname>dbproduction</dbname>
            </params>
        </database>
    </production>
</root>

ECS;

        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $parseString);
    }

    /**
     * @group 6797
     */
    public function testAddBranchProperyConstructsSubBranchesOfTypeNumeric()
    {
        $parser = new Parser([], true);
        $parser->production = [['foo'], ['bar']];

        $parserString = $this->render->toString($parser,'root',null);

        $expected = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<root>
    <production>foo</production>
    <production>bar</production>
</root>

ECS;

        $this->assertEquals($expected, $parserString);
    }
}
