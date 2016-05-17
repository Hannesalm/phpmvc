<?php

namespace Anax\Calendar;

class CWeek {
    private $weekdays = array();
    private $weekNr;

    public function addWeekday($day){
        $this->weekdays[] = $day;
    }
    public function getWeekdays(){
        return $this->weekdays;
    }
    public function getLengthOfDayArray(){
        return count($this->weekdays);
    }
}