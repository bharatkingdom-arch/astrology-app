<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================= DATA =================
$nakshatra_data = [
"Ashwini"=>["rasi"=>"Mesha","lord"=>"Mars","vashya"=>"Chatushpada","yoni"=>"Horse","gana"=>"Deva","nadi"=>"Adi"],
"Bharani"=>["rasi"=>"Mesha","lord"=>"Venus","vashya"=>"Chatushpada","yoni"=>"Elephant","gana"=>"Manushya","nadi"=>"Madhya"],
"Krittika"=>["rasi"=>"Mesha","lord"=>"Sun","vashya"=>"Chatushpada","yoni"=>"Goat","gana"=>"Rakshasa","nadi"=>"Antya"],
"Rohini"=>["rasi"=>"Vrishabha","lord"=>"Moon","vashya"=>"Chatushpada","yoni"=>"Serpent","gana"=>"Manushya","nadi"=>"Adi"],
"Mrigashira"=>["rasi"=>"Vrishabha","lord"=>"Mars","vashya"=>"Chatushpada","yoni"=>"Serpent","gana"=>"Deva","nadi"=>"Madhya"],
"Ardra"=>["rasi"=>"Mithuna","lord"=>"Rahu","vashya"=>"Manava","yoni"=>"Dog","gana"=>"Manushya","nadi"=>"Antya"],
"Punarvasu"=>["rasi"=>"Mithuna","lord"=>"Jupiter","vashya"=>"Manava","yoni"=>"Cat","gana"=>"Deva","nadi"=>"Adi"],
"Pushya"=>["rasi"=>"Karka","lord"=>"Saturn","vashya"=>"Jalachara","yoni"=>"Sheep","gana"=>"Deva","nadi"=>"Madhya"],
"Ashlesha"=>["rasi"=>"Karka","lord"=>"Mercury","vashya"=>"Jalachara","yoni"=>"Cat","gana"=>"Rakshasa","nadi"=>"Antya"],
"Magha"=>["rasi"=>"Simha","lord"=>"Ketu","vashya"=>"Vanachara","yoni"=>"Rat","gana"=>"Rakshasa","nadi"=>"Adi"],
"Purva Phalguni"=>["rasi"=>"Simha","lord"=>"Venus","vashya"=>"Vanachara","yoni"=>"Rat","gana"=>"Manushya","nadi"=>"Madhya"],
"Uttara Phalguni"=>["rasi"=>"Simha","lord"=>"Sun","vashya"=>"Manava","yoni"=>"Cow","gana"=>"Manushya","nadi"=>"Antya"],
"Hasta"=>["rasi"=>"Kanya","lord"=>"Moon","vashya"=>"Manava","yoni"=>"Buffalo","gana"=>"Deva","nadi"=>"Adi"],
"Chitra"=>["rasi"=>"Kanya","lord"=>"Mars","vashya"=>"Manava","yoni"=>"Tiger","gana"=>"Rakshasa","nadi"=>"Madhya"],
"Swati"=>["rasi"=>"Tula","lord"=>"Rahu","vashya"=>"Manava","yoni"=>"Buffalo","gana"=>"Deva","nadi"=>"Antya"],
"Vishakha"=>["rasi"=>"Tula","lord"=>"Jupiter","vashya"=>"Manava","yoni"=>"Tiger","gana"=>"Rakshasa","nadi"=>"Adi"],
"Anuradha"=>["rasi"=>"Vrischika","lord"=>"Saturn","vashya"=>"Jalachara","yoni"=>"Deer","gana"=>"Deva","nadi"=>"Madhya"],
"Jyeshtha"=>["rasi"=>"Vrischika","lord"=>"Mercury","vashya"=>"Jalachara","yoni"=>"Deer","gana"=>"Rakshasa","nadi"=>"Antya"],
"Moola"=>["rasi"=>"Dhanu","lord"=>"Ketu","vashya"=>"Vanachara","yoni"=>"Dog","gana"=>"Rakshasa","nadi"=>"Adi"],
"Purva Ashadha"=>["rasi"=>"Dhanu","lord"=>"Venus","vashya"=>"Vanachara","yoni"=>"Monkey","gana"=>"Manushya","nadi"=>"Madhya"],
"Uttara Ashadha"=>["rasi"=>"Makara","lord"=>"Sun","vashya"=>"Chatushpada","yoni"=>"Mongoose","gana"=>"Manushya","nadi"=>"Antya"],
"Shravana"=>["rasi"=>"Makara","lord"=>"Moon","vashya"=>"Chatushpada","yoni"=>"Monkey","gana"=>"Deva","nadi"=>"Adi"],
"Dhanishta"=>["rasi"=>"Makara","lord"=>"Mars","vashya"=>"Chatushpada","yoni"=>"Lion","gana"=>"Rakshasa","nadi"=>"Madhya"],
"Shatabhisha"=>["rasi"=>"Kumbha","lord"=>"Rahu","vashya"=>"Manava","yoni"=>"Horse","gana"=>"Rakshasa","nadi"=>"Antya"],
"Purva Bhadrapada"=>["rasi"=>"Kumbha","lord"=>"Jupiter","vashya"=>"Manava","yoni"=>"Lion","gana"=>"Manushya","nadi"=>"Adi"],
"Uttara Bhadrapada"=>["rasi"=>"Meena","lord"=>"Saturn","vashya"=>"Jalachara","yoni"=>"Cow","gana"=>"Manushya","nadi"=>"Madhya"],
"Revati"=>["rasi"=>"Meena","lord"=>"Mercury","vashya"=>"Jalachara","yoni"=>"Elephant","gana"=>"Deva","nadi"=>"Antya"]
];

