<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'engine/Panchanga.php';

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

        // ==========================
        // CONVERT IST → UTC
        // ==========================

        $datetime = new DateTime(
            "{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}",
            new DateTimeZone('Asia/Kolkata')
        );

        $datetime->setTimezone(new DateTimeZone('UTC'));

        $date = $datetime->format('d.m.Y');
        $time = $datetime->format('H:i');

        // ==========================
        // ASTRO API PARAMETERS
        // ==========================

        $lat = 17.385;
        $lon = 78.486;
        $timezone = 5.5;

        $apiUrl = "http://localhost/public/api/calculate.php"
            . "?date={$date}"
            . "&time={$time}"
            . "&lat={$lat}"
            . "&lon={$lon}"
            . "&timezone={$timezone}";

        // ==========================
        // CALL API USING CURL
        // ==========================

        $ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($ch);
echo "<pre>";
echo $apiUrl;
echo "\n\n";
echo $response;
exit;

if (curl_errno($ch)) {
    $error = curl_error($ch);
}

        if ($response === false) {

            $error = "Unable to connect to astrology engine.";

        } else {

            $data = json_decode($response, true);

            if (!isset($data['status']) || $data['status'] !== 'success') {

                $error = "Astrology calculation failed.";

            } else {

                $planets = $data['planets'];

                // ==========================
                // JULIAN DAY
                // ==========================

                $jd = $datetime->getTimestamp() / 86400 + 2440587.5;

                // ==========================
                // PANCHANGA
                // ==========================

                $panchanga = Panchanga::calculate(
                    $planets['Sun']['decimal'],
                    $planets['Moon']['decimal'],
                    $jd
                );

                // ==========================
                // STORE SESSION DATA
                // ==========================

                $_SESSION['kundli_data'] = [
                    'name' => $_POST['name'] ?? '',
                    'gender' => $_POST['gender'] ?? '',
                    'date' => "{$day}-{$month}-{$year}",
                    'time' => "{$hour}:{$minute}:{$second}",
                    'planets' => $planets,
                    'panchanga' => $panchanga
                ];

                header("Location: kundli-details.php");
                exit;
            }
        }

        curl_close($ch);

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
<input type="text" name="birthplace" required>

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
<a href="#" class="login-btn-kundli">Login</a>
</div>
</div>

</div>
</div>
</section>

<?php require 'bottom.php'; ?>