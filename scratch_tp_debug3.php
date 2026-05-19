<?php
$swetestPath = "/home/bharat/astrology-app/swisseph/swetest";
$ephePath = "/home/bharat/astrology-app/ephemeris";
$cmd = "$swetestPath -edir$ephePath -b19.08.2024 -ut00:00:00 -p01 -fTtPl -sid1 -n5 -s1";
echo shell_exec($cmd);
