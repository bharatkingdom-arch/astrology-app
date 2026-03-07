<?php

echo "<h2>Swiss Ephemeris Test</h2>";

$cmd = "/app/swisseph/swetest -p0123456789 -eswe -b1.1.2024 -ut0:0 2>&1";

$output = [];
$return = 0;

exec($cmd, $output, $return);

echo "<pre>";
print_r($output);
echo "</pre>";

echo "Return code: $return";

?>
