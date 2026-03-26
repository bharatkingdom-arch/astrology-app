<?php
session_start();
require __DIR__ . '/../header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<style>
.container { 
    width: 1200px; 
    margin: 30px auto; 
}

.title { 
    text-align: center; 
    font-size: 30px; 
    margin-bottom: 30px; 
}

.flex { 
    display: flex; 
    gap: 25px; 
}

.card {
    flex: 1;
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

label { 
    font-weight: bold; 
    display: block; 
    margin-top: 12px; 
}

input {
    width: 100%;
    padding: 12px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: #f9f9f9;
    transition: 0.3s;
}

.row3, .row2 {
    display: flex;
    gap: 10px;
    margin-top: 5px;
}

.row3 input { 
    flex: 1; 
    text-align: center; 
}

.row2 input { 
    flex: 1; 
}

input::placeholder { 
    color: #888; 
    font-size: 13px; 
}

input:focus::placeholder { 
    color: transparent; 
}

input:focus {
    background: #fff;
    border-color: #000;
    outline: none;
}

button {
    margin-top: 30px;
    width: 100%;
    padding: 16px;
    background: black;
    color: yellow;
    border: none;
    border-radius: 40px;
    font-size: 18px;
    cursor: pointer;
}

button:hover { 
    background: #222; 
}

.result {
    margin-top: 30px;
    padding: 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 10px #ddd;
}

/* Table Styles */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 14px;
    background: #ffffff;
}

.data-table th, 
.data-table td {
    border: 1px solid #ddd;
    padding: 10px 12px;
    text-align: left;
    vertical-align: top;
}

.data-table th {
    background-color: #f8f8f8;
    font-weight: 700;
    color: #333;
}

.data-table td {
    background-color: #ffffff;
}

.match-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 14px;
}

.match-table th, 
.match-table td {
    border: 1px solid #ddd;
    padding: 10px 12px;
    text-align: left;
}

.match-table th {
    background-color: #f8f8f8;
    font-weight: 700;
}

.score-highlight {
    font-weight: 800;
    font-size: 18px;
    color: #d97706;
}

.section-title {
    font-size: 20px;
    font-weight: bold;
    margin: 25px 0 15px 0;
    padding: 8px 0;
    border-bottom: 2px solid #f5a623;
    color: #333;
}

.new-match-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #f1f5f9;
    padding: 10px 20px;
    border-radius: 40px;
    font-weight: 600;
    color: #1e293b;
    text-decoration: none;
    transition: 0.2s;
    border: 1px solid #e2e8f0;
}

.new-match-btn:hover {
    background: #e9eef3;
    transform: translateY(-2px);
}

.note-box {
    margin-top: 20px;
    padding: 12px 16px;
    background: #fff3e0;
    border-radius: 8px;
    border-left: 4px solid #f5a623;
    font-size: 13px;
}

@media(max-width:1000px){
    .flex { 
        flex-direction: column; 
    }
    .container { 
        width: 95%; 
    }
    .data-table th, .data-table td {
        padding: 6px 8px;
        font-size: 12px;
    }
}
</style>

<div class="container">

<div class="title">Kundali Matching</div>

<form method="GET">

<div class="flex">

<!-- BOY -->
<div class="card">
<h3>Boy Details</h3>

<label>Birth Date</label>
<div class="row3">
<input name="b_day" placeholder="Day" required value="<?php echo isset($_GET['b_day']) ? htmlspecialchars($_GET['b_day']) : ''; ?>">
<input name="b_month" placeholder="Month" required value="<?php echo isset($_GET['b_month']) ? htmlspecialchars($_GET['b_month']) : ''; ?>">
<input name="b_year" placeholder="Year" required value="<?php echo isset($_GET['b_year']) ? htmlspecialchars($_GET['b_year']) : ''; ?>">
</div>

<label>Birth Time</label>
<div class="row3">
<input name="b_hour" placeholder="Hour" required value="<?php echo isset($_GET['b_hour']) ? htmlspecialchars($_GET['b_hour']) : ''; ?>">
<input name="b_min" placeholder="Minute" required value="<?php echo isset($_GET['b_min']) ? htmlspecialchars($_GET['b_min']) : ''; ?>">
<input name="b_sec" placeholder="Second" value="<?php echo isset($_GET['b_sec']) ? htmlspecialchars($_GET['b_sec']) : ''; ?>">
</div>

