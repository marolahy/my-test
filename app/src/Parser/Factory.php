<?php
namespace MindGeek\Parser;
use MindGeek\Utils\ArrayUtils;
class Factory
{
    /**
     * Plugin manager for loading readers
     *
     * @var null|ContainerInterface
     */
    public static $readers = null;

    /**
     * Plugin manager for loading Renders
     *
     * @var null|ContainerInterface
     */
    public static $renders = null;

    /**
     * Registered Parser file extensions.
     * key is extension, value is reader instance or plugin name
     *
     * @var array
     */
    protected static $extensions = [
        'json'        => 'json',
        'xml'         => 'xml',
    ];

    /**
     * Register Parser file extensions for writing
     * key is extension, value is Render instance or plugin name
     *
     * @var array
     */
    protected static $renderExtensions = [
        'json' => 'json',
        'xml'  => 'xml',
    ];

    /**
     * Read a Parser from a file.
     *
     * @param  string  $filename
     * @param  bool $returnParserObject
     * @param  bool $useIncludePath
     * @return array|Parser
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public static function fromFile($filename, $returnParserObject = false, $useIncludePath = false)
    {
        $filepath = $filename;
        if (! file_exists($filename)) {
            if (! $useIncludePath) {
                throw new Exception\RuntimeException(sprintf(
                    'Filename "%s" cannot be found relative to the working directory',
                    $filename
                ));
            }

            $fromIncludePath = stream_resolve_include_path($filename);
            if (! $fromIncludePath) {
                throw new Exception\RuntimeException(sprintf(
                    'Filename "%s" cannot be found relative to the working directory or the include_path ("%s")',
                    $filename,
                    get_include_path()
                ));
            }
            $filepath = $fromIncludePath;
        }

        $pathinfo = pathinfo($filepath);

        if (! isset($pathinfo['extension'])) {
            throw new Exception\RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected',
                $filename
            ));
        }

        $extension = strtolower($pathinfo['extension']);

       if (isset(static::$extensions[$extension])) {
            $reader = static::$extensions[$extension];
            if (! $reader instanceof Reader\ReaderInterface) {
                $reader = static::getReaderManager()->get($reader);
                static::$extensions[$extension] = $reader;
            }

            /* @var Reader\ReaderInterface $reader */
            $Parser = $reader->fromFile($filepath);
        } else {
            throw new Exception\RuntimeException(sprintf(
                'Unsupported Parser file extension: .%s',
                $pathinfo['extension']
            ));
        }

        return ($returnParserObject) ? new Parser($Parser) : $Parser;
    }

    /**
     * Read Parseruration from multiple files and merge them.
     *
     * @param  array   $files
     * @param  bool $returnParserObject
     * @param  bool $useIncludePath
     * @return array|Parser
     */
    public static function fromFiles(array $files, $returnParserObject = false, $useIncludePath = false)
    {
        $Parser = [];

        foreach ($files as $file) {
            $Parser = ArrayUtils::merge($Parser, static::fromFile($file, false, $useIncludePath));
        }

        return ($returnParserObject) ? new Parser($Parser) : $Parser;
    }

    /**
     * Writes a Parser to a file
     *
     * @param string $filename
     * @param array|Parser $Parser
     * @return bool TRUE on success | FALSE on failure
     * @throws Exception\RuntimeException
     * @throws Exception\InvalidArgumentException
     */
    public static function toFile($filename, $Parser)
    {
        if ((is_object($Parser) && ! ($Parser instanceof Parser))
            || (! is_object($Parser) && ! is_array($Parser))
        ) {
            throw new Exception\InvalidArgumentException(
                __METHOD__." \$Parser should be an array or instance of MindGeek\\Parser\\Parser"
            );
        }

        $extension = substr(strrchr($filename, '.'), 1);
        $directory = dirname($filename);

        if (! is_dir($directory)) {
            throw new Exception\RuntimeException(
                "Directory '{$directory}' does not exists!"
            );
        }

        if (! is_writable($directory)) {
            throw new Exception\RuntimeException(
                "Cannot write in directory '{$directory}'"
            );
        }

        if (! isset(static::$renderExtensions[$extension])) {
            throw new Exception\RuntimeException(
                "Unsupported Parser file extension: '.{$extension}' for writing."
            );
        }

        $render = static::$renderExtensions[$extension];
        if (($render instanceof Render\AbstractRender) === false) {
            $render = self::getRenderManager()->get($render);
            static::$renderExtensions[$extension] = $render;
        }

        if (is_object($Parser)) {
            $Parser = $Parser->toArray();
        }

        $content = $render->processParser($Parser);

        return (bool) (file_put_contents($filename, $content) !== false);
    }

    /**
     * Set reader plugin manager
     *
     * @param ReaderManager $readers
     * @return void
     */
    public static function setReaderManager(ReaderManager $readers)
    {
        static::$readers = $readers;
    }

    /**
     * Get the reader plugin manager.
     *
     * If none is available, registers and returns a
     * ReaderManager instance by default.
     *
     * @return ReaderManager
     */
    public static function getReaderManager()
    {
        if (static::$readers === null) {
            static::$readers = new ReaderManager();
        }
        return static::$readers;
    }

    /**
     * Set Render plugin manager
     *
     * @param MindGeek\Parser\RenderManager $renders
     * @return void
     */
    public static function setRenderManager(RenderManager $renders)
    {
        static::$renders = $renders;
    }

    /**
     * Get the Render plugin manager.
     *
     * If none is available, registers and returns a
     * RenderManager instance by default.
     *
     * @return ContainerInterface
     */
    public static function getRenderManager()
    {
        if (static::$renders === null) {
            static::$renders = new RenderManager();
        }

        return static::$renders;
    }

    /**
     * Set Parser reader for file extension
     *
     * @param  string $extension
     * @param  string|Reader\ReaderInterface $reader
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public static function registerReader($extension, $reader)
    {
        $extension = strtolower($extension);

        if (! is_string($reader) && ! $reader instanceof Reader\ReaderInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Reader should be plugin name, class name or ' .
                'instance of %s\Reader\ReaderInterface; received "%s"',
                __NAMESPACE__,
                (is_object($reader) ? get_class($reader) : gettype($reader))
            ));
        }

        static::$extensions[$extension] = $reader;
    }

    /**
     * Set Parser Render for file extension
     *
     * @param string $extension
     * @param string|Render\AbstractRender $render
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    public static function registerRender($extension, $render)
    {
        $extension = strtolower($extension);

        if (! is_string($render) && ! $render instanceof Render\AbstractRender) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Render should be plugin name, class name or ' .
                'instance of %s\Render\AbstractRender; received "%s"',
                __NAMESPACE__,
                (is_object($render) ? get_class($render) : gettype($render))
            ));
        }

        static::$renderExtensions[$extension] = $render;
    }
}
