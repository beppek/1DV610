<?php

class DateTimeView {

    public function show() {

        $weekday = date("l");
        $dayOfMonth = date("jS");
        $monthAndYear = date("F Y");
        $time24Hour = date("H:i:s");
        $timeString =  $weekday . ", the " . $dayOfMonth . " of " . $monthAndYear . ", The time is " . $time24Hour;

        return '<p>' . $timeString . '</p>';
    }
}