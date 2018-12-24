<?php

namespace MindGeek\Model;

final class Student
{

    private $id;
    private $name;
    private $grades = [];
    private $result;
    public function getId()
    {
        return $this->id;
    }
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
    public function getGrades()
    {
        return $this->grades;
    }
    public function setGrades(array $grades)
    {
        $this->grades = $grades;

        return $this;
    }
    public function getResult()
    {
        return $this->result;
    }
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }
    public function getAverage()
    {
        if (0 === count($this->grades)) {
            return 0;
        }
        return round(array_reduce($this->grades, function($carry,$item){
            return $carry += ($item->grade / $item->ratio) ;
        },0) /count($this->grades),2);
    }

    public function asArray()
      {
          $result = array();

          $clazz = new \ReflectionClass(__CLASS__);
          foreach ($clazz->getMethods() as $method) {
              if (substr($method->name, 0, 3) == 'get') {
                  $propName = strtolower(substr($method->name, 3, 1)) . substr($method->name, 4);

                  if($propName != "grades")
                    $result[$propName] = $method->invoke($this);
                  else
                  {
                    $grades = $method->invoke($this);
                    $listGrade = [];
                    foreach ($grades as $grade) {
                      $listGrade[] = (array)$grade;
                    }
                    $result[$propName] = $listGrade;
                  }

              }
          }

          return $result;
      }
}
