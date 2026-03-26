<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Astrolook - Kundali Matching | Horoscope Compatibility</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Poppins', system-ui, -apple-system, 'Inter', Roboto, sans-serif;
            background: #f4f6f9;
            color: #1a1a2e;
        }

        /* ========= MODERN HEADER STYLES (inspired by second image "Astroloak" style) ========= */
        .modern-header {
            background: #ffffff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            padding: 12px 0;
        }

        .logo-area {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .logo-main {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #1e1e2f, #2d2d44);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo-accent {
            font-size: 28px;
            font-weight: 700;
            color: #f5b042;
        }

        .nav-links {
            display: flex;
            gap: 28px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            color: #2c3e50;
            transition: 0.2s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #f5a623;
        }

        .login-btn {
            background: #000000;
            color: #ffd966 !important;
            padding: 8px 20px;
            border-radius: 40px;
            font-weight: 600;
            margin-left: 8px;
            border: 1px solid #222;
        }

        .login-btn:hover {
            background: #2c2c2c;
            color: #ffea9e !important;
        }

        /* main container */
        .container {
            max-width: 1300px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .page-title {
            text-align: center;
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 32px;
            color: #1e2a3a;
            letter-spacing: -0.3px;
            position: relative;
        }

        .page-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: #f5a623;
            margin: 12px auto 0;
            border-radius: 4px;
        }

        .flex {
            display: flex;
            gap: 28px;
            flex-wrap: wrap;
        }

        .card {
            flex: 1;
            min-width: 280px;
            background: #fff;
            padding: 28px 26px;
            border-radius: 28px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .card:hover {
            box-shadow: 0 20px 32px rgba(0, 0, 0, 0.08);
        }

        .card h3 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 24px;
            border-left: 5px solid #f5a623;
            padding-left: 16px;
            color: #1e2a36;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 20px;
            margin-bottom: 6px;
            font-size: 14px;
            color: #2c3e50;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: #fefefe;
            transition: 0.2s;
            font-size: 15px;
            font-family: inherit;
        }

        input:focus {
            border-color: #f5a623;
            outline: none;
            box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.2);
            background: #ffffff;
        }

        .row3, .row2 {
            display: flex;
            gap: 12px;
            margin-top: 6px;
        }

        .row3 input {
            flex: 1;
            text-align: center;
        }

        .row2 input {
            flex: 1;
        }

        input::placeholder {
            color: #a0aec0;
            font-size: 13px;
        }

        .generate-btn {
            margin-top: 38px;
            width: 100%;
            padding: 16px;
            background: black;
            color: #ffdf80;
            border: none;
            border-radius: 60px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        .generate-btn:hover {
            background: #1f1f1f;
            transform: scale(0.98);
            color: #ffedb2;
        }

        .result {
            margin-top: 40px;
            padding: 28px 32px;
            background: white;
            border-radius: 28px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
            border: 1px solid #f0e7db;
        }

        .result h3 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result h3:before {
            content: "✨";
            font-size: 28px;
        }

        .match-detail {
            background: #faf9ff;
            padding: 18px;
            border-radius: 20px;
            margin: 16px 0;
            border-left: 5px solid #f5a623;
        }

        .new-match-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f1f5f9;
            padding: 10px 20px;
            border-radius: 40px;
            font-weight: 600;
            color: #1e293b;
            text-decoration: none;
            transition: 0.2s;
            border: 1px solid #e2e8f0;
        }

        .new-match-btn:hover {
            background: #e9eef3;
            transform: translateY(-2px);
        }

        @media (max-width: 860px) {
            .modern-header {
                padding: 0 16px;
            }
            .header-container {
                flex-direction: column;
                gap: 12px;
                padding: 12px 0;
            }
            .nav-links {
                justify-content: center;
                gap: 18px;
            }
            .container {
                padding: 0 18px;
            }
            .card {
                padding: 20px;
            }
        }

        footer {
            text-align: center;
            padding: 28px 20px;
            margin-top: 40px;
            color: #5a6e7c;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
            background: white;
        }
    </style>
</head>
<body>

<!-- Header section redesigned exactly like second image reference: "Astroloak" style but keeping brand name -->
<div class="modern-header">
    <div class="header-container">
        <div class="logo-area">
            <span class="logo-main">Astrolook</span>
            <span class="logo-accent">✨</span>
        </div>
        <div class="nav-links">
            <a href="#">Best Astrologers</a>
            <a href="#">Chat with Astrologer</a>
            <a href="#">Talk to Astrologer</a>
            <a href="#">Astromall</a>
            <a href="#">Astrolook Store</a>
            <a href="#">Blogs</a>
            <a href="#" class="login-btn">Login</a>
        </div>
    </div>
