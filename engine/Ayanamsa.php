<?php

class Ayanamsa {

    // Lahiri Ayanamsa (Approx Formula)
    public static function lahiri($JD) {

        $T = ($JD - 2451545.0) / 36525;

        // Lahiri base formula
        $ayan = 22.460148 + (1.396042 * $T);

        return $ayan;
    }
}
