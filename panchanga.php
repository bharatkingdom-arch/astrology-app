<?php
session_start();

$data = $_SESSION['kundli_data'] ?? null;

if (!$data) {
    header("Location: freekundali.php");
    exit;
}

$panchanga = $data['panchanga'] ?? [];
?>

<?php require 'header.php'; ?>

<style>
/* PANCHANGA PAGE STYLES */
.panchanga-container {
    max-width: 800px;
    margin: 20px auto;
    background-color: var(--card-bg, #fff);
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}

.panchanga-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.panchanga-item {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color, #eaeaea);
    display: flex;
    flex-direction: column;
}

.panchanga-item:last-child {
    border-bottom: none;
}

.panchanga-label {
    font-weight: 600;
    font-size: 1.05rem;
    color: var(--text-color, #333);
    margin-bottom: 5px;
}

.panchanga-value {
    color: var(--text-color-secondary, #555);
    font-size: 0.95rem;
    line-height: 1.4;
}

.panchanga-value span {
    display: block;
}

.panchanga-highlight {
    color: var(--primary-color, #2E8B57); /* Matches the green text in the image */
}

/* Dark mode overrides if any */
body.dark-mode .panchanga-container {
    background-color: #1e1e1e;
    border: 1px solid #333;
}
body.dark-mode .panchanga-item {
    border-bottom: 1px solid #333;
}
body.dark-mode .panchanga-label {
    color: #f1f1f1;
}
body.dark-mode .panchanga-value {
    color: #ccc;
}
body.dark-mode .panchanga-highlight {
    color: #4ade80; /* Lighter green for dark mode */
}
</style>

<section class="kundli-section">
<div class="kundli-container">

<h2>Kundli Details</h2>

<!-- ================= TOP TABS ================= -->
<div class="kundli-tabs">
    <a href="basic-details.php">Basic</a>
    <a href="south-chart.php">Kundli</a>
    <a href="engine/kpdetails.php">KP</a>
    <a href="#">Ashtakavarga</a>
    <a href="#">Charts</a>
    <a href="#">Dasha</a>
    <a href="#">Free Report</a>
    <a href="panchanga.php" class="active">Panchanga</a>
</div>

<div class="panchanga-container">
    <ul class="panchanga-list">
        
        <li class="panchanga-item">
            <span class="panchanga-label">Weekday</span>
            <span class="panchanga-value"><?php echo htmlspecialchars($panchanga['Weekday'] ?? '--'); ?></span>
        </li>
        
        <li class="panchanga-item">
            <span class="panchanga-label">Vaara (Vedic Day)</span>
            <span class="panchanga-value"><?php echo htmlspecialchars($panchanga['Vaara'] ?? '--'); ?></span>
        </li>

        <?php if (isset($panchanga['Tithi']) && is_array($panchanga['Tithi'])): ?>
        <li class="panchanga-item">
            <span class="panchanga-label">Tithi</span>
            <span class="panchanga-value">
                <span class="panchanga-highlight"><?php echo htmlspecialchars($panchanga['Tithi']['name']); ?></span>
                <span><?php echo htmlspecialchars($panchanga['Tithi']['type']); ?></span>
                <span><?php echo htmlspecialchars($panchanga['Tithi']['end']); ?></span>
            </span>
        </li>
        <?php endif; ?>

        <?php if (isset($panchanga['Nakshatra']) && is_array($panchanga['Nakshatra'])): ?>
        <li class="panchanga-item">
            <span class="panchanga-label">Nakshatra</span>
            <span class="panchanga-value">
                <span class="panchanga-highlight"><?php echo htmlspecialchars($panchanga['Nakshatra']['name']); ?></span>
                <span><?php echo htmlspecialchars($panchanga['Nakshatra']['end']); ?></span>
            </span>
        </li>
        <?php endif; ?>

        <?php if (isset($panchanga['Yoga']) && is_array($panchanga['Yoga'])): ?>
        <li class="panchanga-item">
            <span class="panchanga-label">Yoga</span>
            <span class="panchanga-value">
                <span class="panchanga-highlight"><?php echo htmlspecialchars($panchanga['Yoga']['name']); ?></span>
                <span><?php echo htmlspecialchars($panchanga['Yoga']['end']); ?></span>
            </span>
        </li>
        <?php endif; ?>

        <?php if (isset($panchanga['Karana']) && is_array($panchanga['Karana'])): ?>
        <li class="panchanga-item">
            <span class="panchanga-label">Karana</span>
            <span class="panchanga-value">
                <span class="panchanga-highlight"><?php echo htmlspecialchars($panchanga['Karana']['name']); ?></span>
                <span><?php echo htmlspecialchars($panchanga['Karana']['end']); ?></span>
            </span>
        </li>
        <?php endif; ?>

        <li class="panchanga-item">
            <span class="panchanga-label">Moon</span>
            <span class="panchanga-value"><?php echo htmlspecialchars($panchanga['Moon'] ?? '--'); ?></span>
        </li>

        <li class="panchanga-item">
            <span class="panchanga-label">Sun</span>
            <span class="panchanga-value"><?php echo htmlspecialchars($panchanga['Sun'] ?? '--'); ?></span>
        </li>

        <?php if (isset($panchanga['Amrithathi']) && is_array($panchanga['Amrithathi'])): ?>
        <li class="panchanga-item">
            <span class="panchanga-label">Amrithathi Yoga (Tamil)</span>
            <span class="panchanga-value">
                <span><?php echo htmlspecialchars($panchanga['Amrithathi']['name']); ?></span>
                <span><?php echo htmlspecialchars($panchanga['Amrithathi']['end']); ?></span>
            </span>
        </li>
        <?php endif; ?>

    </ul>
</div>

</div>
</section>

<?php require 'bottom.php'; ?>
