<?php
namespace MindGeek\Parser;

use Psr\Container\ContainerInterface;

class ReaderManager implements ContainerInterface
{
    private $knownPlugins = [
        'json'           => Reader\Json::class,
        'xml'            => Reader\Xml::class,
    ];

    /**
     * @param string $plugin
     * @return bool
     */
    public function has($plugin)
    {
        if (in_array($plugin, array_values($this->knownPlugins), true)) {
            return true;
        }

        return in_array(strtolower($plugin), array_keys($this->knownPlugins), true);
    }

    /**
     * @param string $plugin
     * @return Reader\ReaderInterface
     * @throws Exception\ParserNotFoundException
     */
    public function get($plugin)
    {
        if (! $this->has($plugin)) {
            throw new Exception\ParserNotFoundException(sprintf(
                'Parser reader plugin by name %s not found',
                $plugin
            ));
        }

        if (! class_exists($plugin)) {
            $plugin = $this->knownPlugins[strtolower($plugin)];
        }

        return new $plugin();
    }
}
