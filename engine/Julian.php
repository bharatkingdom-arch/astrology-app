<?php

class Julian {

    // Convert Date & Time to Julian Day
    public static function calculate($year, $month, $day, $hour, $minute = 0, $second = 0) {

        // Convert full time into decimal hours
        $decimalHour = $hour + ($minute / 60) + ($second / 3600);

        if ($month <= 2) {
            $year -= 1;
            $month += 12;
        }

        $A = floor($year / 100);
        $B = 2 - $A + floor($A / 4);

        $JD = floor(365.25 * ($year + 4716))
            + floor(30.6001 * ($month + 1))
            + $day + $B - 1524.5 + ($decimalHour / 24);

        return $JD;
    }
}
