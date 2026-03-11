<?php
session_start();
session_unset();
?>
$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: freekundali.php");
    exit;
}

$panchanga = $data['panchanga'] ?? [];
$planets   = $data['planets'] ?? [];
$houses    = $data['houses'] ?? [];   // ADD THIS
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

/* ================= NORTH/SOUTH TOGGLE ================= */
.chart-toggle {
    text-align:center;
    margin:15px 0 25px 0;
}

.chart-toggle a {
    display:inline-block;
    padding:8px 25px;
    border-radius:25px;
    text-decoration:none;
    border:1px solid #ccc;
    color:#333;
    margin:0 8px;
    transition:0.3s;
}

.chart-toggle .active-toggle {
    background:#f4c400;
    border:none;
}

/* ================= TABLE STYLING ================= */
.table-box {
    margin-top:30px;
    padding:20px;
    background:#f4f4f4;
    border-radius:10px;
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
}

</style>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Details</h2>

<p><strong>Name:</strong> <?php echo htmlspecialchars($data['name']); ?></p>
<p><strong>Date:</strong> <?php echo htmlspecialchars($data['date']); ?></p>
<p><strong>Time:</strong> <?php echo htmlspecialchars($data['time']); ?></p>

<!-- ================= TOP TABS ================= -->
<div class="kundli-tabs">
    <a href="basic-details.php">Basic</a>
    <a href="south-chart.php" class="active">Kundli</a>
    <a href="#">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Charts</a>
    <a href="#">Dasha</a>
    <a href="#">Free Report</a>
</div>

<!-- ================= NORTH/SOUTH TOGGLE ================= -->
<div class="chart-toggle">
    <a href="#">North Indian</a>
    <a href="south-chart.php" class="active-toggle">South Indian</a>
</div>

<!-- ================= PLANETS ================= -->

<div class="table-box">

<h4>Planetary Positions (Sidereal - Lahiri)</h4>

<table>
<tr>
<th>Planet</th>
<th>Longitude (°)</th>
</tr>

<?php foreach ($planets as $planet => $longitude): ?>
<tr>
<td><?php echo htmlspecialchars($planet); ?></td>
<td><?php echo $longitude['dms']; ?></td>
</tr>
<?php endforeach; ?>


</table>

</div>

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
<td>House <?php echo $i; ?></td>
<td><?php echo htmlspecialchars($houses["House $i"]['dms'] ?? ''); ?></td>
</tr>
<?php endfor; ?>

<tr>
<td><strong>Ascendant</strong></td>
<td><?php echo htmlspecialchars($houses["Ascendant"]['dms'] ?? ''); ?></td>
</tr>

<tr>
<td><strong>MC</strong></td>
<td><?php echo htmlspecialchars($houses["MC"]['dms'] ?? ''); ?></td>
</tr>

</table>

</div>

<?php endif; ?>

<!-- ================= PANCHANGA ================= -->

<?php if (!empty($panchanga)): ?>

<div class="table-box" style="background:#f9f9f9;">

<h4>Panchanga Details</h4>

<table>

<tr>
<td><strong>Tithi</strong></td>
<td><?php echo htmlspecialchars($panchanga['Tithi'] ?? ''); ?></td>
</tr>

<tr>
<td><strong>Nakshatra</strong></td>
<td><?php echo htmlspecialchars($panchanga['Nakshatra'] ?? ''); ?></td>
</tr>

<tr>
<td><strong>Yoga</strong></td>
<td><?php echo htmlspecialchars($panchanga['Yoga'] ?? ''); ?></td>
</tr>

<tr>
<td><strong>Karana</strong></td>
<td><?php echo htmlspecialchars($panchanga['Karana'] ?? ''); ?></td>
</tr>

<tr>
<td><strong>Vara</strong></td>
<td><?php echo htmlspecialchars($panchanga['Vara'] ?? ''); ?></td>
</tr>

</table>

</div>

<?php endif; ?>

</div>
</section>

<?php require 'bottom.php'; ?>
