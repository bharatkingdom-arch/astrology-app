<?php

echo "<h2>Swiss Ephemeris Test</h2>";

$compile = "cd /app/swisseph && make clean && make swetest 2>&1";
exec($compile, $compile_output);

echo "<h3>Compile output</h3>";
echo "<pre>";
print_r($compile_output);
echo "</pre>";

$cmd = "/app/swisseph/swetest -p0123456789 -eswe -b1.1.2024 -ut0:0 2>&1";

$output = [];
$return = 0;

exec($cmd, $output, $return);

echo "<h3>Swetest output</h3>";
echo "<pre>";
print_r($output);
echo "</pre>";

echo "Return code: $return";

?>
