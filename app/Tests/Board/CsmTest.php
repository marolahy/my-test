<?php

namespace MindGeekTest\Board;
use PHPUnit\Framework\TestCase;
use MindGeek\Model\Student;

use MindGeek\Board\Csm;
class CsmTest extends TestCase
{

  public function testFromStringResult()
  {
    $csm = new Csm();

    $this->assertEquals(json_decode(file_get_contents(\dirname(__FILE__).'/_files/result.json')),
    \json_decode($csm->execute(\file_get_contents(\dirname(__FILE__).'/_files/student.json'))));

  }

  public function testFromFile()
  {
    $csm = new Csm();

    $this->assertEquals(json_decode(file_get_contents(\dirname(__FILE__).'/_files/result.json')),
    json_decode($csm->execute(\dirname(__FILE__).'/_files/student.json')));

  }


}
