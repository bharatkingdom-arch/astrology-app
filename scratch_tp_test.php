<?php
$swetestPath = "/home/bharat/astrology-app/swisseph/swetest";
$ephePath = "/home/bharat/astrology-app/ephemeris";

// Test if swetest works
$cmd = "$swetestPath -edir$ephePath -b01.01.2024 -ut12:00 -p01 -fPls -sid1";
echo shell_exec($cmd);
