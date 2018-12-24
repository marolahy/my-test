<?php
namespace MindGeek\Parser\Render;

use Traversable;
use MindGeek\Parser\Exception;
use MindGeek\Utils\ArrayUtils;

abstract class AbstractRender implements RenderInterface
{
    /**
     * toFile(): defined by Writer interface.
     *
     * @see    WriterInterface::toFile()
     * @param  string  $filename
     * @param  mixed   $parser
     * @param  bool $exclusiveLock
     * @return void
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function toFile($filename, $parser, $exclusiveLock = true)
    {
        if (empty($filename)) {
            throw new Exception\InvalidArgumentException('No file name specified');
        }

        $flags = 0;
        if ($exclusiveLock) {
            $flags |= LOCK_EX;
        }

        set_error_handler(
            function ($error, $message = '') use ($filename) {
                throw new Exception\RuntimeException(
                    sprintf('Error writing to "%s": %s', $filename, $message),
                    $error
                );
            },
            E_WARNING
        );

        try {
            file_put_contents($filename, $this->toString($parser,"root",null), $flags);
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }

        restore_error_handler();
    }

    /**
     * toString(): defined by Writer interface.
     *
     * @see    WriterInterface::toString()
     * @param  mixed   $parser
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function toString($parser,$root="root",$item="item")
    {
        if ($parser instanceof Traversable) {
            $parser = ArrayUtils::iteratorToArray($parser);
        } elseif (! is_array($parser)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable parser');
        }

        return $this->processParse($parser,$root,$item);
    }

    /**
     * @param array $parser
     * @return string
     */
    abstract protected function processParse(array $parser, $root="root",$item="item");
}
