<?php
$nakSize  = 360 / 27;
$padaSize = $nakSize / 4;
$partSize = $padaSize / 81;

$currentLon = 120; // Magha

for ($i=0; $i<5; $i++) {
    $nakIndex = floor(round($currentLon, 6) / round($nakSize, 6));
    $longitudeFromNakStart = $currentLon - ($nakIndex * $nakSize);
    if ($longitudeFromNakStart < 0) $longitudeFromNakStart = 0;
    
    $padaIndex = floor(round($longitudeFromNakStart, 6) / round($padaSize, 6)) + 1;
    if ($padaIndex > 4) $padaIndex = 4;
    
    $longitudeFromPadaStart = $longitudeFromNakStart - (($padaIndex - 1) * $padaSize);
    if ($longitudeFromPadaStart < 0) $longitudeFromPadaStart = 0;
    
    $partIndex = floor(round($longitudeFromPadaStart, 6) / round($partSize, 6)) + 1;
    if ($partIndex > 81) $partIndex = 81;

    echo "Lon: $currentLon, Pada: $padaIndex, Part: $partIndex\n";
    $currentLon += $partSize;
}
