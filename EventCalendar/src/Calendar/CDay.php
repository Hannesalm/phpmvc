<?php

namespace Anax\Calendar;

class CDay {
    private $name;
    private $dayOfMonthNumber;


    public function getDayOfMonthNumber()
    {
        return $this->dayOfMonthNumber;
    }


    public function setDayOfMonthNumber($dayOfMonthNumber)
    {
        $this->dayOfMonthNumber = $dayOfMonthNumber;
    }


    public function getName()
    {
        return $this->name;
    }


    public function setName($name)
    {
        $this->name = $name;
    }
}