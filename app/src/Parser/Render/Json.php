<?php
namespace MindGeek\Parser\Render;

use  MindGeek\Parser\Exception;

class Json extends AbstractRender
{
    /**
     * processParse(): defined by AbstractRender.
     *
     * @param  array $parser
     * @return string
     * @throws Exception\RuntimeException if encoding errors occur.
     */
    public function processParse(array $parser,$root=null,$item=null)
    {
        $serialized = json_encode($parser, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        if (false === $serialized) {
            throw new Exception\RuntimeException(json_last_error_msg());
        }

        return $serialized;
    }
}