function getRasiFromNakshatra($nak,$pada){

    switch($nak){

        case "Krittika": return ($pada==1)?"Mesha":"Vrishabha";
        case "Mrigashira": return ($pada<=2)?"Vrishabha":"Mithuna";
        case "Punarvasu": return ($pada<=3)?"Mithuna":"Karka";
        case "Uttara Phalguni": return ($pada==1)?"Simha":"Kanya";
        case "Chitra": return ($pada<=2)?"Kanya":"Tula";
        case "Vishakha": return ($pada<=3)?"Tula":"Vrischika";
        case "Uttara Ashadha": return ($pada==1)?"Dhanu":"Makara";
        case "Dhanishta": return ($pada<=2)?"Makara":"Kumbha";
        case "Purva Bhadrapada": return ($pada<=3)?"Kumbha":"Meena";

        default:
            global $nakshatra_data;
            return $nakshatra_data[$nak]['rasi'];
    }
}

$boy = $boy ?? '';
$girl = $girl ?? '';
$boy_pada = $boy_pada ?? 1;
$girl_pada = $girl_pada ?? 1;

// ================= SUPPORT =================
$rasi_map = ["Mesha"=>1,"Vrishabha"=>2,"Mithuna"=>3,"Karka"=>4,"Simha"=>5,"Kanya"=>6,"Tula"=>7,"Vrischika"=>8,"Dhanu"=>9,"Makara"=>10,"Kumbha"=>11,"Meena"=>12];

$rasi_lord = [
"Mesha"=>"Mars",
"Vrishabha"=>"Venus",
"Mithuna"=>"Mercury",
"Karka"=>"Moon",
"Simha"=>"Sun",
"Kanya"=>"Mercury",
"Tula"=>"Venus",
"Vrischika"=>"Mars",
"Dhanu"=>"Jupiter",
"Makara"=>"Saturn",
"Kumbha"=>"Saturn",
"Meena"=>"Jupiter"
];

$rasi_varna=["Mesha"=>"Kshatriya","Simha"=>"Kshatriya","Dhanu"=>"Kshatriya","Karka"=>"Brahmin","Vrischika"=>"Brahmin","Meena"=>"Brahmin","Vrishabha"=>"Vaishya","Kanya"=>"Vaishya","Makara"=>"Vaishya","Mithuna"=>"Shudra","Tula"=>"Shudra","Kumbha"=>"Shudra"];
$rank=["Brahmin"=>4,"Kshatriya"=>3,"Vaishya"=>2,"Shudra"=>1];

$rasi_vashya = [
"Mesha"=>"Chatushpada",
"Vrishabha"=>"Chatushpada",
"Mithuna"=>"Manava",
"Karka"=>"Jalachara",
"Simha"=>"Vanachara",
"Kanya"=>"Manava",
"Tula"=>"Manava",
"Vrischika"=>"Jalachara",
"Dhanu"=>"Vanachara",
"Makara"=>"Chatushpada",
"Kumbha"=>"Manava",
"Meena"=>"Jalachara"
];



