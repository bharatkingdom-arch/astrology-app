<?php
$swetestPath = "/home/bharat/astrology-app/swisseph/swetest";
$ephePath = "/home/bharat/astrology-app/ephemeris";
$cmd = "$swetestPath -edir$ephePath -b01.01.2024 -ut12:00 -p01 -fTPls -sid1 -n5 -s1";
echo shell_exec($cmd);