<label>Latitude / Longitude</label>
<div class="row2">
<input name="b_lat" placeholder="Latitude" required value="<?php echo isset($_GET['b_lat']) ? htmlspecialchars($_GET['b_lat']) : ''; ?>">
<input name="b_lon" placeholder="Longitude" required value="<?php echo isset($_GET['b_lon']) ? htmlspecialchars($_GET['b_lon']) : ''; ?>">
</div>

<label>Timezone</label>
<input name="b_tz" value="<?php echo isset($_GET['b_tz']) ? htmlspecialchars($_GET['b_tz']) : '5.5'; ?>">
</div>

<!-- GIRL -->
<div class="card">
<h3>Girl Details</h3>

<label>Birth Date</label>
<div class="row3">
<input name="g_day" placeholder="Day" required value="<?php echo isset($_GET['g_day']) ? htmlspecialchars($_GET['g_day']) : ''; ?>">
<input name="g_month" placeholder="Month" required value="<?php echo isset($_GET['g_month']) ? htmlspecialchars($_GET['g_month']) : ''; ?>">
<input name="g_year" placeholder="Year" required value="<?php echo isset($_GET['g_year']) ? htmlspecialchars($_GET['g_year']) : ''; ?>">
</div>

<label>Birth Time</label>
<div class="row3">
<input name="g_hour" placeholder="Hour" required value="<?php echo isset($_GET['g_hour']) ? htmlspecialchars($_GET['g_hour']) : ''; ?>">
<input name="g_min" placeholder="Minute" required value="<?php echo isset($_GET['g_min']) ? htmlspecialchars($_GET['g_min']) : ''; ?>">
<input name="g_sec" placeholder="Second" value="<?php echo isset($_GET['g_sec']) ? htmlspecialchars($_GET['g_sec']) : ''; ?>">
</div>

<label>Latitude / Longitude</label>
<div class="row2">
<input name="g_lat" placeholder="Latitude" required value="<?php echo isset($_GET['g_lat']) ? htmlspecialchars($_GET['g_lat']) : ''; ?>">
<input name="g_lon" placeholder="Longitude" required value="<?php echo isset($_GET['g_lon']) ? htmlspecialchars($_GET['g_lon']) : ''; ?>">
</div>

<label>Timezone</label>
<input name="g_tz" value="<?php echo isset($_GET['g_tz']) ? htmlspecialchars($_GET['g_tz']) : '5.5'; ?>">
</div>

</div>

<button type="submit">Generate Horoscope & Match</button>

</form>