$graha_rel = [

"Sun"=>[
    "friend"=>["Moon","Mars","Jupiter"],
    "neutral"=>["Mercury"],
    "enemy"=>["Saturn","Venus","Rahu","Ketu"]
],

"Moon"=>[
    "friend"=>["Sun","Mercury"],
    "neutral"=>["Mars","Jupiter","Venus","Saturn"],
    "enemy"=>["Rahu","Ketu"]
],

"Mars"=>[
    "friend"=>["Sun","Moon","Jupiter"],
    "neutral"=>["Venus","Saturn","Rahu","Ketu"],
    "enemy"=>["Mercury"]
],

"Mercury"=>[
    "friend"=>["Sun","Venus","Rahu","Ketu"],
    "neutral"=>["Mars","Jupiter","Saturn"],
    "enemy"=>["Moon"]
],

"Jupiter"=>[
    "friend"=>["Sun","Moon","Mars"],
    "neutral"=>["Saturn","Rahu","Ketu"],
    "enemy"=>["Mercury","Venus"]
],

"Venus"=>[
    "friend"=>["Mercury","Saturn","Rahu","Ketu"],
    "neutral"=>["Mars","Jupiter"],
    "enemy"=>["Sun","Moon"]
],

"Saturn"=>[
    "friend"=>["Mercury","Venus","Rahu","Ketu"],
    "neutral"=>["Jupiter"],
    "enemy"=>["Sun","Moon","Mars"]
],

"Rahu"=>[
    "friend"=>["Venus","Saturn","Mercury","Ketu"],
    "neutral"=>["Mars","Jupiter"],
    "enemy"=>["Sun","Moon"]
],

"Ketu"=>[
    "friend"=>["Venus","Saturn","Mercury","Rahu"],
    "neutral"=>["Mars","Jupiter"],
    "enemy"=>["Sun","Moon"]
]

];

// ================= YONI =================
$yoni_relation = [
"Horse"=>["friend"=>["Elephant"],"enemy"=>["Buffalo"]],
"Elephant"=>["friend"=>["Horse"],"enemy"=>["Lion"]],
"Goat"=>["friend"=>["Sheep"],"enemy"=>["Monkey"]],
"Serpent"=>["friend"=>[],"enemy"=>["Mongoose"]],
"Dog"=>["friend"=>[],"enemy"=>["Deer"]],
"Cat"=>["friend"=>[],"enemy"=>["Rat"]],
"Rat"=>["friend"=>[],"enemy"=>["Cat"]],
"Cow"=>["friend"=>[],"enemy"=>["Tiger"]],
"Buffalo"=>["friend"=>[],"enemy"=>["Horse"]],
"Tiger"=>["friend"=>[],"enemy"=>["Cow"]],
"Deer"=>["friend"=>[],"enemy"=>["Dog"]],
"Monkey"=>["friend"=>[],"enemy"=>["Goat"]],
"Mongoose"=>["friend"=>[],"enemy"=>["Serpent"]],
"Lion"=>["friend"=>[],"enemy"=>["Elephant"]]
];

function yoniScore($b,$g,$rel){
    if($b==$g) return 4;
    if(in_array($g,$rel[$b]['friend'])) return 3;
    if(in_array($g,$rel[$b]['enemy'])) return 0;
    return 2;
}

// ================= TARA =================
$nak_order = array_keys($nakshatra_data);

function taraScore($boy,$girl,$order){
    $good=[2,4,6,8,9];

    $calc=function($a,$b,$o){
        $d=(array_search($b,$o)-array_search($a,$o)+27)%27+1;
        $r=$d%9;
        return $r==0?9:$r;
    };

    $names=[1=>"Janma",2=>"Sampat",3=>"Vipat",4=>"Kshema",5=>"Pratyak",6=>"Sadhaka",7=>"Vedha",8=>"Maitra",9=>"Ati Maitra"];

    $r1=$calc($boy,$girl,$order);
    $r2=$calc($girl,$boy,$order);

    $ok1=in_array($r1,$good);
    $ok2=in_array($r2,$good);

    if($ok1&&$ok2) $p=3;
    elseif($ok1||$ok2) $p=1.5;
    else $p=0;

    return [$names[$r1],$names[$r2],$p];
}

