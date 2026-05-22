<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================= NAKSHATRA LIST =================
$nak_order = [
"Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra","Punarvasu",
"Pushya","Ashlesha","Magha","Purva Phalguni","Uttara Phalguni","Hasta",
"Chitra","Swati","Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha",
"Uttara Ashadha","Shravana","Dhanishta","Shatabhisha",
"Purva Bhadrapada","Uttara Bhadrapada","Revati"
];

// ================= TATTWA MAP =================
$tattwa_map = [

"Prithvi" => ["Ashwini","Bharani","Krittika","Rohini","Mrigashira"],

"Jala" => ["Ardra","Punarvasu","Pushya","Ashlesha","Magha","Purva Phalguni"],

"Agni" => ["Uttara Phalguni","Hasta","Chitra","Swati","Vishakha","Anuradha"],

"Vayu" => ["Jyeshtha","Moola","Purva Ashadha","Uttara Ashadha","Shravana"],

"Akash" => ["Dhanishta","Shatabhisha","Purva Bhadrapada","Uttara Bhadrapada","Revati"]

];

// ================= GET TATTWA =================
function getTattwa($nak, $map){
    foreach($map as $t => $list){
        if(in_array($nak,$list)) return $t;
    }
    return "Unknown";
}

// ================= FINAL ADVANCED SCORE =================
function tattwaScoreAdvanced($boy, $girl, $map){

    $b = getTattwa($boy,$map);
    $g = getTattwa($girl,$map);

    // Same tattwa
    if($b == $g){
        return [$b,$g,2,"Excellent (Same Tattwa)"];
    }

    // Order independent key
    $pair = [$b,$g];
    sort($pair);
    $key = implode("-",$pair);

    // COMPLETE RULE SET
    $rules = [

        // Strong bad
        "Agni-Jala" => 0,
        "Prithvi-Vayu" => 0,
        "Akash-Prithvi" => 0,

        // Strong good
        "Agni-Vayu" => 2,
        "Jala-Prithvi" => 2,

        // Medium
        "Agni-Prithvi" => 1,
        "Jala-Vayu" => 1,
        "Akash-Agni" => 1,
        "Akash-Jala" => 1,
        "Akash-Vayu" => 1
    ];

    // Safe fallback
    if(isset($rules[$key])){
        $points = $rules[$key];
    } else {
        $points = 1; // default average
    }

    // Status label
    if($points == 2){
        $status = "Good";
    } elseif($points == 1){
        $status = "Average";
    } else {
        $status = "Bad";
    }

    return [$b,$g,$points,$status];
}

// ================= INPUT =================
$boy = $_GET['boy'] ?? $boy ?? '';
$girl = $_GET['girl'] ?? $girl ?? '';
?>

<!DOCTYPE html>
<html>
<head>
<style>
body {font-family: Arial;}
table {border-collapse: collapse; margin-top:20px;}
td, th {border:1px solid #999; padding:10px;}
th {background:#333; color:#fff;}
.good {color:green;}
.avg {color:orange;}
.bad {color:red;}
</style>
</head>

<body>

<h2>Tattwa Matching</h2>



<?php
if($boy && $girl){

    list($bt,$gt,$points,$status) = tattwaScoreAdvanced($boy,$girl,$tattwa_map);

    $class = ($points==2) ? "good" : (($points==1) ? "avg" : "bad");

    echo "<table>";
    echo "<tr><th>Item</th><th>Details</th></tr>";

    echo "<tr><td>Boy Nakshatra</td><td>$boy</td></tr>";
    echo "<tr><td>Girl Nakshatra</td><td>$girl</td></tr>";
    echo "<tr><td>Boy Tattwa</td><td>$bt</td></tr>";
    echo "<tr><td>Girl Tattwa</td><td>$gt</td></tr>";
    echo "<tr><td>Result</td><td class='$class'>$status</td></tr>";
    echo "<tr><td>Points</td><td class='$class'>$points / 2</td></tr>";

    echo "</table>";
}
?>

</body>
</html>