<?php
if(isset($_GET['b_day']) && isset($_GET['b_month']) && isset($_GET['b_year'])){

    $b_date = $_GET['b_day'].".".$_GET['b_month'].".".$_GET['b_year'];
    $b_time = $_GET['b_hour'].":".$_GET['b_min'];

    $g_date = $_GET['g_day'].".".$_GET['g_month'].".".$_GET['g_year'];
    $g_time = $_GET['g_hour'].":".$_GET['g_min'];

    $api = "https://www.astroloak.com/astroapi/calculate.php";

    $b_url = $api . "?date=" . urlencode($b_date) . "&time=" . urlencode($b_time) . "&lat=" . $_GET['b_lat'] . "&lon=" . $_GET['b_lon'] . "&timezone=" . $_GET['b_tz'];
    $g_url = $api . "?date=" . urlencode($g_date) . "&time=" . urlencode($g_time) . "&lat=" . $_GET['g_lat'] . "&lon=" . $_GET['g_lon'] . "&timezone=" . $_GET['g_tz'];

    $b_json = @file_get_contents($b_url);
    $g_json = @file_get_contents($g_url);
    
    $b_data = $b_json ? json_decode($b_json, true) : null;
    $g_data = $g_json ? json_decode($g_json, true) : null;

    if(
        !$b_data || !$g_data ||
        !isset($b_data['planets']['Moon']['decimal']) ||
        !isset($g_data['planets']['Moon']['decimal'])
    ){
        echo "<div class='result'>❌ API not responding properly. Please check your birth details.</div>";
    } else {
        $boyMoon  = $b_data['planets']['Moon']['decimal'];
        $girlMoon = $g_data['planets']['Moon']['decimal'];

        // Nakshatra mapping
        $nakshatras = ["Ashwini","Bharani","Krittika","Rohini","Mrigashira",
        "Ardra","Punarvasu","Pushya","Ashlesha","Magha",
        "Purva Phalguni","Uttara Phalguni","Hasta","Chitra","Swati",
        "Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha",
        "Uttara Ashadha","Shravana","Dhanishta","Shatabhisha",
        "Purva Bhadrapada","Uttara Bhadrapada","Revati"];

        function getNakshatraPada($moon, $nakshatras){
            $nak_size = 13.3333333333;
            $pada_size = 3.3333333333;
            $nak_index = (int) floor($moon / $nak_size);
            if($nak_index >= count($nakshatras)) $nak_index = count($nakshatras) - 1;
            $nak = $nakshatras[$nak_index];
            $balance = $moon - ($nak_index * $nak_size);
            $pada = (int) floor($balance / $pada_size) + 1;
            if($pada > 4) $pada = 4;
            return [$nak, $pada, $nak_index];
        }

        list($boyNak, $boyPada, $boyIdx) = getNakshatraPada($boyMoon, $nakshatras);
        list($girlNak, $girlPada, $girlIdx) = getNakshatraPada($girlMoon, $nakshatras);

        // Nakshatra details array
        $nakshatraDetails = [
            "Ashwini" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Horse", "Lord" => "Mars", "Gana" => "Deva", "Rasi" => "Mesha", "Nadi" => "Adi", "Tara" => "Janma"],
            "Bharani" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Elephant", "Lord" => "Venus", "Gana" => "Manushya", "Rasi" => "Mesha", "Nadi" => "Madhya", "Tara" => "Sampat"],
            "Krittika" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Goat", "Lord" => "Sun", "Gana" => "Rakshasa", "Rasi" => "Vrishabha", "Nadi" => "Antya", "Tara" => "Vipat"],
            "Rohini" => ["Varna" => "Shudra", "Vashya" => "Chatushpada", "Yoni" => "Serpent", "Lord" => "Moon", "Gana" => "Manushya", "Rasi" => "Vrishabha", "Nadi" => "Adi", "Tara" => "Kshema"],
            "Mrigashira" => ["Varna" => "Shudra", "Vashya" => "Chatushpada", "Yoni" => "Serpent", "Lord" => "Mars", "Gana" => "Deva", "Rasi" => "Mithuna", "Nadi" => "Madhya", "Tara" => "Pratyak"],
            "Ardra" => ["Varna" => "Shudra", "Vashya" => "Chatushpada", "Yoni" => "Dog", "Lord" => "Rahu", "Gana" => "Manushya", "Rasi" => "Mithuna", "Nadi" => "Antya", "Tara" => "Sadhana"],
            "Punarvasu" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Cat", "Lord" => "Jupiter", "Gana" => "Deva", "Rasi" => "Karka", "Nadi" => "Adi", "Tara" => "Naidhana"],
            "Pushya" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Goat", "Lord" => "Saturn", "Gana" => "Deva", "Rasi" => "Karka", "Nadi" => "Madhya", "Tara" => "Mitra"],
            "Ashlesha" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Cat", "Lord" => "Mercury", "Gana" => "Rakshasa", "Rasi" => "Karka", "Nadi" => "Antya", "Tara" => "Param Mitra"],
            "Magha" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Rat", "Lord" => "Ketu", "Gana" => "Rakshasa", "Rasi" => "Simha", "Nadi" => "Adi", "Tara" => "Janma"],
            "Purva Phalguni" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Rat", "Lord" => "Venus", "Gana" => "Manushya", "Rasi" => "Simha", "Nadi" => "Madhya", "Tara" => "Sampat"],
            "Uttara Phalguni" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Cow", "Lord" => "Sun", "Gana" => "Manushya", "Rasi" => "Kanya", "Nadi" => "Antya", "Tara" => "Vipat"],
            "Hasta" => ["Varna" => "Shudra", "Vashya" => "Dwipada", "Yoni" => "Buffalo", "Lord" => "Moon", "Gana" => "Deva", "Rasi" => "Kanya", "Nadi" => "Adi", "Tara" => "Kshema"],
            "Chitra" => ["Varna" => "Shudra", "Vashya" => "Dwipada", "Yoni" => "Tiger", "Lord" => "Mars", "Gana" => "Rakshasa", "Rasi" => "Tula", "Nadi" => "Madhya", "Tara" => "Pratyak"],
            "Swati" => ["Varna" => "Shudra", "Vashya" => "Dwipada", "Yoni" => "Buffalo", "Lord" => "Rahu", "Gana" => "Deva", "Rasi" => "Tula", "Nadi" => "Antya", "Tara" => "Sadhana"],
            "Vishakha" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Tiger", "Lord" => "Jupiter", "Gana" => "Rakshasa", "Rasi" => "Tula", "Nadi" => "Adi", "Tara" => "Naidhana"],
            "Anuradha" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Deer", "Lord" => "Saturn", "Gana" => "Deva", "Rasi" => "Vrishchika", "Nadi" => "Madhya", "Tara" => "Mitra"],
            "Jyeshtha" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Deer", "Lord" => "Mercury", "Gana" => "Rakshasa", "Rasi" => "Vrishchika", "Nadi" => "Antya", "Tara" => "Param Mitra"],
            "Moola" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Dog", "Lord" => "Ketu", "Gana" => "Rakshasa", "Rasi" => "Dhanu", "Nadi" => "Adi", "Tara" => "Janma"],
            "Purva Ashadha" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Monkey", "Lord" => "Venus", "Gana" => "Manushya", "Rasi" => "Dhanu", "Nadi" => "Madhya", "Tara" => "Sampat"],
            "Uttara Ashadha" => ["Varna" => "Kshatriya", "Vashya" => "Chatushpada", "Yoni" => "Mongoose", "Lord" => "Sun", "Gana" => "Manushya", "Rasi" => "Makara", "Nadi" => "Antya", "Tara" => "Vipat"],
            "Shravana" => ["Varna" => "Shudra", "Vashya" => "Dwipada", "Yoni" => "Monkey", "Lord" => "Moon", "Gana" => "Deva", "Rasi" => "Makara", "Nadi" => "Adi", "Tara" => "Kshema"],
            "Dhanishta" => ["Varna" => "Shudra", "Vashya" => "Dwipada", "Yoni" => "Lion", "Lord" => "Mars", "Gana" => "Rakshasa", "Rasi" => "Kumbha", "Nadi" => "Madhya", "Tara" => "Pratyak"],
            "Shatabhisha" => ["Varna" => "Shudra", "Vashya" => "Dwipada", "Yoni" => "Horse", "Lord" => "Rahu", "Gana" => "Rakshasa", "Rasi" => "Kumbha", "Nadi" => "Antya", "Tara" => "Sadhana"],
            "Purva Bhadrapada" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Lion", "Lord" => "Jupiter", "Gana" => "Manushya", "Rasi" => "Kumbha", "Nadi" => "Adi", "Tara" => "Naidhana"],
            "Uttara Bhadrapada" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Cow", "Lord" => "Saturn", "Gana" => "Manushya", "Rasi" => "Meena", "Nadi" => "Madhya", "Tara" => "Mitra"],
            "Revati" => ["Varna" => "Vaishya", "Vashya" => "Dwipada", "Yoni" => "Elephant", "Lord" => "Mercury", "Gana" => "Deva", "Rasi" => "Meena", "Nadi" => "Antya", "Tara" => "Param Mitra"]
        ];

        $boyDetails = $nakshatraDetails[$boyNak];
        $girlDetails = $nakshatraDetails[$girlNak];

        // Function to calculate points for each Kuta
        function getVarnaPoints($boyVarna, $girlVarna) {
            $order = ["Brahmin", "Kshatriya", "Vaishya", "Shudra"];
            $bIndex = array_search($boyVarna, $order);
            $gIndex = array_search($girlVarna, $order);
            if($bIndex <= $gIndex) return 1;
            return 0;
        }

        function getVashyaPoints($boyVashya, $girlVashya) {
            if($boyVashya == $girlVashya) return 2;
            return 0;
        }

        function getTaraPoints($boyTara, $girlTara, $boyIdx, $girlIdx) {
            $taraOrder = ["Janma", "Sampat", "Vipat", "Kshema", "Pratyak", "Sadhana", "Naidhana", "Mitra", "Param Mitra"];
            $bTara = array_search($boyTara, $taraOrder);
            $gTara = array_search($girlTara, $taraOrder);
            $diff = abs($bTara - $gTara);
            if($diff == 0 || $diff == 6) return 0;
            if($diff == 1 || $diff == 5) return 1.5;
            return 3;
        }

        function getYoniPoints($boyYoni, $girlYoni) {
            $yoniCompatibility = [
                "Horse" => ["Horse"=>4, "Elephant"=>2, "Cow"=>1, "Buffalo"=>0],
                "Elephant" => ["Elephant"=>4, "Horse"=>2, "Rat"=>1, "Tiger"=>0],
                "Goat" => ["Goat"=>4, "Deer"=>2, "Buffalo"=>1, "Dog"=>0],
                "Serpent" => ["Serpent"=>4, "Cat"=>2, "Mongoose"=>1, "Monkey"=>0],
                "Dog" => ["Dog"=>4, "Goat"=>2, "Monkey"=>1, "Lion"=>0],
                "Cat" => ["Cat"=>4, "Serpent"=>2, "Rat"=>1, "Mongoose"=>0],
                "Rat" => ["Rat"=>4, "Elephant"=>2, "Cat"=>1, "Buffalo"=>0],
                "Cow" => ["Cow"=>4, "Horse"=>2, "Goat"=>1, "Buffalo"=>0],
                "Buffalo" => ["Buffalo"=>4, "Cow"=>2, "Rat"=>1, "Goat"=>0],
                "Tiger" => ["Tiger"=>4, "Deer"=>2, "Elephant"=>1, "Lion"=>0],
                "Deer" => ["Deer"=>4, "Tiger"=>2, "Goat"=>1, "Serpent"=>0],
                "Monkey" => ["Monkey"=>4, "Mongoose"=>2, "Serpent"=>1, "Dog"=>0],
                "Mongoose" => ["Mongoose"=>4, "Monkey"=>2, "Cat"=>1, "Serpent"=>0],
                "Lion" => ["Lion"=>4, "Tiger"=>2, "Dog"=>1, "Elephant"=>0]
            ];
            if(isset($yoniCompatibility[$boyYoni][$girlYoni])) {
                return $yoniCompatibility[$boyYoni][$girlYoni];
            }
            return 2;
        }

        function getGrahaPoints($boyLord, $girlLord) {
            $friend = ["Sun"=>["Moon","Mars","Jupiter"], "Moon"=>["Sun","Mercury"], "Mars"=>["Sun","Moon","Jupiter"], "Mercury"=>["Sun","Venus"], "Jupiter"=>["Sun","Moon","Mars"], "Venus"=>["Mercury","Saturn"], "Saturn"=>["Venus","Mercury"], "Rahu"=>["Saturn","Venus"], "Ketu"=>["Mars","Jupiter"]];
            if($boyLord == $girlLord) return 0;
            if(in_array($girlLord, $friend[$boyLord])) return 5;
            return 0;
        }

        function getGanaPoints($boyGana, $girlGana) {
            $order = ["Deva", "Manushya", "Rakshasa"];
            $bIndex = array_search($boyGana, $order);
            $gIndex = array_search($girlGana, $order);
            if($bIndex == $gIndex) return 6;
            if(($bIndex == 0 && $gIndex == 1) || ($bIndex == 1 && $gIndex == 0)) return 5;
            if(($bIndex == 0 && $gIndex == 2) || ($bIndex == 2 && $gIndex == 0)) return 1;
            if(($bIndex == 1 && $gIndex == 2) || ($bIndex == 2 && $gIndex == 1)) return 0;
            return 3;
        }

        function getRasiPoints($boyRasi, $girlRasi) {
            $rasiOrder = ["Mesha", "Vrishabha", "Mithuna", "Karka", "Simha", "Kanya", "Tula", "Vrishchika", "Dhanu", "Makara", "Kumbha", "Meena"];
            $bIndex = array_search($boyRasi, $rasiOrder);
            $gIndex = array_search($girlRasi, $rasiOrder);
            $diff = abs($bIndex - $gIndex);
            if($diff == 0) return 7;
            if($diff == 1 || $diff == 11) return 6;
            if($diff == 2 || $diff == 10) return 5;
            if($diff == 3 || $diff == 9) return 4;
            if($diff == 4 || $diff == 8) return 3;
            if($diff == 5 || $diff == 7) return 2;
            return 1;
        }

        function getNadiPoints($boyNadi, $girlNadi) {
            if($boyNadi != $girlNadi) return 8;
            return 0;
        }

        // Calculate all points
        $varnaPoints = getVarnaPoints($boyDetails['Varna'], $girlDetails['Varna']);
        $vashyaPoints = getVashyaPoints($boyDetails['Vashya'], $girlDetails['Vashya']);
        $taraPoints = getTaraPoints($boyDetails['Tara'], $girlDetails['Tara'], $boyIdx, $girlIdx);
        $yoniPoints = getYoniPoints($boyDetails['Yoni'], $girlDetails['Yoni']);
        $grahaPoints = getGrahaPoints($boyDetails['Lord'], $girlDetails['Lord']);
        $ganaPoints = getGanaPoints($boyDetails['Gana'], $girlDetails['Gana']);
        $rasiPoints = getRasiPoints($boyDetails['Rasi'], $girlDetails['Rasi']);
        $nadiPoints = getNadiPoints($boyDetails['Nadi'], $girlDetails['Nadi']);
        
        $totalPoints = $varnaPoints + $vashyaPoints + $taraPoints + $yoniPoints + $grahaPoints + $ganaPoints + $rasiPoints + $nadiPoints;
        
        // Rajju calculation
        $rajjuMap = ["Ashwini"=>"Siro", "Bharani"=>"Siro", "Krittika"=>"Siro", "Rohini"=>"Kantha", "Mrigashira"=>"Kantha", "Ardra"=>"Kantha", "Punarvasu"=>"Udara", "Pushya"=>"Udara", "Ashlesha"=>"Udara", "Magha"=>"Pada", "Purva Phalguni"=>"Pada", "Uttara Phalguni"=>"Pada", "Hasta"=>"Jangha", "Chitra"=>"Jangha", "Swati"=>"Jangha", "Vishakha"=>"Siro", "Anuradha"=>"Siro", "Jyeshtha"=>"Siro", "Moola"=>"Kantha", "Purva Ashadha"=>"Kantha", "Uttara Ashadha"=>"Kantha", "Shravana"=>"Udara", "Dhanishta"=>"Udara", "Shatabhisha"=>"Udara", "Purva Bhadrapada"=>"Pada", "Uttara Bhadrapada"=>"Pada", "Revati"=>"Pada"];
        
        $boyRajju = $rajjuMap[$boyNak];
        $girlRajju = $rajjuMap[$girlNak];
        $rajjuPoints = ($boyRajju == $girlRajju) ? 0 : 2;
        $rajjuResult = ($boyRajju == $girlRajju) ? "Rajju Dosha" : "No Rajju Dosha";
        
        // Mahendra calculation
        $mahendraDistance = ($girlIdx - $boyIdx + 27) % 27;
        $mahendraPoints = (in_array($mahendraDistance, [4, 5, 6, 10, 11, 12, 17, 18, 19, 23, 24, 25])) ? 2 : 0;
        $mahendraResult = ($mahendraPoints > 0) ? "Mahendra Present - Good for prosperity & children" : "Mahendra Not Present";
        
        // Stree Deergha
        $streeDeerghaDistance = ($girlIdx - $boyIdx + 27) % 27;
        $streeDeerghaPoints = (in_array($streeDeerghaDistance, [3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25])) ? 2 : 0;
        $streeDeerghaResult = ($streeDeerghaPoints > 0) ? "Good" : "Bad";
        
        // Tattwa matching
        $tattwaMap = ["Ashwini"=>"Earth", "Bharani"=>"Earth", "Krittika"=>"Fire", "Rohini"=>"Earth", "Mrigashira"=>"Earth", "Ardra"=>"Fire", "Punarvasu"=>"Air", "Pushya"=>"Water", "Ashlesha"=>"Water", "Magha"=>"Fire", "Purva Phalguni"=>"Fire", "Uttara Phalguni"=>"Fire", "Hasta"=>"Earth", "Chitra"=>"Fire", "Swati"=>"Air", "Vishakha"=>"Fire", "Anuradha"=>"Earth", "Jyeshtha"=>"Earth", "Moola"=>"Earth", "Purva Ashadha"=>"Water", "Uttara Ashadha"=>"Water", "Shravana"=>"Air", "Dhanishta"=>"Air", "Shatabhisha"=>"Air", "Purva Bhadrapada"=>"Air", "Uttara Bhadrapada"=>"Air", "Revati"=>"Earth"];
        
        $boyTattwa = $tattwaMap[$boyNak];
        $girlTattwa = $tattwaMap[$girlNak];
        $tattwaPoints = ($boyTattwa == $girlTattwa) ? 2 : 0;
        $tattwaResult = ($boyTattwa == $girlTattwa) ? "Same Tattwa - Good" : "Different Tattwa";
        
        // Vedha matching
        $vedhaPairs = [
            "Ashwini" => "Jyeshtha", "Bharani" => "Anuradha", "Krittika" => "Visakha", "Rohini" => "Swati",
            "Mrigashira" => "Chitra", "Ardra" => "Hasta", "Punarvasu" => "Uttara Phalguni", "Pushya" => "Purva Phalguni",
            "Ashlesha" => "Magha", "Magha" => "Ashlesha", "Purva Phalguni" => "Pushya", "Uttara Phalguni" => "Punarvasu",
            "Hasta" => "Ardra", "Chitra" => "Mrigashira", "Swati" => "Rohini", "Visakha" => "Krittika",
            "Anuradha" => "Bharani", "Jyeshtha" => "Ashwini", "Moola" => "Revati", "Purva Ashadha" => "Uttara Bhadrapada",
            "Uttara Ashadha" => "Purva Bhadrapada", "Shravana" => "Shatabhisha", "Dhanishta" => "Satabhisha",
            "Shatabhisha" => "Shravana", "Purva Bhadrapada" => "Uttara Ashadha", "Uttara Bhadrapada" => "Purva Ashadha", "Revati" => "Moola"
        ];
        
        $vedhaPoints = (isset($vedhaPairs[$boyNak]) && $vedhaPairs[$boyNak] == $girlNak) ? 0 : 2;
        $vedhaResult = ($vedhaPoints > 0) ? "No Vedha - Good Match" : "Vedha Present - Avoid Match";

        echo "<div style='text-align:right; margin-bottom:10px;'>
        <a href='kundali_matching.php'>
        <button style='padding:8px 15px; background:#f1f5f9; color:#333; width:auto;'>🔄 New Match</button>
        </a></div>";

        echo "<div class='result'>";
        echo "<h3>Result</h3>";
        echo "<p><b>Boy:</b> $boyNak (Pada $boyPada)</p>";
        echo "<p><b>Girl:</b> $girlNak (Pada $girlPada)</p>";
        
        // Table 1: Nakshatra Details
        echo "<table class='data-table'>";
        echo "<tr><th>Parameter</th><th>Boy ($boyNak)</th><th>Girl ($girlNak)</th></tr>";
        echo "<tr><td>Varna</td><td>{$boyDetails['Varna']}</td><td>{$girlDetails['Varna']}</td></tr>";
        echo "<tr><td>Vashya</td><td>{$boyDetails['Vashya']}</td><td>{$girlDetails['Vashya']}</td></tr>";
        echo "<tr><td>Tara</td><td>{$boyDetails['Tara']}</td><td>{$girlDetails['Tara']}</td></tr>";
        echo "<tr><td>Yoni</td><td>{$boyDetails['Yoni']}</td><td>{$girlDetails['Yoni']}</td></tr>";
        echo "<tr><td>Graha (Lord)</td><td>{$boyDetails['Lord']}</td><td>{$girlDetails['Lord']}</td></tr>";
        echo "<tr><td>Gana</td><td>{$boyDetails['Gana']}</td><td>{$girlDetails['Gana']}</td></tr>";
        echo "<tr><td>Rasi</td><td>{$boyDetails['Rasi']}</td><td>{$girlDetails['Rasi']}</td></tr>";
        echo "<tr><td>Nadi</td><td>{$boyDetails['Nadi']}</td><td>{$girlDetails['Nadi']}</td></tr>";
        echo "</table>";
        
        // Table 2: Ashtakoot Points
        echo "<table class='data-table'>";
        echo "<tr><th>Kuta</th><th>Boy</th><th>Girl</th><th>Points</th></tr>";
        echo "<tr><td>Varna</td><td>{$boyDetails['Varna']}</td><td>{$girlDetails['Varna']}</td><td>$varnaPoints</td></tr>";
        echo "<tr><td>Vashya</td><td>{$boyDetails['Vashya']}</td><td>{$girlDetails['Vashya']}</td><td>$vashyaPoints</td></tr>";
        echo "<tr><td>Tara</td><td>{$boyDetails['Tara']}</td><td>{$girlDetails['Tara']}</td><td>$taraPoints</td></tr>";
        echo "<tr><td>Yoni</td><td>{$boyDetails['Yoni']}</td><td>{$girlDetails['Yoni']}</td><td>$yoniPoints</td></tr>";
        echo "<tr><td>Graha Maitri</td><td>{$boyDetails['Lord']}</td><td>{$girlDetails['Lord']}</td><td>$grahaPoints</td></tr>";
        echo "<tr><td>Gana</td><td>{$boyDetails['Gana']}</td><td>{$girlDetails['Gana']}</td><td>$ganaPoints</td></tr>";
        echo "<tr><td>Rasi</td><td>{$boyDetails['Rasi']}</td><td>{$girlDetails['Rasi']}</td><td>$rasiPoints</td></tr>";
        echo "<tr><td>Nadi</td><td>{$boyDetails['Nadi']}</td><td>{$girlDetails['Nadi']}</td><td>$nadiPoints</td></tr>";
        echo "<tr style='background:#f0f0f0; font-weight:bold;'><td colspan='3' style='text-align:right'>Total:</td><td class='score-highlight'>$totalPoints / 36</td></tr>";
        echo "</table>";
        
        // Table 3: Rajju Matching
        echo "<h3>Rajju Matching</h3>";
        echo "<table class='data-table'>";
        echo "<tr><th>Factor</th><th>Boy</th><th>Girl</th><th>Result</th></tr>";
        echo "<tr><td>Rajju</td><td>$boyRajju</td><td>$girlRajju</td><td>" . ($rajjuPoints == 0 ? "Rajju Dosha" : "Good") . "</td></tr>";
        echo "</table>";
        
        // Table 4: Mahendra Matching
        echo "<h3>Mahendra Matching</h3>";
        echo "<table class='data-table'>";
        echo "<tr><th>Girl Nakshatra</th><th>Boy Nakshatra</th><th>Distance</th><th>Result</th><th>Points</th></tr>";
        echo "<tr><td>$girlNak</td><td>$boyNak</td><td>$mahendraDistance</td><td>$mahendraResult</td><td>$mahendraPoints / 2</td></tr>";
        echo "</table>";
        
        // Table 5: Stree Deergha Matching
        echo "<h3>Stree Deergha Matching</h3>";
        echo "<table class='data-table'>";
        echo "<tr><th>Girl Nakshatra</th><th>Boy Nakshatra</th><th>Distance</th><th>Result</th><th>Points</th></tr>";
        echo "<tr><td>$girlNak</td><td>$boyNak</td><td>$streeDeerghaDistance</td><td>$streeDeerghaResult</td><td>$streeDeerghaPoints / 2</td></tr>";
        echo "</table>";
        
        // Table 6: Tattwa Matching
        echo "<h3>Tattwa Matching</h3>";
        echo "<table class='data-table'>";
        echo "<tr><th>Boy Tattwa</th><th>Girl Tattwa</th><th>Result</th><th>Points</th></tr>";
        echo "<tr><td>$boyTattwa</td><td>$girlTattwa</td><td>$tattwaResult</td><td>$tattwaPoints / 2</td></tr>";
        echo "</table>";
        
        // Table 7: Vedha Matching
        echo "<h3>Vedha Matching</h3>";
        echo "<table class='data-table'>";
        echo "<tr><th>Girl Nakshatra</th><th>Boy Nakshatra</th><th>Result</th><th>Points</th></tr>";
        echo "<tr><td>$girlNak</td><td>$boyNak</td><td>$vedhaResult</td><td>$vedhaPoints / 2</td></tr>";
        echo "</table>";
        
        echo "<div class='note-box'>📜 <strong>Note:</strong> This report is generated using Vedic Astrology principles. For personalized remedies and detailed analysis, consult our expert astrologers.</div>";
        
        echo "</div>";
    }
}
?>

</div>

<?php require __DIR__ . '/../bottom.php'; ?>