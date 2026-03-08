<?php

echo "<h2>Swiss Ephemeris Test</h2>";

/* DEBUG: show files inside container */
echo "<pre>";
print_r(scandir("/app/ephemeris"));
echo "</pre>";

$cmd = "/app/swisseph/swetest -edir=/app/ephemeris/ -b1.1.2000 -p0123456789 -fPl";

$output = [];
$return = 0;

exec($cmd . " 2>&1", $output, $return);

echo "<pre>";
print_r($output);
echo "</pre>";

echo "<br>Return code: $return";

?>