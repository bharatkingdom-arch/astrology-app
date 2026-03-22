<?php
session_start();

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
    <a href="engine/kpdetails.php">KP</a>
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

<?php foreach ($planets as $planet => $data): ?>

<?php
$status = '';

if (isset($data['retrograde']) && $data['retrograde']) {
    $status .= ' (R)';
}

if (isset($data['combust']) && $data['combust']) {
    $status .= ' (C)';
}
?>

<tr>
<td><?php echo htmlspecialchars($planet); ?></td>
<td><?php echo htmlspecialchars($data['dms']) . $status; ?></td>
</tr>

<?php endforeach; ?>

</table>

</div>

<?php if (!empty($houses)): ?>

<div class="table-box">

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

<div class="table-box">

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
