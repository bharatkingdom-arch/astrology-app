<?php

class TithiPraveshaEngine
{
    /**
     * Calculate Tithi Pravesha dates for a range of years.
     * 
     * @param string $birthDate "DD.MM.YYYY"
     * @param string $birthTime "HH:MM:SS" (Local Time)
     * @param float $timezone Offset in hours
     * @param float $S_b Natal Sun longitude
     * @param float $M_b Natal Moon longitude
     * @param int $startYear The year to start from
     * @param int $numYears Number of years to calculate
     * @return array Array of associative arrays with year, date, time (Local), and weekday
     */
    public static function calculateForYears($birthDate, $birthTime, $timezone, $S_b, $M_b, $startYear, $numYears = 10)
    {
        $swetestPath = __DIR__ . '/../swisseph/swetest';
        $ephePath    = __DIR__ . '/../ephemeris';

        // Fallback for docker paths if local fails
        if (!file_exists($swetestPath)) {
            $swetestPath = '/app/swisseph/swetest';
            $ephePath = '/app/ephemeris';
        }

        // 1. Compute Natal Tithi Angle and Sun Sign
        $T_b = fmod($M_b - $S_b + 360, 360);
        $SunSign_b = floor($S_b / 30);

        // Calculate birth month and day for approximations
        $bDateParts = explode('.', $birthDate);
        $bDay = (int)$bDateParts[0];
        $bMonth = (int)$bDateParts[1];

        $results = [];

        for ($year = $startYear; $year <= $startYear + $numYears; $year++) {
            
            // Start scanning 30 days before the Gregorian birthday
            $dtStart = new DateTime("$year-$bMonth-$bDay 00:00:00", new DateTimeZone('UTC'));
            $dtStart->modify('-30 days');
            $utDate = $dtStart->format('d.m.Y');
            $utTime = '00:00:00';

            // Coarse scan: 60 days
            $cmd = "$swetestPath -edir$ephePath -b$utDate -ut$utTime -p01 -fTPl -sid1 -n60 -s1";
            $output = shell_exec($cmd);

            $lines = explode("\n", trim($output));
            
            $daysData = [];
            $currentDate = '';
            $sun = 0;
            $moon = 0;

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, 'UT:') === 0 || strpos($line, 'TT:') === 0 || strpos($line, 'Epsilon') === 0 || strpos($line, 'version') !== false) {
                    continue;
                }
                
