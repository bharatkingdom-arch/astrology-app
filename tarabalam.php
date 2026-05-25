<?php
session_start();
require 'header.php';

$nakshatras = [
    1=>"Ashwini",2=>"Bharani",3=>"Krittika",4=>"Rohini",
    5=>"Mrigashira",6=>"Ardra",7=>"Punarvasu",8=>"Pushya",
    9=>"Ashlesha",10=>"Magha",11=>"Purva Phalguni",
    12=>"Uttara Phalguni",13=>"Hasta",14=>"Chitra",
    15=>"Swati",16=>"Vishakha",17=>"Anuradha",
    18=>"Jyeshtha",19=>"Mula",20=>"Purva Ashadha",
    21=>"Uttara Ashadha",22=>"Shravana",23=>"Dhanishta",
    24=>"Shatabhisha",25=>"Purva Bhadrapada",
    26=>"Uttara Bhadrapada",27=>"Revati"
];

$rasis = [
    1=>"Mesha (Aries)",2=>"Vrishabha (Taurus)",3=>"Mithuna (Gemini)",
    4=>"Karka (Cancer)",5=>"Simha (Leo)",6=>"Kanya (Virgo)",
    7=>"Tula (Libra)",8=>"Vrischika (Scorpio)",9=>"Dhanu (Sagittarius)",
    10=>"Makara (Capricorn)",11=>"Kumbha (Aquarius)",12=>"Meena (Pisces)"
];

$bride_nak = $_POST['bride_nak'] ?? 1;
$groom_nak = $_POST['groom_nak'] ?? 1;
$bride_rasi = $_POST['bride_rasi'] ?? 1;
$groom_rasi = $_POST['groom_rasi'] ?? 1;
$month = $_POST['month'] ?? date('m');
$year = $_POST['year'] ?? date('Y');

$auspicious_dates = [];
$tara_names = [
    1 => "Janma (Average)", 2 => "Sampat (Excellent)", 3 => "Vipat (Bad)", 
    4 => "Kshema (Good)", 5 => "Pratyak (Bad)", 6 => "Sadhaka (Excellent)", 
    7 => "Naidhana (Very Bad)", 8 => "Mitra (Good)", 9 => "Parama Mitra (Very Good)"
];
$auspicious_tara = [2, 4, 6, 8, 9];

$chandra_names = [
    1 => "1st (Good)", 2 => "2nd (Average)", 3 => "3rd (Good)", 
    4 => "4th (Bad)", 5 => "5th (Average)", 6 => "6th (Good)", 
    7 => "7th (Good)", 8 => "8th/Ashtama (Very Bad)", 9 => "9th (Average)", 
    10 => "10th (Good)", 11 => "11th (Good)", 12 => "12th (Bad)"
];
$bad_chandra = [4, 8, 12];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_days = date('t', strtotime("$year-$month-01"));
    
    $swetestPath = __DIR__ . '/swisseph/swetest';
    $ephePath = __DIR__ . '/swisseph/ephe'; // Ephemeris usually here
    if (!file_exists($ephePath)) {
        $ephePath = __DIR__ . '/ephemeris';
    }

    for ($day = 1; $day <= $num_days; $day++) {
        $dateStr = sprintf("%02d.%02d.%04d", $day, $month, $year);
        // Calculate at 06:00 AM IST approx (00:30 UTC)
        $cmd = "$swetestPath -edir$ephePath -sid1 -b$dateStr -ut00:30:00 -p1 -fPl";
        $output = shell_exec($cmd);
        
        if ($output) {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                if (preg_match('/^Moon\s+([\d\.]+)/i', trim($line), $matches)) {
                    $moon_lon = floatval($matches[1]);
                    $nak_idx = floor($moon_lon / (13 + 1/3)) + 1; // 1 to 27
                    $daily_rasi_idx = floor($moon_lon / 30) + 1; // 1 to 12
                    
                    $b_distance = ($nak_idx - $bride_nak + 27) % 27;
                    $b_count = $b_distance + 1;
                    $b_tara = $b_count % 9;
                    if ($b_tara == 0) $b_tara = 9;
                    
                    $g_distance = ($nak_idx - $groom_nak + 27) % 27;
                    $g_count = $g_distance + 1;
                    $g_tara = $g_count % 9;
                    if ($g_tara == 0) $g_tara = 9;
                    
                    $b_chandra = ($daily_rasi_idx - $bride_rasi + 12) % 12 + 1;
                    $g_chandra = ($daily_rasi_idx - $groom_rasi + 12) % 12 + 1;
                    
                    if (in_array($b_tara, $auspicious_tara) && in_array($g_tara, $auspicious_tara) && 
                        !in_array($b_chandra, $bad_chandra) && !in_array($g_chandra, $bad_chandra)) {
                        $auspicious_dates[] = [
                            'date' => sprintf("%02d-%02d-%04d", $day, $month, $year),
                            'daily_nak' => $nakshatras[$nak_idx],
                            'daily_rasi' => $rasis[$daily_rasi_idx],
                            'b_tara' => $b_tara,
                            'g_tara' => $g_tara,
                            'b_chandra' => $b_chandra,
                            'g_chandra' => $g_chandra
                        ];
                    }
                }
            }
        }
    }
}
?>

