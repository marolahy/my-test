<?php
namespace MindGeekTest\Parser;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;
use MindGeek\Parser\Factory;
use MindGeek\Parser\ReaderManager;
use MindGeek\Parser\RenderManager;

/**
 * @group      Parser
 */
class FactoryTest extends TestCase
{
    protected $tmpFiles = [];
    protected $originalIncludePath;

    protected function getTestAssetFileName($ext)
    {
        if (empty($this->tmpfiles[$ext])) {
            $this->tmpfiles[$ext] = tempnam(sys_get_temp_dir(), 'mindgeek-parser-render').'.'.$ext;
        }
        return $this->tmpfiles[$ext];
    }

    public function setUp()
    {
        $this->originalIncludePath = get_include_path();
        set_include_path(__DIR__ . '/TestAssets');
        $this->resetPluginManagers();
    }

    public function tearDown()
    {
        set_include_path($this->originalIncludePath);

        foreach ($this->tmpFiles as $file) {
            if (file_exists($file)) {
                if (! is_writable($file)) {
                    chmod($file, 0777);
                }
                @unlink($file);
            }
        }

        $this->resetPluginManagers();
    }

    public function resetPluginManagers()
    {
        foreach (['readers', 'renders'] as $pluginManager) {
            $r = new ReflectionProperty(Factory::class, $pluginManager);
            $r->setAccessible(true);
            $r->setValue(null);
        }
    }


    public function testFromXml()
    {
        $Parser = Factory::fromFile(__DIR__ . '/TestAssets/Xml/include-base.xml');

        $this->assertEquals('bar', $Parser['base']['foo']);
    }

    public function testFromXmlFiles()
    {
        $files = [
            __DIR__ . '/TestAssets/Xml/include-base.xml',
            __DIR__ . '/TestAssets/Xml/include-base2.xml'
        ];
        $Parser = Factory::fromFiles($files);

        $this->assertEquals('bar', $Parser['base']['foo']);
        $this->assertEquals('baz', $Parser['test']['bar']);
    }



    public function testNonExistentFileThrowsRuntimeException()
    {
        $this->expectException(RuntimeException::class);
        $Parser = Factory::fromFile('foo.bar');
    }

    public function testUnsupportedFileExtensionThrowsRuntimeException()
    {
        $this->expectException(RuntimeException::class);
        $Parser = Factory::fromFile(__DIR__ . '/TestAssets/bad.ext');
    }



    public function testFactoryToFileInvalidFileExtension()
    {
        $this->expectException(RuntimeException::class);
        $result = Factory::toFile(__DIR__.'/TestAssets/bad.ext', []);
    }




    public function testDefaultReaderManager()
    {
        $readers = Factory::getReaderManager();
        $this->assertInstanceOf(ReaderManager::class, $readers);
    }

    public function testDefaultrenderManager()
    {
        $renders = Factory::getRenderManager();
        $this->assertInstanceOf(RenderManager::class, $renders);
    }

}
