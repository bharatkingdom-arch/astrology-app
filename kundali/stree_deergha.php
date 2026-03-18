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

// ================= FUNCTION =================
function streeDeerghaScore($boy, $girl, $nak_order){

    $g = array_search($girl, $nak_order);
    $b = array_search($boy, $nak_order);

    $d = ($b - $g + 27) % 27 + 1;

    if($d >= 13){
        return [$d,2,"Excellent"];
    }
    elseif($d >= 8){
        return [$d,1,"Average"];
    }
    else{
        return [$d,0,"Bad"];
    }
}
// ================= INPUT =================
$boy = $_GET['boy'] ?? '';
$girl = $_GET['girl'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
<style>
body {font-family: Arial;}
table {border-collapse: collapse; margin-top:20px;}
td, th {border:1px solid #999; padding:10px;}
th {background:#333; color:#fff;}
</style>
</head>

<body>

<h2>Stree Deergha Matching</h2>

<form method="GET">
<select name="boy">
<option value="">Select Boy Nakshatra</option>
<?php foreach($nak_order as $n) echo "<option value='$n'>$n</option>"; ?>
</select>

<select name="girl">
<option value="">Select Girl Nakshatra</option>
<?php foreach($nak_order as $n) echo "<option value='$n'>$n</option>"; ?>
</select>

<button>Check</button>
</form>

<?php
if($boy && $girl){

    list($distance,$points,$status) = streeDeerghaScore($boy,$girl,$nak_order);

    echo "<table>";
    echo "<tr><th>Item</th><th>Details</th></tr>";

    echo "<tr><td>Girl Nakshatra</td><td>$girl</td></tr>";
    echo "<tr><td>Boy Nakshatra</td><td>$boy</td></tr>";
    echo "<tr><td>Distance (Girl → Boy)</td><td>$distance</td></tr>";
    echo "<tr><td>Result</td><td>$status</td></tr>";
    echo "<tr><td>Points</td><td>$points / 2</td></tr>";

    echo "</table>";
}
?>

</body>
</html>