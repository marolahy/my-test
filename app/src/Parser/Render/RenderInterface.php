<?php

namespace MindGeek\Parser\Render;

interface RenderInterface
{
  /**
   * Write a parser object to a file.
   *
   * @param  string  $filename
   * @param  mixed   $parser
   * @param  bool $exclusiveLock
   * @return void
   */
  public function toFile($filename, $parser, $exclusiveLock = true);

  /**
   * Write a parser object to a string.
   *
   * @param  mixed parser
   * @return string
   */
  public function toString($parser, $root="root");
}
