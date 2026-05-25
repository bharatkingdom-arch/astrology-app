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
                
                <div style="margin-bottom: 15px; position: relative;">
                    <label style="display:block; margin-bottom: 5px; font-weight: 600; color:var(--text-2);">City Name</label>
                    <input type="text" id="place_input" name="place" value="<?= htmlspecialchars($place) ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #bdc3c7;" required autocomplete="off">
                    <div id="place_suggestions" class="place-suggestions" style="display:none; position:absolute; z-index:10; background:var(--bg-primary, #fff); border:1px solid #bdc3c7; width:100%; max-height:200px; overflow-y:auto; border-radius:4px; box-shadow:0 2px 10px rgba(0,0,0,0.1);"></div>
                </div>

                <div style="display:flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display:block; margin-bottom: 5px; font-weight: 600; color:var(--text-2);">Latitude</label>
                        <input type="number" step="any" id="latitude" name="lat" value="<?= $lat ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #bdc3c7;" required>
                    </div>
                    <div style="flex: 1;">
                        <label style="display:block; margin-bottom: 5px; font-weight: 600; color:var(--text-2);">Longitude</label>
                        <input type="number" step="any" id="longitude" name="lon" value="<?= $lon ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #bdc3c7;" required>
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

<style>
.place-item { padding: 10px; cursor: pointer; border-bottom: 1px solid var(--border-color, #ecf0f1); color: var(--text-primary); }
.place-item:hover { background: var(--bg-tertiary, #f8f9fa); }
.place-empty { padding: 10px; color: var(--text-secondary); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const apiKey = "fce70220d8a54a3b898d9363403bcae1";
    const input = document.getElementById("place_input");
    const suggestions = document.getElementById("place_suggestions");
    let timeout = null;

    input.addEventListener("input", function() {
        clearTimeout(timeout);
        const text = this.value;

        if (text.length < 3) {
            suggestions.innerHTML = "";
            suggestions.style.display = "none";
            return;
        }

        timeout = setTimeout(async () => {
            let url = "https://api.geoapify.com/v1/geocode/autocomplete?text=" + encodeURIComponent(text) + "&limit=5&apiKey=" + apiKey;
            try {
                let res = await fetch(url);
                let data = await res.json();
                
                suggestions.innerHTML = "";
                suggestions.style.display = "block";

                if (!data.features || !data.features.length) {
                    suggestions.innerHTML = "<div class='place-empty'>No results</div>";
                    return;
                }

                data.features.forEach(place => {
                    let item = document.createElement("div");
                    item.className = "place-item";
                    item.innerText = place.properties.formatted;
                    
                    item.onclick = function() {
                        input.value = place.properties.formatted;
                        document.getElementById("latitude").value = place.properties.lat;
                        document.getElementById("longitude").value = place.properties.lon;
                        
                        suggestions.innerHTML = "";
                        suggestions.style.display = "none";
                    };
                    
                    suggestions.appendChild(item);
                });
            } catch(e) {
                console.error("Autocomplete error:", e);
            }
        }, 300);
    });

    document.addEventListener('click', function(e) {
        if (e.target !== input && e.target !== suggestions) {
            suggestions.style.display = "none";
        }
    });
});
</script>

<?php require 'footer.php'; ?>
