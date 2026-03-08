<?php

class Navamsha {

    // Movable signs
    private static $movable = [1,4,7,10];

    // Fixed signs
    private static $fixed = [2,5,8,11];

    // Dual signs
    private static $dual = [3,6,9,12];

    public static function calculate($longitude) {

        // Step 1: D1 Rasi
        $rasi = floor($longitude / 30) + 1;

        // Degree inside sign
        $degInSign = fmod($longitude, 30);

        // Step 2: Navamsa division (0-8)
        $navDivision = floor($degInSign / (30/9));

        // Step 3: Determine starting sign
        if (in_array($rasi, self::$movable)) {
            $start = $rasi;
        }
        elseif (in_array($rasi, self::$fixed)) {
            $start = ($rasi + 8) % 12;
            if ($start == 0) $start = 12;
        }
        else { // dual
            $start = ($rasi + 4) % 12;
            if ($start == 0) $start = 12;
        }

        // Step 4: Final Navamsa sign
        $navRasi = ($start + $navDivision) % 12;
        if ($navRasi == 0) $navRasi = 12;

        return $navRasi;
    }
}
