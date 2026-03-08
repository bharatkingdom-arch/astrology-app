<?php

class PlanetEngine
{

    public static function getPlanets($date, $time)
    {

        $swetest = "/app/swisseph/swetest";
        $ephe    = "/app/ephemeris";

        $cmd = "$swetest -edir$ephe -p0123456789 -eswe -b$date -ut$time 2>&1";

        $output = [];
        exec($cmd, $output);

        $planets = [];

        foreach ($output as $line) {

            if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto)/', $line)) {

                $parts = preg_split('/\s+/', trim($line));

                $planet = $parts[0];
                $longitude = $parts[1];

                $decimal = floatval(str_replace("°", "", $longitude));

                $planets[$planet] = [
                    "longitude" => $longitude,
                    "decimal" => $decimal
                ];
            }
        }

        return $planets;
    }

}