<?php
namespace MindGeek\Parser\Exception;

use Psr\Container\NotFoundExceptionInterface;

class ParserNotFoundException extends RuntimeException implements
    NotFoundExceptionInterface
{
}
