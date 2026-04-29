<?php
session_start();
require __DIR__ . '/../header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<section class="kundli-section">
<div class="kundli-container">

<div class="kundli-title">
    <h1>Kundali Matching</h1>
    <div class="kundli-divider"></div>
</div>

<form method="GET">

<div class="grid-2">

<!-- BOY -->
<div class="kundli-form-box">
<h3>Boy Details</h3>

<label>Birth Date</label>
<div style="display:flex;gap:10px;">
<input name="b_day" placeholder="Day" required>
<input name="b_month" placeholder="Month" required>
<input name="b_year" placeholder="Year" required>
</div>

<label>Birth Time</label>
<div style="display:flex;gap:10px;">
<input name="b_hour" placeholder="Hour" required>
<input name="b_min" placeholder="Minute" required>
<input name="b_sec" placeholder="Second">
</div>

<label>Latitude / Longitude</label>
<div style="display:flex;gap:10px;">
<input name="b_lat" placeholder="Latitude" required>
<input name="b_lon" placeholder="Longitude" required>
</div>

<label>Timezone</label>
<input name="b_tz" value="5.5">

</div>

<!-- GIRL -->
<div class="kundli-form-box">
<h3>Girl Details</h3>

<label>Birth Date</label>
<div style="display:flex;gap:10px;">
<input name="g_day" placeholder="Day" required>
<input name="g_month" placeholder="Month" required>
<input name="g_year" placeholder="Year" required>
</div>

<label>Birth Time</label>
<div style="display:flex;gap:10px;">
<input name="g_hour" placeholder="Hour" required>
<input name="g_min" placeholder="Minute" required>
<input name="g_sec" placeholder="Second">
</div>

<label>Latitude / Longitude</label>
<div style="display:flex;gap:10px;">
<input name="g_lat" placeholder="Latitude" required>
<input name="g_lon" placeholder="Longitude" required>
</div>

<label>Timezone</label>
<input name="g_tz" value="5.5">

</div>

</div>

<button type="submit" class="generate-btn">Generate Horoscope & Match</button>

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
        echo "<div class='kundli-saved-box' style='margin-top:30px;color:red;'>❌ API not responding properly</div>";
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

    echo "<div style='text-align:right; margin-bottom:10px; margin-top: 30px;'>
        <a href='kundali_matching.php' class='login-btn-kundli' style='text-decoration:none;'>🔄 New Match</a>
    </div>";

    echo "<div class='details-box'>";
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
</div>
</section>

<?php require __DIR__ . '/../bottom.php'; ?>