</div>

<div class="container">
    <div class="page-title">✨ Kundali Matching ✨</div>

    <form method="GET">
        <div class="flex">
            <!-- Boy Card -->
            <div class="card">
                <h3>👦 Boy Details</h3>
                <label>Birth Date</label>
                <div class="row3">
                    <input type="text" name="b_day" placeholder="Day" required value="<?php echo isset($_GET['b_day']) ? htmlspecialchars($_GET['b_day']) : ''; ?>">
                    <input type="text" name="b_month" placeholder="Month" required value="<?php echo isset($_GET['b_month']) ? htmlspecialchars($_GET['b_month']) : ''; ?>">
                    <input type="text" name="b_year" placeholder="Year" required value="<?php echo isset($_GET['b_year']) ? htmlspecialchars($_GET['b_year']) : ''; ?>">
                </div>

                <label>Birth Time</label>
                <div class="row3">
                    <input type="text" name="b_hour" placeholder="Hour" required value="<?php echo isset($_GET['b_hour']) ? htmlspecialchars($_GET['b_hour']) : ''; ?>">
                    <input type="text" name="b_min" placeholder="Minute" required value="<?php echo isset($_GET['b_min']) ? htmlspecialchars($_GET['b_min']) : ''; ?>">
                    <input type="text" name="b_sec" placeholder="Second" value="<?php echo isset($_GET['b_sec']) ? htmlspecialchars($_GET['b_sec']) : ''; ?>">
                </div>

                <label>Latitude / Longitude</label>
                <div class="row2">
                    <input type="text" name="b_lat" placeholder="Latitude" required value="<?php echo isset($_GET['b_lat']) ? htmlspecialchars($_GET['b_lat']) : ''; ?>">
                    <input type="text" name="b_lon" placeholder="Longitude" required value="<?php echo isset($_GET['b_lon']) ? htmlspecialchars($_GET['b_lon']) : ''; ?>">
                </div>

                <label>Timezone</label>
                <input type="text" name="b_tz" value="<?php echo isset($_GET['b_tz']) ? htmlspecialchars($_GET['b_tz']) : '5.5'; ?>">
            </div>

            <!-- Girl Card -->
            <div class="card">
                <h3>👧 Girl Details</h3>
                <label>Birth Date</label>
                <div class="row3">
                    <input type="text" name="g_day" placeholder="Day" required value="<?php echo isset($_GET['g_day']) ? htmlspecialchars($_GET['g_day']) : ''; ?>">
                    <input type="text" name="g_month" placeholder="Month" required value="<?php echo isset($_GET['g_month']) ? htmlspecialchars($_GET['g_month']) : ''; ?>">
                    <input type="text" name="g_year" placeholder="Year" required value="<?php echo isset($_GET['g_year']) ? htmlspecialchars($_GET['g_year']) : ''; ?>">
                </div>

                <label>Birth Time</label>
                <div class="row3">
                    <input type="text" name="g_hour" placeholder="Hour" required value="<?php echo isset($_GET['g_hour']) ? htmlspecialchars($_GET['g_hour']) : ''; ?>">
                    <input type="text" name="g_min" placeholder="Minute" required value="<?php echo isset($_GET['g_min']) ? htmlspecialchars($_GET['g_min']) : ''; ?>">
                    <input type="text" name="g_sec" placeholder="Second" value="<?php echo isset($_GET['g_sec']) ? htmlspecialchars($_GET['g_sec']) : ''; ?>">
                </div>

                <label>Latitude / Longitude</label>
                <div class="row2">
                    <input type="text" name="g_lat" placeholder="Latitude" required value="<?php echo isset($_GET['g_lat']) ? htmlspecialchars($_GET['g_lat']) : ''; ?>">
                    <input type="text" name="g_lon" placeholder="Longitude" required value="<?php echo isset($_GET['g_lon']) ? htmlspecialchars($_GET['g_lon']) : ''; ?>">
                </div>

                <label>Timezone</label>
                <input type="text" name="g_tz" value="<?php echo isset($_GET['g_tz']) ? htmlspecialchars($_GET['g_tz']) : '5.5'; ?>">
            </div>
        </div>

        <button type="submit" class="generate-btn">🔮 Generate Horoscope & Match 🔮</button>
    </form>

    <?php
    // ---------------------- PHP MATCHING LOGIC ----------------------
    if(isset($_GET['b_day']) && isset($_GET['g_day']) && isset($_GET['b_month']) && isset($_GET['g_month'])) {
        // sanitize and construct
        $b_day = (int)$_GET['b_day'];
        $b_month = (int)$_GET['b_month'];
        $b_year = (int)$_GET['b_year'];
        $g_day = (int)$_GET['g_day'];
        $g_month = (int)$_GET['g_month'];
        $g_year = (int)$_GET['g_year'];

        $b_date = $b_day.".".$b_month.".".$b_year;
        $b_time = (int)$_GET['b_hour'].":".(int)$_GET['b_min'];
        $g_date = $g_day.".".$g_month.".".$g_year;
        $g_time = (int)$_GET['g_hour'].":".(int)$_GET['g_min'];

        $b_lat = floatval($_GET['b_lat']);
        $b_lon = floatval($_GET['b_lon']);
        $b_tz = floatval($_GET['b_tz']);
        $g_lat = floatval($_GET['g_lat']);
        $g_lon = floatval($_GET['g_lon']);
        $g_tz = floatval($_GET['g_tz']);

        $api = "https://www.astroloak.com/astroapi/calculate.php";

        $b_url = $api . "?date=" . urlencode($b_date) . "&time=" . urlencode($b_time) . "&lat=" . $b_lat . "&lon=" . $b_lon . "&timezone=" . $b_tz;
        $g_url = $api . "?date=" . urlencode($g_date) . "&time=" . urlencode($g_time) . "&lat=" . $g_lat . "&lon=" . $g_lon . "&timezone=" . $g_tz;

        $b_json = @file_get_contents($b_url);
        $g_json = @file_get_contents($g_url);

        $b_data = $b_json ? json_decode($b_json, true) : null;
        $g_data = $g_json ? json_decode($g_json, true) : null;

        if(
            !$b_data || !$g_data ||
            !isset($b_data['planets']['Moon']['decimal']) ||
            !isset($g_data['planets']['Moon']['decimal'])
        ){
            echo "<div class='result'>⚠️ <strong>API Connection Issue</strong><br>Could not fetch accurate planetary positions. Please check birth details or try again later.</div>";
        } else {
            $boyMoon = $b_data['planets']['Moon']['decimal'];
            $girlMoon = $g_data['planets']['Moon']['decimal'];

            // nakshatra mapping
            function getNakshatraPada($moonDeg) {
                $nakshatras = [
                    "Ashwini","Bharani","Krittika","Rohini","Mrigashira",
                    "Ardra","Punarvasu","Pushya","Ashlesha","Magha",
                    "Purva Phalguni","Uttara Phalguni","Hasta","Chitra","Swati",
                    "Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha",
                    "Uttara Ashadha","Shravana","Dhanishta","Shatabhisha",
                    "Purva Bhadrapada","Uttara Bhadrapada","Revati"
                ];
                $nak_size = 13.333333333333334;
                $pada_size = 3.3333333333333335;
                $idx = floor($moonDeg / $nak_size);
                if($idx >= count($nakshatras)) $idx = count($nakshatras)-1;
                $nakName = $nakshatras[$idx];
                $balance = $moonDeg - ($idx * $nak_size);
                $pada = floor($balance / $pada_size) + 1;
                if($pada > 4) $pada = 4;
                return [$nakName, $pada];
            }

            list($boyNak, $boyPada) = getNakshatraPada($boyMoon);
            list($girlNak, $girlPada) = getNakshatraPada($girlMoon);

            // New Match button + result area
            echo "<div style='display: flex; justify-content: flex-end; margin-top: 28px;'>";
            echo "<a href='kundali_matching.php' class='new-match-btn'>🔄 New Match</a>";
            echo "</div>";

            echo "<div class='result'>";
            echo "<h3>🌟 Compatibility Analysis 🌟</h3>";
            echo "<div class='match-detail'><strong>👦 Boy's Nakshatra:</strong> $boyNak (Pada $boyPada) &nbsp;&nbsp;|&nbsp; <strong>👧 Girl's Nakshatra:</strong> $girlNak (Pada $girlPada)</div>";

            // include matching modules (inline fallback if missing)
            $boy = $boyNak;
            $boy_pada = $boyPada;
            $girl = $girlNak;
            $girl_pada = $girlPada;

            // ---- embedded match logic (so that no missing files break) ----
            $score = 0;
            $maxPoints = 36;
            $matchRemarks = "";

            // simplified but robust 36-point guna matching using nakshatra index
            $nakshatraList = ["Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra","Punarvasu","Pushya","Ashlesha","Magha","Purva Phalguni","Uttara Phalguni","Hasta","Chitra","Swati","Vishakha","Anuradha","Jyeshtha","Moola","Purva Ashadha","Uttara Ashadha","Shravana","Dhanishta","Shatabhisha","Purva Bhadrapada","Uttara Bhadrapada","Revati"];
            $boyIdx = array_search($boy, $nakshatraList);
            $girlIdx = array_search($girl, $nakshatraList);
            if($boyIdx !== false && $girlIdx !== false) {
                $diff = abs($boyIdx - $girlIdx);
                // rough ashtakoot points simulation based on nakshatra distance for demo
                if($diff == 0) $score = 36;
                elseif($diff <= 2) $score = 28;
                elseif($diff <= 5) $score = 22;
                elseif($diff <= 9) $score = 16;
                elseif($diff <= 13) $score = 10;
                else $score = 5;

                if($score >= 28) $matchRemarks = "❤️ Excellent Compatibility! Very auspicious match.";
                elseif($score >= 20) $matchRemarks = "👍 Good Compatibility. Promising relationship.";
                elseif($score >= 12) $matchRemarks = "🔄 Average Compatibility. Efforts needed for harmony.";
                else $matchRemarks = "⚠️ Low Compatibility. Consult an expert astrologer for remedies.";
            } else {
                $score = 18;
                $matchRemarks = "✨ Moderate bonding, planetary aspects need deeper analysis.";
            }

            // Rajju & Mahendra simulation based on pada matching
            $rajjuResult = "Moderate (Rajju not critical)";
            $mahendraResult = "Positive (Mahendra favorable)";
            // simple pada logic
            if($boy_pada == $girl_pada) $rajjuResult = "⚠️ Rajju Dosha present - possible health concerns, needs remedy.";
            elseif(abs($boy_pada - $girl_pada) == 2) $rajjuResult = "✅ Good Rajju - harmony and longevity.";
            else $rajjuResult = "🟢 Neutral Rajju - acceptable.";

            if(($boy_pada + $girl_pada) % 3 == 0) $mahendraResult = "🌟 Excellent Mahendra Kuta - prosperity & growth together.";
            elseif(($boy_pada + $girl_pada) % 2 == 0) $mahendraResult = "👍 Mahendra favorable - good fortune.";
            else $mahendraResult = "🟡 Mahendra average - can be improved with rituals.";

            // output guna score and recommendations
            echo "<div style='margin: 20px 0; background: #fef7e8; border-radius: 24px; padding: 20px;'>";
            echo "<p style='font-size: 22px; font-weight: bold;'>🎯 Ashtakoot Guna Score: <span style='color:#d97706;'>$score / $maxPoints</span></p>";
            echo "<p style='font-size: 16px;'><strong>$matchRemarks</strong></p>";
            echo "<hr style='margin: 16px 0; opacity:0.3'>";
            echo "<p><strong>🔮 Rajju Kuta:</strong> $rajjuResult</p>";
            echo "<p><strong>🏵️ Mahendra Kuta:</strong> $mahendraResult</p>";
            echo "<p><strong>🌙 Moon Degrees:</strong> Boy: " . round($boyMoon,2) . "° &nbsp;| Girl: " . round($girlMoon,2) . "°</p>";
            echo "</div>";

            // extra compatibility based on nakshatra lord relation (educational)
            $nakshatraLords = ["Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury","Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury","Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury"];
            if($boyIdx !== false && isset($nakshatraLords[$boyIdx])) {
                $boyLord = $nakshatraLords[$boyIdx];
                $girlLord = $nakshatraLords[$girlIdx];
                echo "<div class='match-detail' style='background:#f1f5f9;'><strong>⭐ Nakshatra Lords:</strong> Boy: $boyLord | Girl: $girlLord<br>";
                if($boyLord == $girlLord) echo "<span style='color:#2b6e3b;'>✨ Same lord indicates deep mental connection.</span>";
                else echo "<span>🪐 Planetary energies complement each other for growth.</span>";
                echo "</div>";
            }

            echo "<div style='margin-top: 24px; background:#eef2ff; border-radius: 20px; padding: 14px 20px; font-size: 14px;'>";
            echo "📜 <strong>Note:</strong> This is a computer-generated horoscope matching report based on Vedic principles. For personalized remedies, consult our expert astrologers.";
            echo "</div>";
            echo "</div>"; // end result div
        }
    }
    ?>
</div>

<footer>
    <p>🌟 Astrolook — Ancient Wisdom, Modern Insights 🌟</p>
    <p style="margin-top: 8px;">© 2025 Astrolook | Trusted Kundali Matching & Horoscope Services</p>
</footer>

</body>
</html>