                // Example line: 19.08.2024 Sun              122.3135886
                if (preg_match('/^(\d{2}\.\d{2}\.\d{4})\s+(Sun|Moon)\s+([\d\.]+)/', $line, $matches)) {
                    $d = $matches[1];
                    $planet = $matches[2];
                    $long = floatval($matches[3]);

                    $datetimeKey = "$d";
                    if (!isset($daysData[$datetimeKey])) {
                        $daysData[$datetimeKey] = [];
                    }
                    $daysData[$datetimeKey][$planet] = $long;
                }
            }

            // Find the 24-hour window where T_t crosses T_b and Sun is in SunSign_b
            $targetDateStr = null;
            $targetTithiDiff = null;

            $prevTithi = null;
            $prevDate = null;
            $prevSunSign = null;

            foreach ($daysData as $dtKey => $planets) {
                if (!isset($planets['Sun']) || !isset($planets['Moon'])) continue;

                $S_t = $planets['Sun'];
                $M_t = $planets['Moon'];
                
                $T_t = fmod($M_t - $S_t + 360, 360);
                $SunSign_t = floor($S_t / 30);

                if ($prevTithi !== null) {
                    // Check if Tithi crossed T_b.
                    // Since T_t increases by ~12 deg/day, if prevTithi < T_b and T_t >= T_b
                    // Also handle wrap-around (e.g., prevTithi = 350, T_b = 5, T_t = 10)
                    $crossed = false;
                    
                    if ($prevTithi <= $T_b && $T_t >= $T_b && ($T_t - $prevTithi) < 30) {
                        $crossed = true;
                    } elseif ($prevTithi > 330 && $T_t < 30) {
                        // wrap around occurred
                        if ($T_b >= $prevTithi || $T_b <= $T_t) {
                            $crossed = true;
                        }
                    }

                    if ($crossed) {
                        // Check if Sun is in the correct sign in this window
                        if ($SunSign_t == $SunSign_b || $prevSunSign == $SunSign_b) {
                            $targetDateStr = $prevDate;
                            break;
                        }
                    }
                }

                $prevTithi = $T_t;
                $prevDate = $dtKey;
                $prevSunSign = $SunSign_t;
            }

            if ($targetDateStr) {
                // We have a 24-hour window starting at $targetDateStr.
                // Let's do a binary search to find the exact UTC second.
                $startTs = DateTime::createFromFormat('d.m.Y H:i:s', "$targetDateStr 00:00:00", new DateTimeZone('UTC'))->getTimestamp();
                $endTs = $startTs + 86400; // 24 hours later

                $exactTs = self::binarySearchTithi($startTs, $endTs, $T_b, $swetestPath, $ephePath);

                if ($exactTs) {
                    // Convert UTC exact timestamp to Local Time
                    $dtExact = new DateTime("@$exactTs");
                    // Apply timezone offset
                    $hours = floor(abs($timezone));
                    $minutes = abs($timezone - floor($timezone)) * 60;
                    
                    if ($timezone >= 0) {
                        $dtExact->modify("+{$hours} hours");
                        $dtExact->modify("+{$minutes} minutes");
                    } else {
                        $dtExact->modify("-{$hours} hours");
                        $dtExact->modify("-{$minutes} minutes");
                    }

                    $results[] = [
                        'year' => $year,
                        'date' => $dtExact->format('d-m-Y'),
                        'time' => $dtExact->format('h:i:s A'),
                        'weekday' => $dtExact->format('l')
                    ];
                }
            }
        }

        return $results;
    }

    private static function binarySearchTithi($startTs, $endTs, $T_b, $swetestPath, $ephePath)
    {
        $iterations = 0;
        $maxIterations = 20; // 20 iterations gives ~0.08 seconds precision for a 24h window
        $matchTs = $startTs;

        while ($startTs <= $endTs && $iterations < $maxIterations) {
            $midTs = (int)(($startTs + $endTs) / 2);
            
            $dt = new DateTime("@$midTs");
            $utDate = $dt->format('d.m.Y');
            $utTime = $dt->format('H:i:s');

            $cmd = "$swetestPath -edir$ephePath -b$utDate -ut$utTime -p01 -fPl -sid1";
            $output = shell_exec($cmd);
            $lines = explode("\n", trim($output));
            
            $S_t = 0;
            $M_t = 0;
            foreach ($lines as $line) {
                if (strpos($line, 'Sun') !== false) {
                    $parts = preg_split('/\s+/', trim($line));
                    $S_t = floatval($parts[1]);
                }
                if (strpos($line, 'Moon') !== false) {
                    $parts = preg_split('/\s+/', trim($line));
                    $M_t = floatval($parts[1]);
                }
            }

            $T_t = fmod($M_t - $S_t + 360, 360);

            // Calculate angular distance between T_t and T_b
            $diff = $T_t - $T_b;
            
            // Normalize diff to -180 to 180
            if ($diff > 180) $diff -= 360;
            if ($diff < -180) $diff += 360;

            if (abs($diff) < 0.0001) { // Very close match
                $matchTs = $midTs;
                break;
            }

            if ($diff > 0) {
                // T_t is ahead of T_b, meaning we need an earlier time
                $endTs = $midTs - 1;
            } else {
                // T_t is behind T_b, meaning we need a later time
                $startTs = $midTs + 1;
            }
            $matchTs = $midTs;
            $iterations++;
        }

        return $matchTs;
    }
}
