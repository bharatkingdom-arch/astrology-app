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

<label>Birth Place*</label>
<div style="position:relative;">
<input type="text" id="b_birth_place" name="b_birthplace" placeholder="Enter a location" autocomplete="off" required>
<div id="b_place_suggestions" class="place-suggestions" style="display:none; position:absolute; width:100%; top:100%;"></div>
</div>
<input type="hidden" id="b_lat" name="b_lat">
<input type="hidden" id="b_lon" name="b_lon">

<input type="hidden" name="b_tz" id="b_tz" value="5.5">

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

<label>Birth Place*</label>
<div style="position:relative;">
<input type="text" id="g_birth_place" name="g_birthplace" placeholder="Enter a location" autocomplete="off" required>
<div id="g_place_suggestions" class="place-suggestions" style="display:none; position:absolute; width:100%; top:100%;"></div>
</div>
<input type="hidden" id="g_lat" name="g_lat">
<input type="hidden" id="g_lon" name="g_lon">

<input type="hidden" name="g_tz" id="g_tz" value="5.5">

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

    if(empty($_GET['b_lat']) || empty($_GET['b_lon']) || empty($_GET['g_lat']) || empty($_GET['g_lon'])) {
        echo "<div class='kundli-saved-box' style='margin-top:30px;color:red;'>❌ Please ensure you select Birth Places from the autocomplete suggestions.</div>";
        echo "</div></div></section>";
        require __DIR__ . '/../bottom.php';
        exit;
    }

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
    include "mahendra.php";

    echo "</div>";
}
?>

</div>
</div>
</section>

<script>
function setupPlacesAutocomplete(inputId, suggestionsId, latId, lonId) {
    const input = document.getElementById(inputId);
    const suggestions = document.getElementById(suggestionsId);
    let timeout = null;
    const apiKey = "fce70220d8a54a3b898d9363403bcae1";

    input.addEventListener("input", function(){
        clearTimeout(timeout);
        const text = this.value;
        if(text.length < 3){
            suggestions.innerHTML="";
            suggestions.style.display="none";
            return;
        }

        timeout = setTimeout(async () => {
            let url = "https://api.geoapify.com/v1/geocode/autocomplete?text="+text+"&limit=5&apiKey="+apiKey;
            let res = await fetch(url);
            let data = await res.json();
            
            suggestions.innerHTML = "";
            suggestions.style.display = "block";
            
            if(!data.features.length){
                suggestions.innerHTML = "<div class='place-empty'>No results</div>";
                return;
            }
            
            data.features.forEach(place => {
                let item = document.createElement("div");
                item.className = "place-item";
                item.innerText = place.properties.formatted;
                
                item.onclick = function(){
                    input.value = place.properties.formatted;
                    document.getElementById(latId).value = place.properties.lat;
                    document.getElementById(lonId).value = place.properties.lon;
                    suggestions.innerHTML = "";
                    suggestions.style.display = "none";
                };
                
                suggestions.appendChild(item);
            });
        }, 300);
    });
    
    document.addEventListener("click", function(e) {
        if (e.target !== input && e.target !== suggestions) {
            suggestions.style.display = "none";
        }
    });
}

setupPlacesAutocomplete("b_birth_place", "b_place_suggestions", "b_lat", "b_lon");
setupPlacesAutocomplete("g_birth_place", "g_place_suggestions", "g_lat", "g_lon");
</script>

<?php require __DIR__ . '/../bottom.php'; ?>