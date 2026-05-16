<?php

function getNavamsaSign($nakIndex,$padaIndex,$SIGNS){
    return $SIGNS[(($nakIndex*4)+$padaIndex)%12];
}

function getNavamsaStart($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL){
    $index = array_search($sign,$SIGNS);

    if(in_array($sign,$MOVABLE)) $offset=0;
    elseif(in_array($sign,$FIXED)) $offset=8;
    else $offset=4;

    return $SIGNS[($index+$offset)%12];
}

function generateNavamsaSequence($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL){

    $start = getNavamsaStart($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL);
    $startIndex = array_search($start,$SIGNS);

    $seq=[];

    for($i=0;$i<9;$i++){
        $seq[]=$SIGNS[($startIndex+$i)%12];
    }

    return $seq;
}

function generatePR81($nakIndex,$padaIndex,$SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL){

    $navSign  = getNavamsaSign($nakIndex,$padaIndex,$SIGNS);
    $mainLord = $SIGN_LORD[$navSign];

    $subSigns = generateNavamsaSequence($navSign,$SIGNS,$MOVABLE,$FIXED,$DUAL);

    $result=[];
    $part=1;

    foreach($subSigns as $subSign){

        $subLord=$SIGN_LORD[$subSign];

        $subSubSigns = generateNavamsaSequence($subSign,$SIGNS,$MOVABLE,$FIXED,$DUAL);

        foreach($subSubSigns as $subSubSign){

            $result[$part]=[
                "main"=>$mainLord,
                "sub"=>$subLord,
                "subsub"=>$SIGN_LORD[$subSubSign]
            ];

            $part++;
        }
    }

    return $result;
}

