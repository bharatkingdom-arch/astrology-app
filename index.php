<?php
echo "<h2>Root files</h2>";
echo "<pre>";
print_r(scandir(__DIR__));
echo "</pre>";

echo "<h2>Swisseph files</h2>";
echo "<pre>";
print_r(scandir(__DIR__ . "/swisseph"));
echo "</pre>";
?>
