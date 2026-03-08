<?php

echo "<h2>Swiss Ephemeris Planet Positions</h2>";

$swisseph = "/app/swisseph";
$swetest  = "/app/swisseph/swetest";
$ephe     = "/app/ephemeris";

/* ------------------------------------------------
   STEP 1 : Always compile swetest on Railway
------------------------------------------------ */

$compile = "cd $swisseph && make clean && make swetest 2>&1";

$compile_output = [];
exec($compile, $compile_output);

echo "<h3>Compile Output</h3>";
echo "<pre>";
print_r($compile_output);
echo "</pre>";

/* ------------------------------------------------
   STEP 2 : Run swetest
------------------------------------------------ */

$date = "1.1.2024";
$time = "0:0";

$cmd = "$swetest -edir$ephe -p0123456789 -eswe -b$date -ut$time 2>&1";

$output = [];
$return = 0;

exec($cmd, $output, $return);

/* ------------------------------------------------
   STEP 3 : Parse planets
------------------------------------------------ */

$planets = [];

foreach ($output as $line) {

    if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|Uranus|Neptune|Pluto)/', $line)) {

        $parts = preg_split('/\s+/', trim($line));

        $planet = $parts[0];
        $longitude = $parts[1];

        $planets[$planet] = $longitude;
    }
}

/* ------------------------------------------------
   STEP 4 : Display table
------------------------------------------------ */

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Planet</th><th>Longitude</th></tr>";

foreach ($planets as $planet => $lon) {

    echo "<tr>";
    echo "<td>$planet</td>";
    echo "<td>$lon</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Raw Output</h3>";

echo "<pre>";
print_r($output);
echo "</pre>";

echo "Return code: $return";

?>
