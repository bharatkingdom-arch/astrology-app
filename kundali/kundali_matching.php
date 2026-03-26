<?php
session_start();
require __DIR__ . '/../header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<style>
body { font-family: Arial; background: #f4f6f9; }

.container { width: 1200px; margin: 30px auto; }

.title { text-align: center; font-size: 30px; margin-bottom: 30px; }

.flex { display: flex; gap: 25px; }

.card {
    flex: 1;
    background: #fff;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

label { font-weight: bold; display: block; margin-top: 12px; }

input {
    width: 100%;
    padding: 12px;
    margin-top: 5px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: #f9f9f9;
    transition: 0.3s;
}

/* ROWS */
.row3, .row2 {
    display: flex;
    gap: 10px;
    margin-top: 5px;
}

.row3 input {
    flex: 1;
    text-align: center;
}

.row2 input { flex: 1; }

/* PLACEHOLDER */
input::placeholder {
    color: #888;
    font-size: 13px;
    opacity: 1;
}

input:focus::placeholder {
    color: transparent;
}

input:focus {
    background: #fff;
    border-color: #000;
    outline: none;
}

/* BUTTON */
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

/* RESULT */
.result {
    margin-top: 30px;
    padding: 20px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 10px #ddd;
}

/* MOBILE */
@media(max-width:1000px){
    .flex { flex-direction: column; }
    .container { width: 95%; }
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
<input name="b_day" placeholder="Day" required>
<input name="b_month" placeholder="Month" required>
<input name="b_year" placeholder="Year" required>
</div>

<label>Birth Time</label>
<div class="row3">
<input name="b_hour" placeholder="Hour" required>
<input name="b_min" placeholder="Minute" required>
<input name="b_sec" placeholder="Second">
</div>

<label>Latitude / Longitude</label>
<div class="row2">
<input name="b_lat" placeholder="Latitude" required>
<input name="b_lon" placeholder="Longitude" required>
</div>

<label>Timezone</label>
<input name="b_tz" value="5.5">

</div>

<!-- GIRL -->
<div class="card">
<h3>Girl Details</h3>

<label>Birth Date</label>
<div class="row3">
<input name="g_day" placeholder="Day" required>
<input name="g_month" placeholder="Month" required>
<input name="g_year" placeholder="Year" required>
</div>

<label>Birth Time</label>
<div class="row3">
<input name="g_hour" placeholder="Hour" required>
<input name="g_min" placeholder="Minute" required>
<input name="g_sec" placeholder="Second">
</div>

<label>Latitude / Longitude</label>
<div class="row2">
<input name="g_lat" placeholder="Latitude" required>
<input name="g_lon" placeholder="Longitude" required>
</div>

<label>Timezone</label>
<input name="g_tz" value="5.5">

</div>

</div>

<button type="submit">Generate Horoscope & Match</button>

</form>

<?php
if(isset($_GET['b_day'])){

    // ===== FORMAT DATE =====
    $b_date = $_GET['b_day'].".".$_GET['b_month'].".".$_GET['b_year'];
    $b_time = $_GET['b_hour'].":".$_GET['b_min'];

    $g_date = $_GET['g_day'].".".$_GET['g_month'].".".$_GET['g_year'];
    $g_time = $_GET['g_hour'].":".$_GET['g_min'];

    // ===== API =====
    $api = "https://www.astroloak.com/astroapi/calculate.php";

    $b_url = $api . "?date=$b_date&time=$b_time&lat=".$_GET['b_lat']."&lon=".$_GET['b_lon']."&timezone=".$_GET['b_tz'];
    $g_url = $api . "?date=$g_date&time=$g_time&lat=".$_GET['g_lat']."&lon=".$_GET['g_lon']."&timezone=".$_GET['g_tz'];

    // FETCH DATA
    $b_data = json_decode(file_get_contents($b_url), true);
    $g_data = json_decode(file_get_contents($g_url), true);
    
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
        $nakshatras = [
            "Ashwini","Bharani","Krittika","Rohini","Mrigashira",
            "Ardra","Punarvasu","Pushya","Ashlesha","Magha",
            "Purva Phalguni","Uttara Phalguni","Hasta","Chitra","Swati",
            "Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha",
            "Uttara Ashadha","Shravana","Dhanishta","Shatabhisha",
            "Purva Bhadrapada","Uttara Bhadrapada","Revati"
        ];

        $nak_size  = 13.3333333333;
        $pada_size = 3.3333333333;

        $nak_index = (int) floor($moon / $nak_size);
        $nak = $nakshatras[$nak_index];

        $balance = $moon - ($nak_index * $nak_size);
        $pada = (int) floor($balance / $pada_size) + 1;

        return [$nak, $pada];
    }

    list($boyNak, $boyPada)   = getNakshatraPada($boyMoon);
    list($girlNak, $girlPada) = getNakshatraPada($girlMoon);

    echo "<div style='text-align:right; margin-bottom:10px;'>
        <a href='kundali_matching.php'>
        <button style='padding:8px 15px;'>🔄 New Match</button>
        </a>
    </div>";

    echo "<div class='result'>";
    echo "<h3>Result</h3>";
    echo "<b>Boy:</b> $boyNak (Pada $boyPada)<br>";
    echo "<b>Girl:</b> $girlNak (Pada $girlPada)<br>";

    $boy = $boyNak;
    $boy_pada = $boyPada;
    $girl = $girlNak;
    $girl_pada = $girlPada;

    include "match.php";
    include "rajju.php";

    echo "</div>";
}
?>

</div>

<?php require __DIR__ . '/../bottom.php'; ?>