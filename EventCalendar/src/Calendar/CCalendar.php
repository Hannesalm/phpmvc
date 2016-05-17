<?php

namespace Anax\Calendar;

use Anax\Events\Event;
use Anax\Events\EventController;

class CCalendar {
    private $weeks = array();
    private $month;
    private $year;
    private $events = array();

    public function setEvents($values = []){
        $this->events = $values;
    }

    public function getValues(){
        if(isset($_GET['year']) && isset($_GET['month'])){
            $this->setYear($_GET['year'])->setMonth($_GET['month']);
        } else {
            $this->setYear(date('Y'))->setMonth(date('m'));
        }
    }

    public function getWeeks()
    {
        return $this->weeks;
    }

    public function prev(){
      $year = $this->year;
      $month = $this->month;

        if($month == 1){
            $month = 12;
          	$year--;
        } else {
            $month--;
        }

      return ["year" => $year, "month"=>$month];
    }
    public function next(){
      $year = $this->year;
        $month = $this->month;
          if($month == 12){
              $month = 1;
              $year++;
          } else {
              $month++;
          }
        return ["year" => $year, "month"=>$month];
    }
    public function addWeek($week)
    {
        $this->weeks[] = $week;
    }

    public function setMonth($month)
    {
        $this->month = (int)$month;
        return $this;
    }

    public function getMonthNumber(){
        $dateString = strtotime(sprintf('%s-%s-01', $this->year, $this->month));
        return $month = date('m', $dateString);
    }
    public function getMonthName(){
        $dateString = strtotime(sprintf('%s-%s-01', $this->year, $this->month));
        return $month = date('F', $dateString);
    }

    public function setYear($year){
        $this->year = $year;
        return $this;
    }
    public function getMonth(){
        return $this->month;
    }
    public function getYear(){
        return $this->year;
    }

    public function generateCalenderData(){
        $daysUsed = 0;
        $dateString = strtotime(sprintf('%s-%s-01', $this->year, $this->month));
        $monthDays = date('t', $dateString);
        $dayOfWeek = date('w', $dateString);
        $firstWeek = new CWeek();
        $emptyDays = $this->getAmountOfEmptyDays($dayOfWeek);
        for($i = 1; $i<=$emptyDays; $i++){
            $firstWeek->addWeekday(new CDay());
        }

        for($i = 1; $i<=7-$emptyDays;$i++){
            $dateString = strtotime(sprintf('%s-%s-%s', $this->year, $this->month, $i));
            $day = new CDay();
            $day->setName(date('l', $dateString));
            $day->setDayOfMonthNumber($i);
            $firstWeek->addWeekday($day);
            $daysUsed ++;
        }

        $this->addWeek($firstWeek);
        $week = null;

        for($date = $daysUsed+1;$date<=$monthDays;$date++){
            $dateString = strtotime(sprintf('%s-%s-%s', $this->year, $this->month, $date));
            $dayOfWeek = date('w', $dateString);
            if($dayOfWeek == 1){
                if($week){
                    $this->addWeek($week);
                }
                $week = new CWeek();
            }

            $day = new CDay();
            $day->setName(date('l', $dateString));
            $day->setDayOfMonthNumber($date);
            $week->addWeekday($day);

        }
        $extraDays = 7-$week->getLengthOfDayArray();

        for($i = 0;$i<$extraDays;$i++){
            $week->addWeekday(new CDay());
        }
        $this->addWeek($week);

    }


    private function getAmountOfEmptyDays($firstDayOfMonth){
        $number = 0;
        if($firstDayOfMonth == 0){
            $number = 6;
        } else {
            $number = $firstDayOfMonth -1;
        }
        return $number;
    }

