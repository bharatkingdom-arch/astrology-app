<?php
session_start();

require_once 'engine/Avakhada.php';

$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: freekundali.php");
    exit;
}

$planets   = $data['planets'] ?? [];
$panchanga = $data['panchanga'] ?? [];

/* ================= SAFE MOON DECIMAL ================= */
$moonLongitude = 0;

if (isset($planets['Moon']['decimal'])) {
    $moonLongitude = floatval($planets['Moon']['decimal']);
} elseif (isset($planets['Moon'])) {
    $moonLongitude = floatval($planets['Moon']);
}

/* ================= AVAKHADA ================= */
$avakhada = Avakhada::calculate($moonLongitude);

/* ================= DMS CONVERTER ================= */
function decimalToDMS($degree)
{
    $degree = floatval($degree);

    $d = floor($degree);
    $mFloat = ($degree - $d) * 60;
    $m = floor($mFloat);
    $s = floor(($mFloat - $m) * 60);

    return sprintf("%03d° %02d' %02d\"", $d, $m, $s);
}
?>

<?php require 'header.php'; ?>

<style>

/* ================= TABS ================= */
.kundli-tabs {
    display:flex;
    justify-content:space-between;
    background:#f3f3f3;
    border-radius:40px;
    padding:6px;
    margin:20px 0;
}

.kundli-tabs a {
    flex:1;
    text-align:center;
    padding:10px 0;
    text-decoration:none;
    color:#333;
    font-weight:500;
    border-radius:30px;
    transition:0.3s;
}

.kundli-tabs a:hover {
    background:#eaeaea;
}

.kundli-tabs .active {
    background:#f4c400;
    font-weight:600;
}

/* ================= LAYOUT ================= */
.details-container {
    max-width:1100px;
    margin:40px auto;
}

.details-row {
    display:flex;
    gap:30px;
}

.details-box {
    flex:1;
    background:#f4f4f4;
    padding:20px;
    border-radius:10px;
    border:1px solid #ddd;
}

.details-box h3 {
    margin-bottom:20px;
    font-size:18px;
}

.detail-item {
    display:flex;
    justify-content:space-between;
    margin-bottom:12px;
    font-size:14px;
}

.section-title {
    margin-top:30px;
    font-size:18px;
}

.panchang-box {
    margin-top:15px;
    background:#f4f4f4;
    padding:20px;
    border-radius:10px;
    border:1px solid #ddd;
}

</style>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Details</h2>

<!-- ================= TOP TABS ================= -->
<div class="kundli-tabs">
    <a href="basic-details.php" class="active">Basic</a>
    <a href="south-chart.php">Kundli</a>
    <a href="engine/kpdetails.php">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Charts</a>
    <a href="#">Dasha</a>
    <a href="#">Free Report</a>
</div>

<div class="details-container">

<div class="details-row">

    <!-- ================= BASIC DETAILS ================= -->
    <div class="details-box">
        <h3>Basic Details</h3>

        <div class="detail-item">
            <span>Name</span>
            <span><?php echo htmlspecialchars($data['name']); ?></span>
        </div>

        <div class="detail-item">
            <span>Date</span>
            <span><?php echo htmlspecialchars($data['date']); ?></span>
        </div>

        <div class="detail-item">
            <span>Time</span>
            <span><?php echo htmlspecialchars($data['time']); ?></span>
        </div>

        <div class="detail-item">
            <span>Moon Longitude</span>
            <span><?php echo decimalToDMS($moonLongitude); ?></span>
        </div>

        <div class="detail-item">
            <span>Nakshatra</span>
            <span>
                <?php echo $avakhada['Nakshatra']; ?>
                (Pada <?php echo $avakhada['Pada']; ?>)
            </span>
        </div>

        <div class="detail-item">
            <span>Nakshatra Lord</span>
            <span><?php echo $avakhada['Nakshatra Lord']; ?></span>
        </div>

    </div>

    <!-- ================= AVAKHADA DETAILS ================= -->
    <div class="details-box">
        <h3>Avakhada Details</h3>

        <div class="detail-item">
            <span>Varna</span>
            <span><?php echo $avakhada['Varna']; ?></span>
        </div>

        <div class="detail-item">
            <span>Gan</span>
            <span><?php echo $avakhada['Gan']; ?></span>
        </div>

        <div class="detail-item">
            <span>Nadi</span>
            <span><?php echo $avakhada['Nadi']; ?></span>
        </div>

        <div class="detail-item">
            <span>Yoni</span>
            <span><?php echo $avakhada['Yoni']; ?></span>
        </div>

        <div class="detail-item">
            <span>Tatva</span>
            <span><?php echo $avakhada['Tatva']; ?></span>
        </div>

        <div class="detail-item">
            <span>Name Alphabet</span>
            <span><?php echo $avakhada['Name Alphabet']; ?></span>
        </div>

    </div>

</div>

<!-- ================= PANCHANG DETAILS ================= -->

<h3 class="section-title">Panchang Details</h3>

<div class="panchang-box">

    <div class="detail-item">
        <span>Tithi</span>
        <span><?php echo $panchanga['Tithi'] ?? '--'; ?></span>
    </div>

    <div class="detail-item">
        <span>Karan</span>
        <span><?php echo $panchanga['Karana'] ?? '--'; ?></span>
    </div>

    <div class="detail-item">
        <span>Yog</span>
        <span><?php echo $panchanga['Yoga'] ?? '--'; ?></span>
    </div>

    <div class="detail-item">
        <span>Nakshatra</span>
        <span><?php echo $avakhada['Nakshatra']; ?></span>
    </div>

</div>

</div>
</div>
</section>

<?php require 'bottom.php'; ?>
