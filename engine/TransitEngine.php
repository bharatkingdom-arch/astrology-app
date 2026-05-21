<?php
class TransitEngine
{
    public static function getDailyHoroscope($userSignName, $lang = 'en')
    {
        $userSignName = strtolower(trim($userSignName));
        $signs = ['mesha', 'vrishabha', 'mithuna', 'karka', 'simha', 'kanya', 'tula', 'vrischika', 'dhanu', 'makara', 'kumbha', 'meena'];
        $userSignIndex = array_search($userSignName, $signs);
        
        if ($userSignIndex === false) {
            $userSignIndex = 0; // Default to Mesha
        }

        // Get today's UTC Date and Time
        $dt = new DateTime("now", new DateTimeZone("UTC"));
        $utDate = $dt->format("d.m.Y");
        $utTime = "00:00"; // Daily transit considered at start of UTC day

        // Use Swiss Ephemeris
        $swetestPath = "/app/swisseph/swetest";
        $ephePath    = "/app/ephemeris";

        if (!file_exists($swetestPath)) {
            $swetestPath = __DIR__ . '/../../swisseph/swetest';
            $ephePath = __DIR__ . '/../../ephemeris';
        }

        // -p0 (Sun), -p1 (Moon), -p4 (Mars), -p5 (Jupiter)
        $command = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -p0145 -fPl";
        $output = shell_exec($command);

        $planets = [];
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^(Sun|Moon|Mars|Jupiter)\s+([\d\.]+)/', $line, $matches)) {
                    $name = $matches[1];
                    $lon = floatval($matches[2]);
                    $signIndex = floor($lon / 30);
                    $planets[$name] = $signIndex;
                }
            }
        }

        // Load rules
        $rules = require __DIR__ . '/transit_rules.php';

        $predictions = [
            'Personal' => '',
            'Profession' => '',
            'Health' => '',
            'Luck' => ''
        ];

        // Map planets to aspects
        $mapping = [
            'Moon' => 'Personal',
            'Sun' => 'Profession',
            'Mars' => 'Health',
            'Jupiter' => 'Luck'
        ];

        foreach ($mapping as $planet => $aspect) {
            if (isset($planets[$planet])) {
                $transitSign = $planets[$planet];
                // Calculate House (1 to 12)
                $house = ($transitSign - $userSignIndex + 12) % 12 + 1;
                
                // Fetch rule
                if (isset($rules[$planet][$house][$lang])) {
                    $predictions[$aspect] = $rules[$planet][$house][$lang];
                } else {
                    $predictions[$aspect] = $rules[$planet][1][$lang]; // Fallback
                }
            } else {
                // Fallback if swetest fails
                $predictions[$aspect] = ($lang === 'te') ? "ఈ రోజు మీ గ్రహ స్థానాలు సానుకూలంగా ఉన్నాయి." : "Your planetary alignments are favorable today.";
            }
        }

        return $predictions;
    }
}
