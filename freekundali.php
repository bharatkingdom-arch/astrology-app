<?php
session_start();

/* ================= PREVENT OLD SESSION / CACHE ================= */

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$login_user  = $_SESSION['user_name'] ?? null;
$user_email  = $_SESSION['user_email'] ?? null;

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

                /* ================= SAVE KUNDLI ================= */

                if ($user_email) {

                    require_once __DIR__ . '/engine/db.php';

                    $planets_json = json_encode($planets);
                    $houses_json  = json_encode($houses);

                    $stmt = $conn->prepare("
                        INSERT INTO kundlis
                        (user_email,name,gender,birth_date,birth_time,latitude,longitude,planets,houses)
                        VALUES (?,?,?,?,?,?,?,?,?)
                    ");

                    $birth_date = "{$year}-{$month}-{$day}";
                    $birth_time = "{$hour}:{$minute}:{$second}";

                    $stmt->bind_param(
                        "ssssddsss",
                        $user_email,
                        $_POST['name'],
                        $_POST['gender'],
                        $birth_date,
                        $birth_time,
                        $lat,
                        $lon,
                        $planets_json,
                        $houses_json
                    );

                    $stmt->execute();
                }

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

<?php if($login_user): ?>

<div style="margin-bottom:15px;font-weight:bold;">
Logged in as: <?= htmlspecialchars($login_user) ?>
</div>

<?php endif; ?>

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
<?= $error ?>
</div>
<?php endif; ?>

</div>

<div class="kundli-saved-box">
<h3>Saved Kundli</h3>
<div class="saved-content">

<?php if($user_email): ?>

<a href="/saved-kundlis.php" class="login-btn-kundli">
View Saved Kundlis
</a>

<?php else: ?>

<p>Please login to check your saved horoscope!</p>

<a href="/google-login.php" class="login-btn-kundli">
Login with Google
</a>

<?php endif; ?>

</div>
</div>

</div>
</div>
</section>

<script>

const apiKey = "fce70220d8a54a3b898d9363403bcae1";

const input = document.getElementById("birth_place");

let timeout = null;

input.addEventListener("input", function(){

clearTimeout(timeout);

const text = this.value;

if(text.length < 3) return;

timeout = setTimeout(async () => {

let url = "https://api.geoapify.com/v1/geocode/autocomplete?text="+text+"&limit=5&apiKey="+apiKey;

let res = await fetch(url);
let data = await res.json();

if(!data.features.length) return;

let place = data.features[0].properties;

document.getElementById("latitude").value = place.lat;
document.getElementById("longitude").value = place.lon;

},300);

});

</script>
<?php require 'bottom.php'; ?>