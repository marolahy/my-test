<?php
namespace MindGeekTest\Parser\Reader\TestAssets;

use MindGeek\Parser\Exception;
use MindGeek\Parser\Reader\ReaderInterface;

class DummyReader implements ReaderInterface
{
    public function fromFile($filename)
    {
        if (! is_readable($filename)) {
            throw new Exception\RuntimeException("File '{$filename}' doesn't exist or not readable");
        }

        return unserialize(file_get_contents($filename));
    }

    public function fromString($string)
    {
        if (empty($string)) {
            return [];
        }

        return unserialize($string);
    }
}
