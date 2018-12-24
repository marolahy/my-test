<?php

namespace MindGeek\Board;

use MindGeek\Model\Student;
use MindGeek\Parser\Factory;
use MindGeek\Parser\Parser;
abstract class AbstractBoard
{


  protected $parser;
  public function loadStudentsFromFile($filename)
  {
    $this->parser = Factory::fromFile($filename);
    return $this->parser;
  }

  /**
  *
  */

  protected function getStudent(array $studentList): array
  {
      $list = [];
      foreach ($studentList as $student) {
        if(!array_key_exists('id',$student))
          continue;
        $currentStudent = new Student();
        $currentStudent->setId(intval($student['id']));
        $currentStudent->setName($student['name']);
        $courses = [];
        foreach($student['courses'] as $course)
        {
          foreach($course as $c)
            $courses[] = (object) $c;
        }
        $currentStudent->setGrades($courses);
        $list[] = $currentStudent;
      }
      return $list;
  }

  public function loadStudentsFromString($string)
  {
    return $this->parser->fromString($string);
  }

  /**
   * Execute
   *
   *
   * @return string result
   */
  abstract public function execute(string $string): string;


}