function grahaMaitriScore($boy_lord, $girl_lord, $rel){

    function getRelation($p1,$p2,$rel){
        if(in_array($p2,$rel[$p1]['friend'])) return "friend";
        if(in_array($p2,$rel[$p1]['neutral'])) return "neutral";
        return "enemy";
    }

    $r1 = getRelation($boy_lord,$girl_lord,$rel); // boy → girl
    $r2 = getRelation($girl_lord,$boy_lord,$rel); // girl → boy

    if($r1=="friend" && $r2=="friend") return 5;

    if(
        ($r1=="friend" && $r2=="neutral") ||
        ($r1=="neutral" && $r2=="friend")
    ) return 4;

    if($r1=="neutral" && $r2=="neutral") return 3;

    if(
        ($r1=="friend" && $r2=="enemy") ||
        ($r1=="enemy" && $r2=="friend")
    ) return 2;

    if(
        ($r1=="neutral" && $r2=="enemy") ||
        ($r1=="enemy" && $r2=="neutral")
    ) return 1;

    return 0;
}

function ganaScore($boy_gana, $girl_gana){

    $table = [

        "Deva" => [
            "Deva" => 6,
            "Manushya" => 5,
            "Rakshasa" => 1
        ],

        "Manushya" => [
            "Deva" => 5,
            "Manushya" => 6,
            "Rakshasa" => 2
        ],

        "Rakshasa" => [
            "Deva" => 1,
            "Manushya" => 2,
            "Rakshasa" => 6
        ]

    ];

    return $table[$girl_gana][$boy_gana];
}

function vashyaScore($boy, $girl){

    $table = [

        "Chatushpada" => [
            "Chatushpada" => 2,
            "Manava" => 0.5,
            "Jalachara" => 1,
            "Vanachara" => 0
        ],

        "Manava" => [
            "Chatushpada" => 0.5,
            "Manava" => 2,
            "Jalachara" => 0.5,
            "Vanachara" => 0
        ],

        "Jalachara" => [
            "Chatushpada" => 1,
            "Manava" => 0,
            "Jalachara" => 2,
            "Vanachara" => 2
        ],

        "Vanachara" => [
            "Chatushpada" => 0,
            "Manava" => 0,
            "Jalachara" => 2,
            "Vanachara" => 2
        ]

    ];

    return $table[$girl][$boy] ?? 0;
}

function bhakootScoreAdvanced($boy_rasi, $girl_rasi, $rasi_map, $rasi_lord, $graha_rel){

    // distance from girl → boy
    $d = ($rasi_map[$boy_rasi] - $rasi_map[$girl_rasi] + 12) % 12;
    if($d == 0) $d = 12;

    $bad = [2,3,4,5,6];

    $boy_lord = $rasi_lord[$boy_rasi];
    $girl_lord = $rasi_lord[$girl_rasi];

    $isFriend =
        in_array($girl_lord, $graha_rel[$boy_lord]['friend']) ||
        in_array($boy_lord, $graha_rel[$girl_lord]['friend']);

    $sameLord = ($boy_lord == $girl_lord);

    // GOOD
    if(!in_array($d,$bad)){
        return [7, "Good"];
    }

    // NEUTRAL (Cancelled)
    if($sameLord || $isFriend){
        return [7, "Neutral (Dosha Cancelled)"];
    }

    // BAD
    return [0, "Bhakoot Dosha"];
}



// ================= NAKSHATRA NUMBER =================
$nak_num = [
"Ashwini"=>1,"Bharani"=>2,"Krittika"=>3,"Rohini"=>4,"Mrigashira"=>5,
"Ardra"=>6,"Punarvasu"=>7,"Pushya"=>8,"Ashlesha"=>9,"Magha"=>10,
"Purva Phalguni"=>11,"Uttara Phalguni"=>12,"Hasta"=>13,"Chitra"=>14,
"Swati"=>15,"Vishakha"=>16,"Anuradha"=>17,"Jyeshtha"=>18,"Moola"=>19,
"Purva Ashadha"=>20,"Uttara Ashadha"=>21,"Shravana"=>22,"Dhanishta"=>23,
"Shatabhisha"=>24,"Purva Bhadrapada"=>25,"Uttara Bhadrapada"=>26,"Revati"=>27
];