// OPTIMIZED VERSION - Returns only summary data for tree view
function buildPRDasaTreeSummary($data) {
    
    $planets = $data['planets'];
    $sunLon = (float)$planets['Sun']['decimal'];
    $prLon = $sunLon - 30;
    if ($prLon < 0) $prLon += 360;

    $birthDate = $data['date'] ?? date('Y-m-d');
    $birthTime = $data['time'] ?? "00:00:00";
    $birthDateTime = new DateTime("$birthDate $birthTime");

    $nakSize = 360 / 27;
    $padaSize = $nakSize / 4;
    $partSize = $padaSize / 81;
    $secondsPerYear = 365.2425 * 86400;
    $secondsPerPart = (int) round($partSize * $secondsPerYear);

    $SIGNS = [
        "Aries","Taurus","Gemini","Cancer",
        "Leo","Virgo","Libra","Scorpio",
        "Sagittarius","Capricorn","Aquarius","Pisces"
    ];

    $SIGN_LORD = [
        "Aries"=>"Mars","Taurus"=>"Venus","Gemini"=>"Mercury","Cancer"=>"Moon",
        "Leo"=>"Sun","Virgo"=>"Mercury","Libra"=>"Venus","Scorpio"=>"Mars",
        "Sagittarius"=>"Jupiter","Capricorn"=>"Saturn",
        "Aquarius"=>"Saturn","Pisces"=>"Jupiter"
    ];

    $MOVABLE = ["Aries","Cancer","Libra","Capricorn"];
    $FIXED   = ["Taurus","Leo","Scorpio","Aquarius"];
    $DUAL    = ["Gemini","Virgo","Sagittarius","Pisces"];

    $NAK_NAMES = [
        "Ashwini","Bharani","Krittika","Rohini","Mrigashira",
        "Ardra","Punarvasu","Pushya","Ashlesha",
        "Magha","Purva Phalguni","Uttara Phalguni",
        "Hasta","Chitra","Swati","Vishakha",
        "Anuradha","Jyeshtha","Moola",
        "Purvashadha","Uttarashadha",
        "Shravana","Dhanishta","Shatabhisha",
        "Purvabhadra","Uttarabhadra","Revati"
    ];

    $birthNakIndex = floor($prLon / $nakSize);
    $nakStartLon = $birthNakIndex * $nakSize;
    
    $distanceDegrees = $prLon - $nakStartLon;
    if ($distanceDegrees < 0) $distanceDegrees += 360;
    
    $elapsedSeconds = (int) round($distanceDegrees * $secondsPerYear);
    
    $nakStartTime = clone $birthDateTime;
    $nakStartTime->sub(new DateInterval('PT' . $elapsedSeconds . 'S'));
    
    // Calculate end time (120 years from nakStartTime, matching PRDasa.php)
    $endTime = clone $nakStartTime;
    $endTime->add(new DateInterval('P120Y'));
    
    $currentLon = $nakStartLon;  // Start from Nakshatra start
    $currentTime = clone $nakStartTime;
    
    $tree = [];
    $totalParts = 0;
    $maxParts = 400; // Limit to first 400 parts (about 16 years) for summary
    
    // Track current nakshatra for grouping
    $currentNakIndex = $birthNakIndex;
    $currentNakStartTime = $nakStartTime;
    $currentNakEndTime = null;
    
    while ($currentTime < $endTime && $totalParts < $maxParts) {
        
        $nakIndex = floor($currentLon / $nakSize);
        $nakName = $NAK_NAMES[$nakIndex];
        
        // If we moved to a new nakshatra, calculate its start time
        if ($nakIndex != $currentNakIndex) {
            $currentNakIndex = $nakIndex;
            $currentNakStartTime = clone $currentTime;
            $currentNakEndTime = null;
        }
        
        $nakIndexCalc = floor(round($currentLon, 6) / round($nakSize, 6));
        $longitudeFromNakStart = $currentLon - ($nakIndexCalc * $nakSize);
        if ($longitudeFromNakStart < 0) $longitudeFromNakStart = 0;
        
        $padaIndex = floor(round($longitudeFromNakStart, 6) / round($padaSize, 6)) + 1;
        if ($padaIndex > 4) $padaIndex = 4;
        
        $longitudeFromPadaStart = $longitudeFromNakStart - (($padaIndex - 1) * $padaSize);
        if ($longitudeFromPadaStart < 0) $longitudeFromPadaStart = 0;
        
        $partIndex = floor(round($longitudeFromPadaStart, 6) / round($partSize, 6)) + 1;
        if ($partIndex > 81) $partIndex = 81;
        
        $start = clone $currentTime;
        
        $pr81 = generatePR81($nakIndex, $padaIndex - 1, $SIGNS, $SIGN_LORD, $MOVABLE, $FIXED, $DUAL);
        $lords = $pr81[$partIndex];
        
        $currentTime->add(new DateInterval('PT' . $secondsPerPart . 'S'));
        $end = clone $currentTime;
        
        // Store nakshatra end time
        if (!$currentNakEndTime) {
            $currentNakEndTime = $end;
        } else {
            $currentNakEndTime = $end;
        }
        
        // Store in tree (only summary for tree view)
        if (!isset($tree[$nakName])) {
            $tree[$nakName] = [
                'start' => $currentNakStartTime,
                'end' => $currentNakEndTime,
                'padas' => []
            ];
        }
        
        // Store pada summary (without individual parts for tree view)
        if (!isset($tree[$nakName]['padas'][$padaIndex])) {
            $tree[$nakName]['padas'][$padaIndex] = [
                'start' => $start,
                'end' => $end,
                'part_count' => 1
            ];
        } else {
            $tree[$nakName]['padas'][$padaIndex]['end'] = $end;
            $tree[$nakName]['padas'][$padaIndex]['part_count']++;
        }
        
        $currentLon += $partSize;
        if ($currentLon >= 360) $currentLon -= 360;
        $totalParts++;
    }
    
    return $tree;
}

// Function to get detailed parts for a specific pada (for PDF)
function getPadaDetails($data, $nakName, $padaIndex, $page = 1, $perPage = 20) {
    // This would generate only the specific pada's parts
    // Implement if needed for detailed view
    return [];
}

// Original function - kept for backward compatibility but not used directly in PDF
function buildPRDasaTree($data){
    // Limit to 5 years for initial load to avoid timeout
    return buildPRDasaTreeLimited($data, 5);
}

