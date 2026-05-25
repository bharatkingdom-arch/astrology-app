<?php
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $place = trim($_POST['place'] ?? '');
    $lat = floatval($_POST['lat'] ?? 0);
    $lon = floatval($_POST['lon'] ?? 0);
    $timezone = floatval($_POST['timezone'] ?? 5.5);

    $locData = [
        'place' => $place,
        'lat' => $lat,
        'lon' => $lon,
        'timezone' => $timezone
    ];

    setcookie('default_location', json_encode($locData), time() + (86400 * 365), "/"); // 1 year
    
    // Set in $_COOKIE array immediately for the current page rendering
    $_COOKIE['default_location'] = json_encode($locData);
    $message = "Settings updated successfully!";
}

// Get current defaults
if (isset($_COOKIE['default_location'])) {
    $loc = json_decode($_COOKIE['default_location'], true);
    $place = $loc['place'] ?? "Tenali, Andhra Pradesh";
    $lat = $loc['lat'] ?? 16.23;
    $lon = $loc['lon'] ?? 80.64;
    $timezone = $loc['timezone'] ?? 5.5;
} else {
    $place = "Tenali, Andhra Pradesh";
    $lat = 16.23;
    $lon = 80.64;
    $timezone = 5.5;
}

require 'header.php';
?>

<section class="kundli-section">
    <div class="kundli-container">
        <div class="kundli-title">
            <h1>Application Settings</h1>
            <p>Update your default preferences for the Astrology App.</p>
            <div class="kundli-divider"></div>
        </div>

        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="details-container">
            <form method="POST" action="settings.php">
                <h3 style="margin-bottom:20px; color:var(--text-1); border-bottom: 2px solid #ecf0f1; padding-bottom: 10px;">Default Location Settings</h3>
                
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight: 600; color:var(--text-2);">City Name</label>
                    <input type="text" name="place" value="<?= htmlspecialchars($place) ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #bdc3c7;" required>
                </div>

                <div style="display:flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display:block; margin-bottom: 5px; font-weight: 600; color:var(--text-2);">Latitude</label>
                        <input type="number" step="any" name="lat" value="<?= $lat ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #bdc3c7;" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="display:block; margin-bottom: 5px; font-weight: 600; color:var(--text-2);">Longitude</label>
                        <input type="number" step="any" name="lon" value="<?= $lon ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #bdc3c7;" required>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 5px; font-weight: 600; color:var(--text-2);">Time Zone Offset (Hours from UTC)</label>
                    <input type="number" step="0.5" name="timezone" value="<?= $timezone ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #bdc3c7;" required>
                    <small style="color:var(--text-3); display:block; margin-top: 5px;">Example: 5.5 for IST (+05:30), -4 for EST (-04:00)</small>
                </div>

                <button type="submit" style="background: #e67e22; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; font-weight: 600;">Save Settings</button>
            </form>
        </div>
    </div>
</section>

<?php require 'footer.php'; ?>