// ================= YONI TABLE FUNCTION =================
function yoniScoreTable($boy,$girl,$nak_num){

    $b = $nak_num[$boy];
    $g = $nak_num[$girl];

    $cols = [[1,24],[2,27],[3,8],[4,5],[6,19],[7,9],[10,11],[12,26],[13,15],[14,16],[17,18],[20,22],[21],[23,25]];

    $rows = [
"1"  => [4,2,2,2,2,2,1,0,1,3,3,2,1,1],
"2"  => [2,4,3,2,2,2,2,3,1,2,3,2,0,2],
"3"  => [2,3,4,2,1,1,3,3,1,2,0,3,1,1],
"4"  => [2,2,2,4,2,1,2,1,2,2,2,2,0,2],
"5"  => [2,2,2,4,2,1,2,1,2,2,2,2,0,2],
"6"  => [2,2,1,2,4,2,1,2,1,2,2,2,1,1],
"7"  => [2,2,2,2,2,4,0,2,2,1,3,3,2,2],
"8"  => [2,3,4,2,1,1,3,3,1,2,0,3,1,1],
"9"  => [2,2,2,2,2,4,0,2,2,1,3,3,2,2],
"10" => [2,2,1,1,1,0,4,2,2,2,2,2,1,1],
"11" => [2,2,1,1,1,0,4,2,2,2,2,2,1,1],
"12" => [1,2,3,2,2,2,2,4,3,0,3,2,2,1],
"13" => [1,3,3,2,2,2,3,4,1,2,2,2,2,3],
"14" => [1,2,1,1,1,1,0,1,4,1,1,2,2,2],
"15" => [1,3,3,2,2,2,3,4,1,2,2,2,2,3],
"16" => [1,2,1,1,1,1,0,1,4,1,1,2,2,2],
"17" => [1,2,2,2,2,2,3,2,1,4,2,2,2,2],
"18" => [1,2,2,2,2,2,3,2,1,4,2,2,2,2],
"19" => [2,2,1,2,4,2,1,2,1,2,2,2,1,1],
"20" => [3,3,0,2,2,3,2,2,2,2,4,3,3,2],
"21" => [2,3,3,0,1,2,2,2,2,2,3,4,2,2],
"22" => [3,3,0,2,2,3,2,2,2,2,4,3,3,2],
"23" => [1,0,1,2,1,1,1,3,2,2,2,2,2,4],
"24" => [4,2,2,2,2,2,1,0,1,3,3,2,1,1],
"25" => [1,0,1,2,1,1,1,3,2,2,2,2,2,4],
"26" => [1,2,3,2,2,2,2,4,3,0,3,2,2,1],
"27" => [2,4,3,2,2,2,2,3,1,2,3,2,0,2],
];

    $row = $rows[$b];

    foreach($cols as $i=>$grp){
        if(in_array($g,$grp)){
            return $row[$i];
        }
    }

    return 0;
}
?>

