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

// ================= VEDHA PROHIBITED =================
$vedha_pairs = [
["Ashwini","Jyeshtha"],
["Krittika","Vishakha"],
["Ardra","Shravana"],
["Bharani","Anuradha"],
["Rohini","Swati"],
["Punarvasu","Uttara Ashadha"],
["Ashlesha","Moola"],
["Pushya","Purva Ashadha"],
["Magha","Revati"],
["Uttara Phalguni","Purva Bhadrapada"],
["Mrigashira","Dhanishta"],
["Purva Phalguni","Uttara Bhadrapada"],
["Hasta","Shatabhisha"]
];

// ================= VEDHA EXCEPTIONS =================
$vedha_exception = [
["Ashwini","Punarvasu"],
["Bharani","Pushya"],
["Rohini","Magha"],
["Ardra","Uttara Phalguni"],
["Punarvasu","Hasta"],
["Pushya","Chitra"],
["Purva Phalguni","Anuradha"],
["Swati","Uttara Ashadha"],
["Moola","Purva Bhadrapada"],
["Purva Ashadha","Uttara Bhadrapada"],
["Uttara Ashadha","Revati"],
["Purva Bhadrapada","Rohini"]
];

// ================= FUNCTION =================
function vedhaScore($boy, $girl, $vedha_pairs, $vedha_exception){

    // Exception (Girl → Boy)
    foreach($vedha_exception as $pair){
        if($girl == $pair[0] && $boy == $pair[1]){
            return [2,"Good (Vedha Cancelled)"];
        }
    }

    // Prohibited
    foreach($vedha_pairs as $pair){
        if(
            ($boy == $pair[0] && $girl == $pair[1]) ||
            ($boy == $pair[1] && $girl == $pair[0])
        ){
            return [0,"Vedha Dosha"];
        }
    }

    return [2,"No Vedha"];
}

// ================= INPUT =================
$boy = $_GET['boy'] ?? $boy ?? '';
$girl = $_GET['girl'] ?? $girl ?? '';

if($boy && $girl){

    list($points,$status) = vedhaScore($boy,$girl,$vedha_pairs,$vedha_exception);

    echo "<div class='match-scorecard'>";
    echo "<h3>✦ Vedha Dosha Check</h3>";
    
    echo "<table class='match-table'>";
    echo "<tr><th>Item</th><th>Details</th></tr>";

    $badge_class = ($points == 2) ? "badge-good" : "badge-bad";

    echo "<tr><td>Girl Nakshatra</td><td>$girl</td></tr>";
    echo "<tr><td>Boy Nakshatra</td><td>$boy</td></tr>";
    echo "<tr><td>Result</td><td><span class='badge $badge_class'>$status</span></td></tr>";
    echo "<tr><td>Points</td><td><span class='score-highlight'>$points</span> / 2</td></tr>";

    echo "</table>";
    echo "</div>";
}
?>