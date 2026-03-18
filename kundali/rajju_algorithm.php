<?php

function getRajju($nak){

    $aroha = [
        "Siro" => ["Ashwini","Magha","Moola"],
        "Kati" => ["Bharani","Purva Phalguni","Purva Ashadha"],
        "Nabhi" => ["Krittika","Uttara Phalguni","Uttara Ashadha"],
        "Kanta" => ["Rohini","Hasta","Shravana"],
        "Pada" => ["Mrigashira","Chitra","Dhanishta"]
    ];

    $avaroha = [
        "Kanta" => ["Ardra","Swati","Shatabhisha"],
        "Nabhi" => ["Punarvasu","Vishakha","Purva Bhadrapada"],
        "Kati" => ["Pushya","Anuradha","Uttara Bhadrapada"],
        "Pada" => ["Ashlesha","Jyeshtha","Revati"]
    ];

    foreach($aroha as $type=>$list){
        if(in_array($nak,$list)) return [$type,"Aroha"];
    }

    foreach($avaroha as $type=>$list){
        if(in_array($nak,$list)) return [$type,"Avaroha"];
    }

    return ["Unknown",""];
}


function rajjuScore($boy_nak, $girl_nak){

    list($b_type,$b_dir) = getRajju($boy_nak);
    list($g_type,$g_dir) = getRajju($girl_nak);

    // Different Rajju in Aroha
    if($b_dir=="Aroha" && $g_dir=="Aroha" && $b_type != $g_type){
        return [4,"Good"];
    }

    // One Aroha, one Avaroha
    if($b_dir != $g_dir){
        return [3,"Acceptable"];
    }

    // Same Rajju different direction
    if($b_type == $g_type && $b_dir != $g_dir){
        return [2,"Average"];
    }

    // Same Rajju same direction
    if($b_type == $g_type){
        return [0,"Rajju Dosha"];
    }

    return [1,"Low"];
}