    public function printCalendar(){
        $prevData = $this->prev();
        $nextData = $this->next();
        $html = "<div class='headerCalendar'>
                    <p class='year'> $this->year</p><p class='month'>" . $this->getMonthName() . "</p>
                 </div>
                 <div>
                 <table class='bordered'>";

        foreach($this->getWeeks() as $week){
            $html .= "<tr>";
            foreach($week -> getWeekdays() as $day){
                $currentDay = $day->getName();
                $todaysDay = date('j');
                $currentMonth = date('m');
                $html .= "<td>";

                if($currentDay == "Sunday"){
                    $html .= "<div class='redNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                    $html .= "<div class='redName'>" . $day->getName() . "</div>";
                } else {
                    if($day->getDayOfMonthNumber() == $todaysDay && $this->month == $currentMonth) {
                        $html .= "<div class='current'><div class='monthNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                        $html .= "<div class='monthName'>" . $day->getName() . "</div></div>";
                    } else {
                        if($this->month == 12 && $day->getDayOfMonthNumber() == 24){
                            $html .= "<div class='christmas'><div class='monthNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                            $html .= "<div class='monthName'>" . $day->getName() . "</div></div>";
                        } else {
                            $html .= "<div class='monthNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                            $html .= "<div class='monthName'>" . $day->getName() . "</div>";
                        }
                    }
                }
                $html .= "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table></div>";
        return $html;
    }

    public function printMiniCalendar(){
        $img = $this->month;
        $prevData = $this->prev();
        $nextData = $this->next();
        $html = "
                 <div class='leftArrow'> <a href='?month=". $prevData["month"] ."&year=" . $prevData["year"] . "'><img src='./img/orangeArrow.png'></a></div>
                 <div class='rightArrow'> <a href='?month=". $nextData["month"] ."&year=" . $nextData["year"] . "'><img src='./img/orangeArrow.png'></a></div>
                 <div class='bordered'>
                 <table>";




        foreach($this -> getWeeks() as $week){
            $html .= "<tr>";
            foreach($week -> getWeekdays() as $day){
                $currentDay = $day->getName();
                $todaysDay = date('j');
                $currentMonth = date('m');
                $html .= "<td>";

                if($currentDay == "Sunday"){
                    $html .= "<div class='redNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                } else {
                    if($day->getDayOfMonthNumber() == $todaysDay && $this->month == $currentMonth) {
                        $html .= "<div class='current'><div class='monthNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                    } else {
                        if($this->month == 12 && $day->getDayOfMonthNumber() == 24){
                            $html .= "<div class='christmas'><div class='monthNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                        } else {
                            $html .= "<div class='monthNumber'>" . $day->getDayOfMonthNumber() . "</div>";
                        }
                    }
                }
                $html .= "</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table></div>";
        return $html;
    }

    public function printResponsiveCalendar($count){
        $prevData = $this->prev();
        $nextData = $this->next();

        $html = "
                    <div class='page-header'>
                        <h1 class='text-center'>" . $this->getMonthName() ." ". $this->year ."</h1>
                            <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\".bd-example-modal-sm\">Add Event</button>
                            <div class='btn-group '>

                            <a href='?month=". $prevData["month"] ."&year=" . $prevData["year"] . "' class='btn btn-primary btn-md'>Previus</a>
                            <a href='?month=". $nextData["month"] ."&year=" . $nextData["year"] . "' class='btn btn-primary btn-md'>Next</a>
                        </div>
                    </div>
                    <div class='col-md-8'>
                    <div class='calendar'>
        ";

        $html .= "<ul class='weekdays'>
            <li>Mon</li>
            <li>Tue</li>
            <li>Wed</li>
            <li>Thu</li>
            <li>Fri</li>
            <li>Sat</li>
            <li>Sun</li>
        </ul>";



        foreach($this->getWeeks() as $week){
            $html .= "<ul class='days'>";
            foreach($week -> getWeekdays() as $day){
                if($day->getDayOfMonthNumber() == null){
                    $id = 0;

                } else {
                    if($count[$day->getDayOfMonthNumber()] == null){
                        $id = 0;
                    }
                    else{
                    $id = $day->getDayOfMonthNumber();
                    }

                }
                if($day->getDayOfMonthNumber() == null){
                        $html .= "<li class='day-empty'></li>";
                } else {

                    $html .= "
                    <a href='calendar?month=".$this->month."&year=".$this->year."&date=". $day->getDayOfMonthNumber() ."'><li class='days'>";
                        if($id == 0){
                            $html .= "<span class='label label-danger label-pill pull-xs-left'></span>";
                        } else {
                            $html .= "<span class='label label-danger label-pill pull-right '>$count[$id]</span>";
                        }

                    $html .="<div class='date'>". $day->getDayOfMonthNumber() ."</div>
                    </li>
                    </a>
                    ";
                }
            }
            $html .= "</ul>";
        }

        $html .= "</div></div>";

        return $html;
    }

}
