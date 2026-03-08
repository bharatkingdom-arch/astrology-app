<?php

echo "<h2>Swiss Ephemeris Test</h2>";

$swetest = __DIR__ . "/../swisseph/swetest";

/* compile swetest if not exists */
if (!file_exists($swetest)) {
    echo "<b>Compiling Swiss Ephemeris...</b><br>";
    shell_exec("cd ../swisseph && make swetest");
}

/* run swetest */
$cmd = "$swetest -edir ../ephemeris -b1.1.2000 -p0123456789 -fPl";

$output = [];
$return = 0;

exec($cmd . " 2>&1", $output, $return);

echo "<pre>";
print_r($output);
echo "</pre>";

echo "<br>Return code: $return";

?>