<?php
if($boy && $girl){

$boy_pada = $_GET['boy_pada'] ?? $boy_pada ?? 1;
$girl_pada = $_GET['girl_pada'] ?? $girl_pada ?? 1;

$boy_rasi = getRasiFromNakshatra($boy,$boy_pada);
$girl_rasi = getRasiFromNakshatra($girl,$girl_pada);

$b=$nakshatra_data[$boy];
$g=$nakshatra_data[$girl];

echo "<div class='match-dashboard'>";

// ================= BOY GIRL TABLE =================
list($t1,$t2,$tmp)=taraScore($boy,$girl,$nak_order);

echo "<div class='match-scorecard'>";
echo "<h3>✦ Natal Details</h3>";
echo "<div class='match-grid'>";

// ---------- BOY ----------
echo "<div class='table-responsive'>";
echo "<table class='match-table'><tr><th colspan=2>Boy ($boy)</th></tr>";
echo "<tr><td><b>Varna</b></td><td>".$rasi_varna[$boy_rasi]."</td></tr>";
echo "<tr><td><b>Vashya</b></td><td>$rasi_vashya[$boy_rasi]</td></tr>";
echo "<tr><td><b>Tara</b></td><td>$t1</td></tr>";
echo "<tr><td><b>Yoni</b></td><td>{$b['yoni']}</td></tr>";
echo "<tr><td><b>Graha (Lord)</b></td><td>".$rasi_lord[$boy_rasi]."</td></tr>";
echo "<tr><td><b>Gana</b></td><td>{$b['gana']}</td></tr>";
echo "<tr><td><b>Rasi</b></td><td>$boy_rasi</td></tr>";
echo "<tr><td><b>Nadi</b></td><td>{$b['nadi']}</td></tr>";
echo "</table></div>";

// ---------- GIRL ----------
echo "<div class='table-responsive'>";
echo "<table class='match-table'><tr><th colspan=2>Girl ($girl)</th></tr>";
echo "<tr><td><b>Varna</b></td><td>".$rasi_varna[$girl_rasi]."</td></tr>";
echo "<tr><td><b>Vashya</b></td><td>$rasi_vashya[$girl_rasi]</td></tr>";
echo "<tr><td><b>Tara</b></td><td>$t2</td></tr>";
echo "<tr><td><b>Yoni</b></td><td>{$g['yoni']}</td></tr>";
echo "<tr><td><b>Graha (Lord)</b></td><td>".$rasi_lord[$girl_rasi]."</td></tr>";
echo "<tr><td><b>Gana</b></td><td>{$g['gana']}</td></tr>";
echo "<tr><td><b>Rasi</b></td><td>$girl_rasi</td></tr>";
echo "<tr><td><b>Nadi</b></td><td>{$g['nadi']}</td></tr>";
echo "</table></div>";

echo "</div></div>"; // End match-grid & match-scorecard

// ================= ASHTA KOOTA =================
echo "<div class='match-scorecard'>";
echo "<h3>✦ Ashta Koota Compatibility</h3>";
echo "<div class='table-responsive'><table class='match-table'>";
echo "<tr><th>Koota</th><th>Boy</th><th>Girl</th><th>Points</th></tr>";

$total = 0;

// ---------- Varna ----------
$p=($rank[$rasi_varna[$boy_rasi]]>=$rank[$rasi_varna[$girl_rasi]])?1:0;
$total+=$p;
$boy_varna = $rasi_varna[$boy_rasi];
$girl_varna = $rasi_varna[$girl_rasi];
echo "<tr><td>Varna</td><td>$boy_varna</td><td>$girl_varna</td><td><span class='score-highlight'>$p</span> / 1</td></tr>";

// ---------- Vashya ----------
$p = vashyaScore($rasi_vashya[$boy_rasi], $rasi_vashya[$girl_rasi]);
$total += $p;
echo "<tr><td>Vashya</td><td>{$b['vashya']}</td><td>{$g['vashya']}</td><td><span class='score-highlight'>$p</span> / 2</td></tr>";

// ---------- Tara ----------
list($t1,$t2,$p)=taraScore($boy,$girl,$nak_order);
$total+=$p;
echo "<tr><td>Tara</td><td>$t1</td><td>$t2</td><td><span class='score-highlight'>$p</span> / 3</td></tr>";

// ---------- Yoni ----------
$p = yoniScoreTable($boy,$girl,$nak_num);
$total+=$p;
echo "<tr><td>Yoni</td><td>{$b['yoni']}</td><td>{$g['yoni']}</td><td><span class='score-highlight'>$p</span> / 4</td></tr>";

// ---------- Graha ----------
$boy_lord = $rasi_lord[$boy_rasi];
$girl_lord = $rasi_lord[$girl_rasi];
$p = grahaMaitriScore($boy_lord,$girl_lord,$graha_rel);
$total+=$p;
echo "<tr><td>Graha</td><td>$boy_lord</td><td>$girl_lord</td><td><span class='score-highlight'>$p</span> / 5</td></tr>";

// ---------- Gana ----------
$p = ganaScore($b['gana'],$g['gana']);
$total+=$p;
echo "<tr><td>Gana</td><td>{$b['gana']}</td><td>{$g['gana']}</td><td><span class='score-highlight'>$p</span> / 6</td></tr>";

// ---------- Bhakoot ----------
list($p,$status) = bhakootScoreAdvanced($boy_rasi, $girl_rasi, $rasi_map, $rasi_lord, $graha_rel);
$total += $p;
echo "<tr><td>Bhakoot</td><td>$boy_rasi</td><td>$girl_rasi</td><td><span class='score-highlight'>$p</span> / 7 <br><span class='badge badge-".($p>=3.5?'good':'bad')."'>$status</span></td></tr>";

// ---------- Nadi ----------
$p=($b['nadi']!=$g['nadi'])?8:0;
$total+=$p;
echo "<tr><td>Nadi</td><td>{$b['nadi']}</td><td>{$g['nadi']}</td><td><span class='score-highlight'>$p</span> / 8</td></tr>";

echo "</table></div>";

// Total Score
echo "<div class='total-score-box'>";
echo "<div class='score'>$total / 36</div>";
echo "<div class='label'>Total Ashta Koota Points</div>";
echo "</div>";

echo "</div>"; // End Ashta Koota Scorecard

}
?>