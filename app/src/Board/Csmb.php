<?php

namespace MindGeek\Board;

use MindGeek\Model\Student;
use MindGeek\Parser\Factory;
class Csmb extends AbstractBoard
{
    public function __construct()
    {
      $this->parser = Factory::getReaderManager()->get('xml');
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
          $grades = $student->getGrades();
          $finalGrades = [];
          usort($grades,function ($a, $b){
              return ($a->grade / $a->ratio) < ($a->grade / $a->ratio);
          });
          $finalGrades = array_slice($grades, 0, 2);
          $student->setGrades($finalGrades);
          $student->setResult($student->getAverage() >= 10 ? 'PASS': 'FAIL');
          $list[] = $student->asArray();
        }

        $render = Factory::getRenderManager()->get("xml");
        return $render->toString($list,"students","student");
    }
}
