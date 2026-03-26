<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================= NAKSHATRA ORDER =================
$nak_order = [
"Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra","Punarvasu",
"Pushya","Ashlesha","Magha","Purva Phalguni","Uttara Phalguni","Hasta",
"Chitra","Swati","Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha",
"Uttara Ashadha","Shravana","Dhanishta","Shatabhisha",
"Purva Bhadrapada","Uttara Bhadrapada","Revati"
];

// ================= MAHENDRA FUNCTION =================
function mahendraScore($boy, $girl, $nak_order){

    $g = array_search($girl, $nak_order);
    $b = array_search($boy, $nak_order);

    // distance from girl → boy
    $d = ($b - $g + 27) % 27 + 1;

    $good = [4,7,10,13,16,19,22,25];

    // Rule 1
    if(in_array($d,$good)){
        return [$d,2,"Mahendra Good"];
    }

    // Rule 2 (mod rule)
    if($d % 3 == 1){
        return [$d,2,"Mahendra Good (mod rule)"];
    }

    return [$d,0,"No Mahendra"];
}

// ================= INPUT =================
$boy = $boy ?? '';
$girl = $girl ?? '';
?>

<!DOCTYPE html>
<html>
<head>
<style>
body {font-family: Arial;}
table {border-collapse: collapse; margin-top:20px;}
td, th {border:1px solid #999; padding:10px;}
th {background:#333; color:#fff;}
.result {margin-top:20px; font-size:18px;}
</style>
</head>

<body>

<h2>Mahendra Matching</h2>


<?php
if($boy && $girl){

    list($distance,$points,$status) = mahendraScore($boy,$girl,$nak_order);

    echo "<table>";
    echo "<tr><th>Item</th><th>Details</th></tr>";

    echo "<tr><td>Girl Nakshatra</td><td>$girl</td></tr>";
    echo "<tr><td>Boy Nakshatra</td><td>$boy</td></tr>";
    echo "<tr><td>Distance (Girl → Boy)</td><td>$distance</td></tr>";
    echo "<tr><td>Result</td><td>$status</td></tr>";
    echo "<tr><td>Points</td><td>$points / 2</td></tr>";

    echo "</table>";

    // Final message
    echo "<div class='result'>";
    if($points == 2){
        echo "✅ Mahendra Present - Good for prosperity & children";
    } else {
        echo "❌ Mahendra Not Present";
    }
    echo "</div>";
}
?>

</body>
</html>