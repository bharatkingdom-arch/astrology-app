<?php
session_start();

/* ================= LOAD SESSION ================= */
$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: ../freekundali.php");
    exit;
}

$planets = $data['planets'] ?? [];

/* ================= LOAD KP ENGINE ================= */
require_once __DIR__ . '/KP.php';

/* Calculate KP using decimal longitude */
$kpResults = KP::calculateAll($planets);

/* ================= LOAD HEADER ================= */
require_once __DIR__ . '/../header.php';
?>

<style>

.kp-container {
    max-width:1100px;
    margin:40px auto;
}

.kundli-tabs {
    display:flex;
    justify-content:space-between;
    background:#f3f3f3;
    border-radius:40px;
    padding:6px;
    margin:20px 0 30px 0;
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

.table-box {
    background:#f4f4f4;
    padding:20px;
    border-radius:10px;
    margin-bottom:30px;
}

.table-box h4 {
    margin-bottom:15px;
}

.table-box table {
    width:100%;
    border-collapse: collapse;
}

.table-box th {
    background:#ddd;
}

.table-box th,
.table-box td {
    padding:8px;
    border:1px solid #ccc;
    text-align:center;
}

</style>

<section class="kp-container">

<h2>KP Details</h2>

<div class="kundli-tabs">
   <a href="basic-details.php">Basic</a>
    <a href="south-chart.php">Kundli</a>
    <a href="kpdetails.php" class="active">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Dasha</a>
</div>

<div class="table-box">

<h4>KP Planetary Table</h4>

<table>
<tr>
    <th>Planet</th>
    <th>Longitude (DMS)</th>
    <th>Nakshatra</th>
    <th>Star Lord</th>
    <th>Sub Lord</th>
    <th>Sub-Sub</th>
</tr>

<?php foreach ($kpResults as $planet => $kp): ?>

<?php
    // Get DMS from session
    $dms = '';

    if (isset($planets[$planet]['dms'])) {
        $dms = $planets[$planet]['dms'];
    }
?>

<tr>
    <td><?php echo htmlspecialchars($planet); ?></td>
    <td><?php echo $dms; ?></td>
    <td><?php echo $kp['nakshatra']; ?></td>
    <td><?php echo $kp['star_lord']; ?></td>
    <td><?php echo $kp['sub_lord']; ?></td>
    <td><?php echo $kp['sub_sub_lord']; ?></td>
</tr>

<?php endforeach; ?>

</table>

</div>

</section>

<?php require_once __DIR__ . '/../bottom.php'; ?>
