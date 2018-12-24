<?php

namespace MindGeek\Board;

use MindGeek\Model\Student;

use MindGeek\Parser\Factory;
final class Csm extends AbstractBoard
{


    public function __construct()
    {
      $this->parser = Factory::getReaderManager()->get('json');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $string): string
    {
      if(is_file($string))
      {
        $studentUnFormatedList = self::loadStudentsFromFile($string);
      }else{
        $studentUnFormatedList = self::loadStudentsFromString($string);
      }
      $students = $this->getStudent($studentUnFormatedList["student"]);
        $list = [];
        foreach ($students as $student) {
            $student->setResult($student->getAverage() >= 7 ? 'PASS': 'FAIL');
            $list[] = $student->asArray();
        }

        $render = Factory::getRenderManager()->get("json");
        return $render->toString($list,'students','student');;
    }








}
