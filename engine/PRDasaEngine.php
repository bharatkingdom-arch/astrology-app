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

function buildPRDasaTree($data){

    $planets = $data['planets'];

    $sunLon = (float)$planets['Sun']['decimal'];

    $prLon = $sunLon - 30;
    if ($prLon < 0) $prLon += 360;

    $birthDate = $data['date'] ?? date('Y-m-d');
    $birthTime = $data['time'] ?? "00:00:00";

    $birthDateTime = new DateTime("$birthDate $birthTime");

    $nakSize  = 360 / 27;
    $padaSize = $nakSize / 4;
    $partSize = $padaSize / 81;

    // Use high precision with float, don't round too early
    $secondsPerYear = 365.2425 * 86400;
    $degreesPerSecond = 360 / $secondsPerYear;  // Degrees per second
    $secondsPerPart = $partSize / $degreesPerSecond;  // Exact seconds per part

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

    // Find which nakshatra the PR planet is in
    $birthNakIndex = floor($prLon / $nakSize);
    $nakStartLon = $birthNakIndex * $nakSize;
    
    // Calculate time elapsed from nakshatra start to birth using longitude
    $longitudeFromStart = $prLon - $nakStartLon;
    $elapsedSeconds = $longitudeFromStart / $degreesPerSecond;
    
    // Calculate exact nakshatra start time by going backwards from birth
    $nakStartTime = clone $birthDateTime;
    
    // Subtract integer seconds
    $intSeconds = (int)$elapsedSeconds;
    $nakStartTime->sub(new DateInterval('PT' . $intSeconds . 'S'));
    
    // Subtract fractional seconds using microseconds for precision
    $fractionalSeconds = $elapsedSeconds - $intSeconds;
    if ($fractionalSeconds > 0) {
        $microseconds = (int)round($fractionalSeconds * 1000000);
        $nakStartTime->modify("-$microseconds microseconds");
    }
    
    // Always start from NAKSHATRA BEGINNING (Part 1, not birth position)
    $endTime = clone $nakStartTime;
    $endTime->add(new DateInterval('P120Y'));
    
    $currentLon = $nakStartLon;  // Start from nakshatra beginning
    $currentTime = clone $nakStartTime;
    
    $tree = [];
    $totalParts = 0;
    $maxParts = 120 * 365.2425 * 86400 / $secondsPerPart;  // Total parts in 120 years

    while ($currentTime < $endTime && $totalParts < $maxParts) {
        
        $nakIndex = floor($currentLon / $nakSize);
        $nakName = $NAK_NAMES[$nakIndex];
        
        $withinNak = fmod($currentLon, $nakSize);
        $padaIndex = floor($withinNak / $padaSize) + 1;
        $withinPada = fmod($withinNak, $padaSize);
        $partIndex = floor($withinPada / $partSize) + 1;
        
        $start = clone $currentTime;
        
        $pr81 = generatePR81($nakIndex, $padaIndex - 1, $SIGNS, $SIGN_LORD, $MOVABLE, $FIXED, $DUAL);
        $lords = $pr81[$partIndex];
        
        // Calculate next time using exact seconds per part
        $currentTime->modify("+" . number_format($secondsPerPart, 6) . " seconds");
        $end = clone $currentTime;
        
        // Initialize nakshatra in tree
        if (!isset($tree[$nakName])) {
            $tree[$nakName] = [
                'start' => $start,
                'end' => $end,
                'padas' => []
            ];
        } else {
            $tree[$nakName]['end'] = $end;
        }
        
        // Initialize pada in tree
        if (!isset($tree[$nakName]['padas'][$padaIndex])) {
            $tree[$nakName]['padas'][$padaIndex] = [
                'start' => $start,
                'end' => $end,
                'parts' => []
            ];
        } else {
            $tree[$nakName]['padas'][$padaIndex]['end'] = $end;
        }
        
        // Add part
        $tree[$nakName]['padas'][$padaIndex]['parts'][] = [
            'part' => $partIndex,
            'start' => $start,
            'end' => $end,
            'lords' => $lords
        ];
        
        // Move to next part
        $currentLon += $partSize;
        if ($currentLon >= 360) $currentLon -= 360;
        $totalParts++;
    }
    
    return $tree;
}

?>