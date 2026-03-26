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
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 10px #ddd;
}

.match-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 15px;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
}

.match-table th, 
.match-table td {
    border: 1px solid #e2e8f0;
    padding: 12px 16px;
    text-align: left;
    vertical-align: top;
}

.match-table th {
    background-color: #f8fafc;
    font-weight: 700;
    color: #1e293b;
    width: 35%;
}

.score-highlight {
    font-weight: 800;
    font-size: 20px;
    color: #d97706;
}

.badge {
    display: inline-block;
    background: #fef3c7;
    padding: 4px 12px;
    border-radius: 40px;
    font-weight: 600;
    font-size: 13px;
    margin-top: 6px;
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
    background: #eef2ff;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    border-left: 4px solid #f5a623;
}

@media(max-width:1000px){
    .flex { 
        flex-direction: column; 
    }
    .container { 
        width: 95%; 
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
if(isset($_GET['b_day'])){

    $b_date = $_GET['b_day'].".".$_GET['b_month'].".".$_GET['b_year'];
    $b_time = $_GET['b_hour'].":".$_GET['b_min'];

    $g_date = $_GET['g_day'].".".$_GET['g_month'].".".$_GET['g_year'];
    $g_time = $_GET['g_hour'].":".$_GET['g_min'];

    $api = "https://www.astroloak.com/astroapi/calculate.php";

    $b_url = $api . "?date=$b_date&time=$b_time&lat=".$_GET['b_lat']."&lon=".$_GET['b_lon']."&timezone=".$_GET['b_tz'];
    $g_url = $api . "?date=$g_date&time=$g_time&lat=".$_GET['g_lat']."&lon=".$_GET['g_lon']."&timezone=".$_GET['g_tz'];

    $b_data = json_decode(@file_get_contents($b_url), true);
    $g_data = json_decode(@file_get_contents($g_url), true);

    if(
        !$b_data || !$g_data ||
        !isset($b_data['planets']['Moon']['decimal']) ||
        !isset($g_data['planets']['Moon']['decimal'])
    ){
        echo "<div class='result'>❌ API not responding properly</div>";
        exit;
    }

    $boyMoon  = $b_data['planets']['Moon']['decimal'];
    $girlMoon = $g_data['planets']['Moon']['decimal'];

    function getNakshatraPada($moon){
        $nakshatras = ["Ashwini","Bharani","Krittika","Rohini","Mrigashira",
        "Ardra","Punarvasu","Pushya","Ashlesha","Magha",
        "Purva Phalguni","Uttara Phalguni","Hasta","Chitra","Swati",
        "Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha",
        "Uttara Ashadha","Shravana","Dhanishta","Shatabhisha",
        "Purva Bhadrapada","Uttara Bhadrapada","Revati"];

        $nak_size = 13.3333333333;
        $pada_size = 3.3333333333;

        $nak_index = (int) floor($moon / $nak_size);
        if($nak_index >= count($nakshatras)) $nak_index = count($nakshatras) - 1;
        $nak = $nakshatras[$nak_index];

        $balance = $moon - ($nak_index * $nak_size);
        $pada = (int) floor($balance / $pada_size) + 1;
        if($pada > 4) $pada = 4;

        return [$nak, $pada];
    }

    list($boyNak, $boyPada) = getNakshatraPada($boyMoon);
    list($girlNak, $girlPada) = getNakshatraPada($girlMoon);

    // Nakshatra Lords
    $nakshatraLords = [
        "Ashwini"=>"Ketu","Bharani"=>"Venus","Krittika"=>"Sun","Rohini"=>"Moon","Mrigashira"=>"Mars",
        "Ardra"=>"Rahu","Punarvasu"=>"Jupiter","Pushya"=>"Saturn","Ashlesha"=>"Mercury","Magha"=>"Ketu",
        "Purva Phalguni"=>"Venus","Uttara Phalguni"=>"Sun","Hasta"=>"Moon","Chitra"=>"Mars","Swati"=>"Rahu",
        "Vishakha"=>"Jupiter","Anuradha"=>"Saturn","Jyeshtha"=>"Mercury","Moola"=>"Ketu","Purva Ashadha"=>"Venus",
        "Uttara Ashadha"=>"Sun","Shravana"=>"Moon","Dhanishta"=>"Mars","Shatabhisha"=>"Rahu",
        "Purva Bhadrapada"=>"Jupiter","Uttara Bhadrapada"=>"Saturn","Revati"=>"Mercury"
    ];
    
    $boyLord = isset($nakshatraLords[$boyNak]) ? $nakshatraLords[$boyNak] : "Unknown";
    $girlLord = isset($nakshatraLords[$girlNak]) ? $nakshatraLords[$girlNak] : "Unknown";

    // Calculate Guna Score
    $nakshatraList = ["Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra","Punarvasu","Pushya","Ashlesha","Magha","Purva Phalguni","Uttara Phalguni","Hasta","Chitra","Swati","Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha","Uttara Ashadha","Shravana","Dhanishta","Shatabhisha","Purva Bhadrapada","Uttara Bhadrapada","Revati"];
    $boyIdx = array_search($boyNak, $nakshatraList);
    $girlIdx = array_search($girlNak, $nakshatraList);
    
    $score = 18;
    $maxPoints = 36;
    
    if($boyIdx !== false && $girlIdx !== false) {
        $diff = abs($boyIdx - $girlIdx);
        if($diff == 0) $score = 36;
        elseif($diff <= 2) $score = 30;
        elseif($diff <= 5) $score = 24;
        elseif($diff <= 9) $score = 18;
        elseif($diff <= 13) $score = 12;
        else $score = 8;
    }

    // Rajju & Mahendra
    $rajjuResult = "";
    $mahendraResult = "";
    
    if($boyPada == $girlPada) {
        $rajjuResult = "Rajju Dosha Present - Health concerns possible";
    } elseif(abs($boyPada - $girlPada) == 2) {
        $rajjuResult = "Excellent Rajju - Longevity & harmony";
    } else {
        $rajjuResult = "Neutral Rajju - Acceptable";
    }
    
    if(($boyPada + $girlPada) % 3 == 0) {
        $mahendraResult = "Excellent Mahendra - Prosperity & growth";
    } elseif(($boyPada + $girlPada) % 2 == 0) {
        $mahendraResult = "Favorable Mahendra - Good fortune";
    } else {
        $mahendraResult = "Average Mahendra";
    }

    echo "<div style='text-align:right; margin-bottom:10px;'>
    <a href='kundali_matching.php'>
    <button style='padding:8px 15px; background:#f1f5f9; color:#333; width:auto;'>🔄 New Match</button>
    </a></div>";

    echo "<div class='result'>";
    echo "<h3>Kundali Matching Result</h3>";
    
    // Table 1: Nakshatra Details
    echo "<table class='match-table'>";
    echo "<tr><th>Parameter</th><th>Boy (Groom)</th><th>Girl (Bride)</th></tr>";
    echo "<tr><td>🌙 Nakshatra (Birth Star)</td><td><strong>$boyNak</strong> (Pada $boyPada)</td><td><strong>$girlNak</strong> (Pada $girlPada)</td></tr>";
    echo "<tr><td>⭐ Nakshatra Lord</td><td>$boyLord</td><td>$girlLord</td></tr>";
    echo "<tr><td>📐 Moon Degree</td><td>" . round($boyMoon, 2) . "°</td><td>" . round($girlMoon, 2) . "°</td></tr>";
    echo "</table>";
    
    // Table 2: Guna Milan
    echo "<table class='match-table'>";
    echo "<tr><th>Ashtakoot Milan (Guna Milan)</th><th>Score</th><th>Verdict</th></tr>";
    echo "<tr><td>🔢 Total Guna Points</td><td class='score-highlight'>$score / $maxPoints</td><td>";
    if($score >= 28) echo "❤️ Excellent Compatibility";
    elseif($score >= 20) echo "👍 Good Compatibility";
    elseif($score >= 12) echo "🔄 Average Compatibility";
    else echo "⚠️ Low Compatibility";
    echo "</td></tr>";
    echo "</table>";
    
    // Table 3: Additional Kuta
    echo "<table class='match-table'>";
    echo "<tr><th>Matching Factor</th><th>Result</th></tr>";
    echo "<tr><td>🏔️ Rajju Kuta</td><td>$rajjuResult</td></tr>";
    echo "<tr><td>🏵️ Mahendra Kuta</td><td>$mahendraResult</td></tr>";
    echo "</table>";
    
    echo "<div class='note-box'>📅 <strong>Birth Details:</strong> Boy: {$_GET['b_day']}/{$_GET['b_month']}/{$_GET['b_year']} at {$_GET['b_hour']}:{$_GET['b_min']} | Girl: {$_GET['g_day']}/{$_GET['g_month']}/{$_GET['g_year']} at {$_GET['g_hour']}:{$_GET['g_min']}</div>";
    echo "<div class='note-box'>📜 <strong>Note:</strong> This report is generated using Vedic Astrology principles. For personalized remedies, consult our expert astrologers.</div>";
    
    echo "</div>";
}
?>

</div>

<?php require __DIR__ . '/../bottom.php'; ?>