<?php

namespace MindGeekTest\Board;
use PHPUnit\Framework\TestCase;
use MindGeek\Board\Csmb;
class CsmbTest extends TestCase
{

  public function testFromStringResult()
  {
    $csmb = new Csmb();
    $excepted = preg_replace('/(\>)\s*(\<)/m', '$1$2', file_get_contents(\dirname(__FILE__).'/_files/result.xml'));
    $actual = preg_replace('/(\>)\s*(\<)/m', '$1$2', $csmb->execute(\file_get_contents(\dirname(__FILE__).'/_files/student.xml')));
    $this->assertEquals(trim($excepted),trim($actual));

  }
  public function testFromFile()
  {
    $csmb = new Csmb();
    $excepted = preg_replace('/(\>)\s*(\<)/m', '$1$2', file_get_contents(\dirname(__FILE__).'/_files/result.xml'));
    $actual = preg_replace('/(\>)\s*(\<)/m', '$1$2', $csmb->execute(\dirname(__FILE__).'/_files/student.xml'));

    $this->assertEquals(trim($excepted),trim($actual));

  }

}
