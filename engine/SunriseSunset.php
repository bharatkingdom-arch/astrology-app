<?php
/**
 * Sunrise & Sunset Calculator
 * Based on NOAA Solar Calculation
 * Accurate for astrology use
 */

class SunriseSunset
{
    private $zenith = 90.833; // Official zenith (includes refraction)

    public function calculate($date, $latitude, $longitude, $timezone)
    {
        $timestamp = strtotime($date);
        $dayOfYear = date("z", $timestamp) + 1;

        $lngHour = $longitude / 15;

        // Sunrise
        $tRise = $dayOfYear + ((6 - $lngHour) / 24);
        $sunrise = $this->calculateTime($tRise, $latitude, $longitude, $timezone, true);

        // Sunset
        $tSet = $dayOfYear + ((18 - $lngHour) / 24);
        $sunset = $this->calculateTime($tSet, $latitude, $longitude, $timezone, false);

        return [
            "sunrise" => $sunrise,
            "sunset"  => $sunset
        ];
    }

    private function calculateTime($t, $latitude, $longitude, $timezone, $isSunrise)
    {
        // Mean anomaly
        $M = (0.9856 * $t) - 3.289;

        // Sun's true longitude
        $L = $M + (1.916 * sin(deg2rad($M)))
               + (0.020 * sin(2 * deg2rad($M)))
               + 282.634;

        $L = fmod($L, 360);
        if ($L < 0) $L += 360;

        // Right ascension
        $RA = rad2deg(atan(0.91764 * tan(deg2rad($L))));
        $RA = fmod($RA, 360);
        if ($RA < 0) $RA += 360;

        // Adjust quadrant
        $Lquadrant  = floor($L / 90) * 90;
        $RAquadrant = floor($RA / 90) * 90;
        $RA = $RA + ($Lquadrant - $RAquadrant);

        $RA = $RA / 15;

        // Declination
        $sinDec = 0.39782 * sin(deg2rad($L));
        $cosDec = cos(asin($sinDec));

        // Hour angle
        $cosH = (cos(deg2rad($this->zenith)) -
                ($sinDec * sin(deg2rad($latitude))))
                / ($cosDec * cos(deg2rad($latitude)));

        if ($cosH > 1) return "Sun never rises";
        if ($cosH < -1) return "Sun never sets";

        if ($isSunrise) {
            $H = 360 - rad2deg(acos($cosH));
        } else {
            $H = rad2deg(acos($cosH));
        }

        $H = $H / 15;

        // Local mean time
        $T = $H + $RA - (0.06571 * $t) - 6.622;

        // UTC
        $UT = $T - ($longitude / 15);
        $UT = fmod($UT, 24);
        if ($UT < 0) $UT += 24;

        // Local time
        $localTime = $UT + $timezone;

        // Format
        return gmdate("H:i:s", (int) round($localTime * 3600));
    }
}
