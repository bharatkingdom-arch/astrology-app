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

        // -p0 (Sun), -p1 (Moon), -p2 (Mercury), -p3 (Venus), -p4 (Mars), -p5 (Jupiter), -p6 (Saturn), -pt (True Node/Rahu)
        $command = "$swetestPath -edir$ephePath -sid1 -b$utDate -ut$utTime -p0123456t -fPl";
        $output = shell_exec($command);

        $planets = [];
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                $line = trim($line);
                if (preg_match('/^(Sun|Moon|Mercury|Venus|Mars|Jupiter|Saturn|true Node|True Node)\s+([\d\.]+)/i', $line, $matches)) {
                    $name = ucfirst(strtolower($matches[1]));
                    if ($name === 'True node') $name = 'Rahu';
                    
                    $lon = floatval($matches[2]);
                    $signIndex = floor($lon / 30);
                    $planets[$name] = $signIndex;
                    
                    if ($name === 'Rahu') {
                        $ketuLon = fmod($lon + 180, 360);
                        if ($ketuLon < 0) $ketuLon += 360;
                        $planets['Ketu'] = floor($ketuLon / 30);
                    }
                }
            }
        }

        // Load rules
        $rules = require __DIR__ . '/transit_rules.php';

        $predictions = [];

        // Map planets to aspects
        $mapping = [
            'Moon' => 'Personal',
            'Sun' => 'Profession',
            'Mars' => 'Health',
            'Jupiter' => 'Luck',
            'Venus' => 'Romance',
            'Mercury' => 'Intellect',
            'Saturn' => 'Karma',
            'Rahu' => 'Ambition',
            'Ketu' => 'Spirituality'
        ];

        $aspectNames = [
            'Personal' => ['en' => 'Personal', 'te' => 'వ్యక్తిగత జీవితం'],
            'Profession' => ['en' => 'Profession', 'te' => 'వృత్తి'],
            'Health' => ['en' => 'Health', 'te' => 'ఆరోగ్యం'],
            'Luck' => ['en' => 'Luck', 'te' => 'అదృష్టం'],
            'Romance' => ['en' => 'Romance', 'te' => 'ప్రేమ & శృంగారం'],
            'Intellect' => ['en' => 'Intellect', 'te' => 'జ్ఞానం & విద్య'],
            'Karma' => ['en' => 'Karma', 'te' => 'కర్మ & శ్రమ'],
            'Ambition' => ['en' => 'Ambition', 'te' => 'లక్ష్యం & కోరికలు'],
            'Spirituality' => ['en' => 'Spirituality', 'te' => 'ఆధ్యాత్మికత']
        ];

        foreach ($mapping as $planet => $aspectKey) {
            $translatedCategory = $aspectNames[$aspectKey][$lang] ?? $aspectNames[$aspectKey]['en'];
            
            if (isset($planets[$planet])) {
                $transitSign = $planets[$planet];
                // Calculate House (1 to 12)
                $house = ($transitSign - $userSignIndex + 12) % 12 + 1;
                
                // Fetch rule
                if (isset($rules[$planet][$house][$lang])) {
                    $predictions[$translatedCategory] = $rules[$planet][$house][$lang];
                } else {
                    $predictions[$translatedCategory] = $rules[$planet][1][$lang]; // Fallback
                }
            } else {
                // Fallback if swetest fails
                $predictions[$translatedCategory] = ($lang === 'te') ? "ఈ రోజు మీ గ్రహ స్థానాలు సానుకూలంగా ఉన్నాయి." : "Your planetary alignments are favorable today.";
            }
        }

        return $predictions;
    }
}
