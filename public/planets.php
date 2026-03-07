<?php

echo "<h2>Swiss Ephemeris Test</h2>";

$cmd = "cd /app/swisseph && make swetest 2>&1";

$output = [];
$return = 0;

exec($cmd, $output, $return);

echo "<pre>";
print_r($output);
echo "</pre>";

echo "Return code: $return";

?>
