<?php

echo "<h2>Swiss Ephemeris Planet Positions</h2>";

/*
-----------------------------------------
CONFIG
-----------------------------------------
*/

$swetest = "/app/swisseph/swetest";
$ephe    = "/app/ephemeris";

/*
-----------------------------------------
COMPILE SWETEST IF NOT EXISTS
-----------------------------------------
*/

if (!file_exists($swetest)) {

    echo "<b>Compiling Swiss Ephemeris...</b><br>";

    $compile = "cd /app/swisseph && make clean && make swetest 2>&1";
    exec($compile, $compile_output);

    echo "<pre>";
    print_r($compile_output);
    echo "</pre>";
}

/*
-----------------------------------------
DATE INPUT
-----------------------------------------
*/

$date = "1.1.2024";
$time = "0:0";

/*
-----------------------------------------
RUN SWETEST
-----------------------------------------
*/

$cmd = "$swetest -edir$ephe -p0123456789 -eswe -b$date -ut$time 2>&1";

$output = [];
$return = 0;

exec($cmd, $output, $return);

/*
-----------------------------------------
PARSE PLANET DATA
-----------------------------------------
*/

$planets = [];

foreach ($output as $line) {

    if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto)/', $line)) {

        $parts = preg_split('/\s+/', trim($line));

        $planet = $parts[0];
        $longitude = $parts[1];

        $planets[$planet] = $longitude;
    }
}

/*
-----------------------------------------
DISPLAY RESULT
-----------------------------------------
*/

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Planet</th><th>Longitude</th></tr>";

foreach ($planets as $planet => $lon) {

    echo "<tr>";
    echo "<td>$planet</td>";
    echo "<td>$lon</td>";
    echo "</tr>";
}

echo "</table>";

/*
-----------------------------------------
DEBUG OUTPUT
-----------------------------------------
*/

echo "<br><h3>Raw Output</h3>";

echo "<pre>";
print_r($output);
echo "</pre>";

echo "Return code: $return";

?>
