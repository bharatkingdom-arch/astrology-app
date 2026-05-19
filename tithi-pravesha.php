<?php
session_start();

$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: freekundali.php");
    exit;
}

$planets   = $data['planets'] ?? [];
$S_b       = $planets['Sun']['decimal'] ?? 0;
$M_b       = $planets['Moon']['decimal'] ?? 0;

$dateParts = explode('-', $data['date']);
$timeParts = explode(':', $data['time']);

$birthDate = sprintf("%02d.%02d.%04d", $dateParts[0], $dateParts[1], $dateParts[2]);
$birthTime = sprintf("%02d:%02d:%02d", $timeParts[0], $timeParts[1], $timeParts[2]);
$timezone  = 5.5; // Defaulting to IST as in freekundali.php

require_once 'engine/TithiPraveshaEngine.php';

$currentYear = (int)date('Y');
$results = TithiPraveshaEngine::calculateForYears($birthDate, $birthTime, $timezone, $S_b, $M_b, $currentYear, 10);

?>

<?php require 'header.php'; ?>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Details</h2>

<div class="kundli-tabs">
    <a href="basic-details.php">Basic</a>
    <a href="south-chart.php">Kundli</a>
    <a href="engine/kpdetails.php">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Charts</a>
    <a href="#">Dasha</a>
    <a href="#">Free Report</a>
    <a href="panchanga.php">Panchanga</a>
    <a href="tithi-pravesha.php" class="active">Tithi Pravesha</a>
</div>

<div class="details-container">
    <div class="details-box" style="width: 100%;">
        <h3>Annual Tithi Pravesha (Next 10 Years)</h3>
        <p style="margin-bottom: 20px; color: var(--text-muted, #ccc);">
            Tithi Pravesha marks the exact moment each year when the transit Moon and Sun are at the same angular distance as they were at the time of your birth, while the Sun is in its natal sidereal sign. This moment is considered your true Vedic birthday for the year.
        </p>

        <table class="data-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: var(--bg-card, rgba(255,255,255,0.05)); border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.1));">
                    <th style="padding: 12px; text-align: left;">Year</th>
                    <th style="padding: 12px; text-align: left;">Date</th>
                    <th style="padding: 12px; text-align: left;">Time (Local)</th>
                    <th style="padding: 12px; text-align: left;">Weekday</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($results)): ?>
                    <tr>
                        <td colspan="4" style="padding: 12px; text-align: center;">No data available.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($results as $row): ?>
                        <tr style="border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.05));">
                            <td style="padding: 12px;"><strong><?php echo $row['year']; ?></strong></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['date']); ?></td>
                            <td style="padding: 12px; color: var(--primary-color, #ff9f43); font-weight: bold;"><?php echo htmlspecialchars($row['time']); ?></td>
                            <td style="padding: 12px;"><?php echo htmlspecialchars($row['weekday']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>
</section>

<?php require 'bottom.php'; ?>