<style>
/* Modern Aesthetic Styles for Tarabalam Finder */
:root {
    --tara-primary: #8a2be2;
    --tara-secondary: #ff1493;
    --tara-bg: #f8f9fa;
    --tara-card-bg: #ffffff;
    --tara-text: #2c3e50;
    --tara-text-light: #7f8c8d;
    --tara-success: #2ecc71;
    --tara-border: #ecf0f1;
}

body.dark-mode {
    --tara-bg: #121212;
    --tara-card-bg: #1e1e1e;
    --tara-text: #ecf0f1;
    --tara-text-light: #bdc3c7;
    --tara-border: #333333;
}

.tarabalam-wrapper {
    background-color: var(--tara-bg);
    padding: 40px 20px;
    font-family: 'Inter', sans-serif;
    min-height: 80vh;
}

.tara-container {
    max-width: 900px;
    margin: 0 auto;
    background: var(--tara-card-bg);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.tara-header {
    background: linear-gradient(135deg, var(--tara-primary), var(--tara-secondary));
    padding: 30px;
    text-align: center;
    color: white;
}

.tara-header h1 {
    margin: 0 0 10px 0;
    font-size: 2.2rem;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.tara-header p {
    margin: 0;
    font-size: 1.1rem;
    opacity: 0.9;
}

.tara-form-section {
    padding: 30px;
}

.tara-form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.tara-form-group {
    display: flex;
    flex-direction: column;
}

.tara-form-group label {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--tara-text);
    margin-bottom: 8px;
}

.tara-select {
    padding: 12px 15px;
    border: 1px solid var(--tara-border);
    border-radius: 8px;
    background-color: var(--tara-bg);
    color: var(--tara-text);
    font-size: 1rem;
    outline: none;
    transition: border-color 0.3s, box-shadow 0.3s;
}

.tara-select:focus {
    border-color: var(--tara-primary);
    box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.1);
}

.tara-btn-wrapper {
    grid-column: 1 / -1;
    text-align: center;
    margin-top: 15px;
}

.tara-btn {
    background: linear-gradient(135deg, var(--tara-primary), var(--tara-secondary));
    color: white;
    border: none;
    padding: 14px 40px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 30px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 15px rgba(138, 43, 226, 0.3);
}

.tara-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(138, 43, 226, 0.4);
}

.tara-results-section {
    padding: 0 30px 30px 30px;
}

.tara-results-header {
    margin-bottom: 20px;
    color: var(--tara-text);
    font-size: 1.5rem;
    border-bottom: 2px solid var(--tara-border);
    padding-bottom: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.tara-badge {
    background-color: rgba(46, 204, 113, 0.1);
    color: var(--tara-success);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
}

.tara-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.tara-card {
    background: var(--tara-bg);
    border: 1px solid var(--tara-border);
    border-radius: 12px;
    padding: 20px;
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
    overflow: hidden;
}

.tara-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--tara-success);
}

.tara-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.tara-date {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--tara-primary);
    margin-bottom: 5px;
}

.tara-nak {
    font-size: 0.95rem;
    color: var(--tara-text-light);
    margin-bottom: 15px;
    font-weight: 500;
}