// Limited version for PDF - generates only first N years
function buildPRDasaTreeLimited($data, $yearsToShow = 5) {
    
    $planets = $data['planets'];
    $sunLon = (float)$planets['Sun']['decimal'];
    $prLon = $sunLon - 30;
    if ($prLon < 0) $prLon += 360;

    $birthDate = $data['date'] ?? date('Y-m-d');
    $birthTime = $data['time'] ?? "00:00:00";
    $birthDateTime = new DateTime("$birthDate $birthTime");

    $nakSize = 360 / 27;
    $padaSize = $nakSize / 4;
    $partSize = $padaSize / 81;
    $secondsPerYear = 365.2425 * 86400;
    $secondsPerPart = (int) round($partSize * $secondsPerYear);

    $SIGNS = [
        "Aries","Taurus","Gemini","Cancer",
        "Leo","Virgo","Libra","Scorpio",
        "Sagittarius","Capricorn","Aquarius","Pisces"
    ];

    $SIGN_LORD = [
        "Aries"=>"Mars","Taurus"=>"Venus","Gemini"=>"Mercury","Cancer"=>"Moon",
        "Leo"=>"Sun","Virgo"=>"Mercury","Libra"=>"Venus","Scorpio"=>"Mars",
        "Sagittarius"=>"Jupiter","Capricorn"=>"Saturn",
        "Aquarius"=>"Saturn","Pisces"=>"Jupiter"
    ];

    $MOVABLE = ["Aries","Cancer","Libra","Capricorn"];
    $FIXED   = ["Taurus","Leo","Scorpio","Aquarius"];
    $DUAL    = ["Gemini","Virgo","Sagittarius","Pisces"];

    $NAK_NAMES = [
        "Ashwini","Bharani","Krittika","Rohini","Mrigashira",
        "Ardra","Punarvasu","Pushya","Ashlesha",
        "Magha","Purva Phalguni","Uttara Phalguni",
        "Hasta","Chitra","Swati","Vishakha",
        "Anuradha","Jyeshtha","Moola",
        "Purvashadha","Uttarashadha",
        "Shravana","Dhanishta","Shatabhisha",
        "Purvabhadra","Uttarabhadra","Revati"
    ];

    $birthNakIndex = floor($prLon / $nakSize);
    $nakStartLon = $birthNakIndex * $nakSize;
    
    $distanceDegrees = $prLon - $nakStartLon;
    if ($distanceDegrees < 0) $distanceDegrees += 360;
    
    $elapsedSeconds = (int) round($distanceDegrees * $secondsPerYear);
    
    $nakStartTime = clone $birthDateTime;
    $nakStartTime->sub(new DateInterval('PT' . $elapsedSeconds . 'S'));
    
    $endTime = clone $nakStartTime;
    $endTime->add(new DateInterval('P120Y'));
    
    $currentLon = $nakStartLon;  // Start from Nakshatra start
    $currentTime = clone $nakStartTime;
    
    $tree = [];
    $totalParts = 0;
    $maxParts = $yearsToShow * 365.2425 * 86400 / $secondsPerPart;

    while ($currentTime < $endTime && $totalParts < $maxParts) {
        
        $nakIndex = floor($currentLon / $nakSize);
        $nakName = $NAK_NAMES[$nakIndex];
        
        $nakIndexCalc = floor(round($currentLon, 6) / round($nakSize, 6));
        $longitudeFromNakStart = $currentLon - ($nakIndexCalc * $nakSize);
        if ($longitudeFromNakStart < 0) $longitudeFromNakStart = 0;
        
        $padaIndex = floor(round($longitudeFromNakStart, 6) / round($padaSize, 6)) + 1;
        if ($padaIndex > 4) $padaIndex = 4;
        
        $longitudeFromPadaStart = $longitudeFromNakStart - (($padaIndex - 1) * $padaSize);
        if ($longitudeFromPadaStart < 0) $longitudeFromPadaStart = 0;
        
        $partIndex = floor(round($longitudeFromPadaStart, 6) / round($partSize, 6)) + 1;
        if ($partIndex > 81) $partIndex = 81;
        
        $start = clone $currentTime;
        
        $pr81 = generatePR81($nakIndex, $padaIndex - 1, $SIGNS, $SIGN_LORD, $MOVABLE, $FIXED, $DUAL);
        $lords = $pr81[$partIndex];
        
        $currentTime->add(new DateInterval('PT' . $secondsPerPart . 'S'));
        $end = clone $currentTime;
        
        if (!isset($tree[$nakName])) {
            $tree[$nakName] = [
                'start' => $start,
                'end' => $end,
                'padas' => []
            ];
        } else {
            $tree[$nakName]['end'] = $end;
        }
        
        if (!isset($tree[$nakName]['padas'][$padaIndex])) {
            $tree[$nakName]['padas'][$padaIndex] = [
                'start' => $start,
                'end' => $end,
                'parts' => []
            ];
        } else {
            $tree[$nakName]['padas'][$padaIndex]['end'] = $end;
        }
        
        $tree[$nakName]['padas'][$padaIndex]['parts'][] = [
            'part' => $partIndex,
            'start' => $start,
            'end' => $end,
            'lords' => $lords
        ];
        
        $currentLon += $partSize;
        if ($currentLon >= 360) $currentLon -= 360;
        $totalParts++;
    }
    
    return $tree;
}

?>