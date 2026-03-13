<?php
session_start();

/* ================= PREVENT OLD SESSION / CACHE ================= */



header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/engine/Panchanga.php';

$error = null;
$planets = [];

if (isset($_POST['generate'])) {

    $year   = intval($_POST['year'] ?? 0);
    $month  = intval($_POST['month'] ?? 0);
    $day    = intval($_POST['day'] ?? 0);
    $hour   = intval($_POST['hour'] ?? 0);
    $minute = intval($_POST['minute'] ?? 0);
    $second = intval($_POST['second'] ?? 0);

    if ($year > 0 && $month > 0 && $day > 0) {

        $hour   = str_pad($hour, 2, '0', STR_PAD_LEFT);
        $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $second = str_pad($second, 2, '0', STR_PAD_LEFT);

        $date = sprintf("%02d.%02d.%04d", $day, $month, $year);
        $time = sprintf("%02d:%02d", $hour, $minute);

        $datetime = new DateTime(
            "{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}",
            new DateTimeZone('Asia/Kolkata')
        );

        $lat = floatval($_POST['latitude'] ?? 0);
        $lon = floatval($_POST['longitude'] ?? 0);
        $timezone = 5.5;

        if ($lat == 0 || $lon == 0) {
            $error = "Please select birth place from suggestions.";
        }

        if (!$error) {

            /* ================= CALL API ================= */

            $_GET['date'] = $date;
            $_GET['time'] = $time;
            $_GET['lat'] = $lat;
            $_GET['lon'] = $lon;
            $_GET['timezone'] = $timezone;

            ob_start();
            require __DIR__ . '/public/api/calculate.php';
            $response = ob_get_clean();

            $data = json_decode($response, true);

            if (!$data || !isset($data['status']) || $data['status'] !== 'success') {

                $error = "Astrology calculation failed.";

            } else {

                $planets = $data['planets'] ?? [];
                $houses  = $data['houses'] ?? [];

                $lagna = $houses['Ascendant']['decimal'] ?? null;

                /* ================= JULIAN DAY ================= */

                $jd = $datetime->getTimestamp() / 86400 + 2440587.5;

                /* ================= PANCHANGA ================= */

                $panchanga = Panchanga::calculate(
                    $planets['Sun']['decimal'] ?? 0,
                    $planets['Moon']['decimal'] ?? 0,
                    $jd
                );

                /* ================= STORE SESSION ================= */

                $_SESSION['kundli_data'] = [
                    'name' => $_POST['name'] ?? '',
                    'gender' => $_POST['gender'] ?? '',
                    'date' => "{$day}-{$month}-{$year}",
                    'time' => "{$hour}:{$minute}:{$second}",
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'planets' => $planets,
                    'houses' => $houses,
                    'panchanga' => $panchanga,
                    'lagna' => $lagna
                ];

                header("Location: kundli-details.php");
                exit;
            }
        }

    } else {
        $error = "Please enter valid birth date.";
    }
}
?>

<?php require 'header.php'; ?>

<section class="kundli-section">
<div class="kundli-container">

<div class="kundli-title">
<h1>Free Kundli Online</h1>
<p>Get instant & accurate Janam Kundli</p>
<div class="kundli-divider"></div>
</div>

<div class="kundli-description">
<p>Enter your birth details below to generate your horoscope instantly.</p>
</div>

<div class="kundli-wrapper">

<div class="kundli-form-box">

<h3>New Kundli</h3>

<form method="post">

<label>Name*</label>
<input type="text" name="name" required>

<label>Gender*</label>
<select name="gender" required>
<option value="">Select Gender</option>
<option>Male</option>
<option>Female</option>
</select>

<label>Birth Details*</label>

<div class="birth-grid">
<input type="number" name="day" placeholder="Day" min="1" max="31" required>
<input type="number" name="month" placeholder="Month" min="1" max="12" required>
<input type="number" name="year" placeholder="Year" required>
<input type="number" name="hour" placeholder="Hour" min="0" max="23">
<input type="number" name="minute" placeholder="Minute" min="0" max="59">
<input type="number" name="second" placeholder="Second">
</div>

<label>Birth Place*</label>

<input type="text" id="birth_place" name="birthplace" placeholder="Enter a location" required>

<input type="hidden" id="latitude" name="latitude">
<input type="hidden" id="longitude" name="longitude">

<button type="submit" name="generate" class="generate-btn">
Generate Horoscope
</button>

</form>

<?php if ($error): ?>
<div style="margin-top:20px; color:red;">
<?php echo $error; ?>
</div>
<?php endif; ?>

</div>

<div class="kundli-saved-box">
<h3>Saved Kundli</h3>
<div class="saved-content">
<p>Please login to check your saved horoscope!</p>
<a href="/google-login.php" class="login-btn-kundli">
Login with Google
</a>
</div>
</div>

</div>
</div>
</section>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyARG42w7pQoMVayxHrD7D6kFNfMEfpdMF8&libraries=places"></script>

<script>

function initAutocomplete(){

const input = document.getElementById("birth_place");

const autocomplete = new google.maps.places.Autocomplete(input,{
types:['(cities)']
});

autocomplete.addListener("place_changed",function(){

const place = autocomplete.getPlace();

if(!place.geometry) return;

document.getElementById("latitude").value =
place.geometry.location.lat();

document.getElementById("longitude").value =
place.geometry.location.lng();

});

}

google.maps.event.addDomListener(window,'load',initAutocomplete);

</script>

<?php require 'bottom.php'; ?>