.tara-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.tara-detail-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    padding: 6px 0;
    border-top: 1px dashed var(--tara-border);
}

.tara-detail-label {
    color: var(--tara-text-light);
}

.tara-detail-value {
    font-weight: 600;
    color: var(--tara-text);
}

.tara-no-results {
    text-align: center;
    padding: 40px;
    color: var(--tara-text-light);
    font-size: 1.1rem;
    background: var(--tara-bg);
    border-radius: 12px;
    border: 1px dashed var(--tara-border);
}
</style>

<div class="tarabalam-wrapper">
    <div class="tara-container">
        
        <div class="tara-header">
            <h1>Tarabalam & Chandrabalam Finder</h1>
            <p>Discover Auspicious Dates for Marriage & Events</p>
        </div>

        <div class="tara-form-section">
            <form method="POST" action="tarabalam.php" class="tara-form">
                
                <div class="tara-form-group">
                    <label>Bride's Nakshatra</label>
                    <select name="bride_nak" class="tara-select">
                        <?php foreach($nakshatras as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($bride_nak == $id) ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tara-form-group">
                    <label>Bride's Rasi</label>
                    <select name="bride_rasi" class="tara-select">
                        <?php foreach($rasis as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($bride_rasi == $id) ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tara-form-group">
                    <label>Groom's Nakshatra</label>
                    <select name="groom_nak" class="tara-select">
                        <?php foreach($nakshatras as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($groom_nak == $id) ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tara-form-group">
                    <label>Groom's Rasi</label>
                    <select name="groom_rasi" class="tara-select">
                        <?php foreach($rasis as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($groom_rasi == $id) ? 'selected' : '' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tara-form-group">
                    <label>Month</label>
                    <select name="month" class="tara-select">
                        <?php 
                        for($m=1; $m<=12; $m++){
                            $m_name = date("F", mktime(0, 0, 0, $m, 10));
                            echo "<option value='$m' " . ($month == $m ? 'selected' : '') . ">$m_name</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="tara-form-group">
                    <label>Year</label>
                    <select name="year" class="tara-select">
                        <?php 
                        $currentYear = date('Y');
                        for($y=$currentYear; $y<=$currentYear+5; $y++){
                            echo "<option value='$y' " . ($year == $y ? 'selected' : '') . ">$y</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="tara-btn-wrapper">
                    <button type="submit" class="tara-btn">Find Auspicious Dates</button>
                </div>

            </form>
        </div>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="tara-results-section">
            <div class="tara-results-header">
                <span>Recommended Dates</span>
                <span class="tara-badge"><?= count($auspicious_dates) ?> Found</span>
            </div>

            <?php if (count($auspicious_dates) > 0): ?>
                <div class="tara-grid">
                    <?php foreach ($auspicious_dates as $ad): ?>
                        <div class="tara-card">
                            <div class="tara-date"><?= date('d M Y', strtotime($ad['date'])) ?></div>
                            <div class="tara-nak"><?= $ad['daily_nak'] ?> <br><small style="opacity:0.8"><?= $ad['daily_rasi'] ?></small></div>
                            
                            <div class="tara-details">
                                <div class="tara-detail-row">
                                    <span class="tara-detail-label">Bride Tara</span>
                                    <span class="tara-detail-value"><?= $tara_names[$ad['b_tara']] ?></span>
                                </div>
                                <div class="tara-detail-row">
                                    <span class="tara-detail-label">Groom Tara</span>
                                    <span class="tara-detail-value"><?= $tara_names[$ad['g_tara']] ?></span>
                                </div>
                                <div class="tara-detail-row">
                                    <span class="tara-detail-label">Bride Chandra</span>
                                    <span class="tara-detail-value"><?= $chandra_names[$ad['b_chandra']] ?></span>
                                </div>
                                <div class="tara-detail-row">
                                    <span class="tara-detail-label">Groom Chandra</span>
                                    <span class="tara-detail-value"><?= $chandra_names[$ad['g_chandra']] ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="tara-no-results">
                    No fully auspicious dates found in this month for both bride and groom based on Tarabalam & Chandrabalam.<br>
                    Try checking the next month.
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php require 'bottom.php'; ?>
