


<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ================= LOAD SESSION ================= */
$data = $_SESSION['kundli_data'] ?? null;

if (!$data || empty($data['planets'])) {
    header("Location: freekundali.php");
    exit;
}

$panchanga = $data['panchanga'] ?? [];
$planets   = $data['planets'] ?? [];
$houses    = $data['houses'] ?? [];

require 'header.php';
?>
<style>

/* ================= TABS ================= */
.kundli-tabs {
    display: flex;
    justify-content: space-between;
    background: #f3f3f3;
    border-radius: 40px;
    padding: 6px;
    margin: 20px 0;
}

.kundli-tabs a {
    flex: 1;
    text-align: center;
    padding: 10px 0;
    text-decoration: none;
    color: #333;
    font-weight: 500;
    border-radius: 30px;
    transition: 0.3s;
}

.kundli-tabs a:hover {
    background: #eaeaea;
}

.kundli-tabs .active {
    background: #f4c400;
    font-weight: 600;
}

/* ================= BIRTH BAR ================= */
.birth-bar {
    text-align:center;
    margin:15px 0;
    padding:10px;
    background:#f9f9f9;
    border-radius:25px;
    font-size:15px;
}

/* ================= LAGNA BAR ================= */
.lagna-bar {
    text-align:center;
    margin-bottom:20px;
    padding:10px;
    background:#eef6ff;
    border-radius:25px;
    font-weight:600;
}

/* ================= TABLE STYLING ================= */
.table-box {
    margin-top: 30px;
    padding: 20px;
    background: #f4f4f4;
    border-radius: 10px;
}

.table-box h4 {
    margin-bottom: 15px;
}

.table-box table {
    width: 100%;
    border-collapse: collapse;
}

.table-box th {
    background: #ddd;
}

.table-box th,
.table-box td {
    padding: 8px;
    border: 1px solid #ccc;
    text-align: center;
}

</style>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Details</h2>

<!-- ================= TOP TABS ================= -->
<div class="kundli-tabs">
    <a href="basic-details.php">Basic</a>
    <a href="south-chart.php" class="active">Kundli</a>
    <a href="engine/kpdetails.php">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Charts</a>
    <a href="#">Dasha</a>
    <a href="#">Free Report</a>
</div>

<!-- ================= BIRTH DETAILS ================= -->
<div class="birth-bar">
    <strong><?= htmlspecialchars($data['name'] ?? '') ?></strong> |
    Date: <?= htmlspecialchars($data['date'] ?? '') ?> |
    Time: <?= htmlspecialchars($data['time'] ?? '') ?>
</div>

<!-- ================= LAGNA ================= -->
<?php if (!empty($data['lagna'])): ?>
<div class="lagna-bar">
    Lagna (Ascendant): <?= htmlspecialchars($data['lagna']['dms'] ?? '') ?>
</div>
<?php endif; ?>



<!-- ================= PLANETS ================= -->
<div class="table-box">

<h4>Planetary Positions (Sidereal - Lahiri)</h4>

<table>
<tr>
    <th>Planet</th>
    <th>Longitude (DMS)</th>
</tr>

<?php foreach ($planets as $planet => $longitude): ?>
<tr>
    <td><?= htmlspecialchars($planet) ?></td>
    <td><?= htmlspecialchars($longitude['dms'] ?? '') ?></td>
</tr>
<?php endforeach; ?>

</table>

</div>


<!-- ================= HOUSES SECTION (NEW) ================= -->
<?php if (!empty($houses)): ?>

<div class="table-box" style="background:#fdf7e6;">

<h4>House Cusps (Placidus)</h4>

<table>
<tr>
    <th>House</th>
    <th>Degree (DMS)</th>
</tr>

<?php for ($i=1; $i<=12; $i++): ?>
<tr>
    <td>House <?= $i ?></td>
    <td><?= htmlspecialchars($houses["House $i"]['dms'] ?? '') ?></td>
</tr>
<?php endfor; ?>

<tr>
    <td><strong>Ascendant</strong></td>
    <td><?= htmlspecialchars($houses["Ascendant"]['dms'] ?? '') ?></td>
</tr>

<tr>
    <td><strong>MC</strong></td>
    <td><?= htmlspecialchars($houses["MC"]['dms'] ?? '') ?></td>
</tr>

</table>

</div>

<?php endif; ?>
<!-- ================= END HOUSES ================= -->



<!-- ================= PANCHANGA ================= -->
<?php if (!empty($panchanga)): ?>

<div class="table-box" style="background:#f9f9f9;">

<h4>Panchanga Details</h4>

<table>
<tr>
    <td><strong>Tithi</strong></td>
    <td><?= htmlspecialchars($panchanga['Tithi'] ?? '') ?></td>
</tr>
<tr>
    <td><strong>Nakshatra</strong></td>
    <td><?= htmlspecialchars($panchanga['Nakshatra'] ?? '') ?></td>
</tr>
<tr>
    <td><strong>Yoga</strong></td>
    <td><?= htmlspecialchars($panchanga['Yoga'] ?? '') ?></td>
</tr>
<tr>
    <td><strong>Karana</strong></td>
    <td><?= htmlspecialchars($panchanga['Karana'] ?? '') ?></td>
</tr>
<tr>
    <td><strong>Vara</strong></td>
    <td><?= htmlspecialchars($panchanga['Vara'] ?? '') ?></td>
</tr>
</table>

</div>

<?php endif; ?>

</div>
</section>

<?php require 'bottom.php'; ?>

