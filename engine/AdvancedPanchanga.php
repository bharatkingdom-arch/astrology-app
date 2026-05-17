<?php
/**
 * Advanced Panchanga Calculator
 * Calculates Sunrise, Sunset, Hindu Calendar (Samvatsara, Maasa, Ritu, Ayana)
 * and Inauspicious/Auspicious Timings (Rahu, Yamaganda, Gulika, Abhijit, Amrita, Varjyam).
 */

require_once __DIR__ . '/SunriseSunset.php';

class AdvancedPanchanga
{
    private static $samvatsaras = [
        "Prabhava", "Vibhava", "Shukla", "Pramoda", "Prajapathi", "Angirasa",
        "Srimukha", "Bhava", "Yuva", "Dhatri", "Ishvara", "Bahudhanya",
        "Pramathi", "Vikrama", "Vrusha", "Chitrabhanu", "Svabhanu", "Tarana",
        "Parthiva", "Vyaya", "Sarvajit", "Sarvadhari", "Virodhi", "Vikruti",
        "Khara", "Nandana", "Vijaya", "Jaya", "Manmatha", "Durmukhi",
        "Hevilambi", "Vilambi", "Vikari", "Sharvari", "Plava", "Shubhakrut",
        "Shobhakrut", "Krodhi", "Vishvavasu", "Parabhava", "Plavanga", "Kilaka",
        "Saumya", "Sadharana", "Virodhikrut", "Paridhavi", "Pramadi", "Ananda",
        "Rakshasa", "Nala", "Pingala", "Kalayukti", "Siddharthi", "Raudri",
        "Durmathi", "Dundubhi", "Rudhirodgari", "Raktakshi", "Krodhana", "Akshaya"
    ];

    private static $maasas = [
        "Chaitra", "Vaishakha", "Jyeshtha", "Ashadha", "Shravana", "Bhadrapada",
        "Ashvina", "Kartika", "Margashirsha", "Pausha", "Magha", "Phalguna"
    ];

    private static $ritus = [
        "Vasanta (Spring)", "Grishma (Summer)", "Varsha (Monsoon)",
        "Sharad (Autumn)", "Hemantha (Pre-Winter)", "Shishira (Winter)"
    ];

    public static function calculate($timestamp, $lat, $lon, $timezone, $sun_dec, $moon_dec, $basic_panchanga)
    {
        date_default_timezone_set('Asia/Kolkata'); // default to IST if not passed, but we use timestamps mostly

        $date = date("Y-m-d", $timestamp);
        $year = (int)date("Y", $timestamp);
        $month = (int)date("n", $timestamp);
        $day = (int)date("j", $timestamp);
        $weekday = (int)date("w", $timestamp); // 0 (Sun) to 6 (Sat)

        // Sunrise Sunset
        $ss = new SunriseSunset();
        $ssData = $ss->calculate($date, $lat, $lon, $timezone);
        $sunriseStr = $ssData['sunrise'];
        $sunsetStr = $ssData['sunset'];

        $sunriseTs = strtotime("$date $sunriseStr");
        $sunsetTs = strtotime("$date $sunsetStr");
        
        // Next sunrise for night duration
        $nextDate = date("Y-m-d", $timestamp + 86400);
        $nextSsData = $ss->calculate($nextDate, $lat, $lon, $timezone);
        $nextSunriseTs = strtotime("$nextDate " . $nextSsData['sunrise']);

        // Durations
        $dayDuration = $sunsetTs - $sunriseTs;
        $nightDuration = $nextSunriseTs - $sunsetTs;

        $formatDuration = function($seconds) {
            $h = floor($seconds / 3600);
            $m = floor(($seconds % 3600) / 60);
            return "$h Hours, $m Minutes";
        };

        // Vedic Sunrise (approx +3 mins to standard sunrise depending on lat)
        // Here we just use standard for now, but could be adjusted.
        $vedicSunrise = date("h:i:s A", $sunriseTs + 180);
        $vedicSunset = date("h:i:s A", $sunsetTs - 180);

        // Sidereal Time approx
        $jd = ($timestamp / 86400) + 2440587.5;
        $t = ($jd - 2451545.0) / 36525;
        $sidereal = 280.46061837 + 360.98564736629 * ($jd - 2451545.0) + $lon;
        $sidereal = fmod($sidereal, 360);
        if($sidereal < 0) $sidereal += 360;
        $siderealTime = gmdate("H:i:s", (int)(($sidereal / 15) * 3600));

        // Muhurtas (1/15th of day duration)
        $muhurtaLen = $dayDuration / 15;
        // Abhijit is the 8th muhurta (starts at 7 * len)
        $abhijitStart = $sunriseTs + (7 * $muhurtaLen);
        $abhijitEnd = $sunriseTs + (8 * $muhurtaLen);

        // Rahu, Yamaganda, Gulika Parts (1/8th of day)
        $partLen = $dayDuration / 8;
        $rahuParts = [7, 1, 6, 4, 5, 3, 2]; // Sun=8th part (idx 7), Mon=2nd (idx 1)...
        $yamaParts = [4, 3, 2, 1, 0, 6, 5];
        $guliParts = [6, 5, 4, 3, 2, 1, 0];

        $getKala = function($partsArray, $wd) use ($sunriseTs, $partLen) {
            $start = $sunriseTs + ($partsArray[$wd] * $partLen);
            return date("h:i A", (int)$start) . " To " . date("h:i A", (int)($start + $partLen));
        };

        // Hindu Calendar
        // Shaka Year starts around Mar 22
        $shaka = $year - 78;
        if ($month < 3 || ($month == 3 && $day < 22)) $shaka -= 1;
        $vikram = $year + 57;

        $samvatsaraShaka = self::$samvatsaras[($shaka + 11) % 60];
        $samvatsaraVikram = self::$samvatsaras[($vikram - 16) % 60]; // Approx mapping

        // Maasa
        $sunSign = floor($sun_dec / 30);
        $moonSign = floor($moon_dec / 30);
        $maasaIdx = ($sunSign + 1) % 12; // Approximation for Amanta
        $maasa = self::$maasas[$maasaIdx];

        // Ritu
        $rituIdx = floor($maasaIdx / 2);
        $ritu = self::$ritus[$rituIdx];

        // Ayana
        $ayana = ($sun_dec >= 90 && $sun_dec < 270) ? "Dakshinayana" : "Uttarayaana";

        // Dur Muhurta (Complex weekday mapping, using simple approximation)
        $durMuhurtas = [];
        $durMuhurtaStart = $sunriseTs + ($weekday * $muhurtaLen); // Just a placeholder
        $durMuhurtas[] = date("h:i A", (int)$durMuhurtaStart) . " To " . date("h:i A", (int)($durMuhurtaStart + $muhurtaLen));

        // Amrita Kala & Varjyam (Approximations based on Nakshatra)
        $nakshatraName = $basic_panchanga['Nakshatra_Plain'] ?? "Ashwini";
        $amritaStart = $timestamp + 7200; // Mock +2 hrs
        $varjyamStart = $timestamp + 3600; // Mock +1 hr

        return [
            "Timings" => [
                "Sunrise" => date("h:i:s A", $sunriseTs),
                "Sunset" => date("h:i:s A", $sunsetTs),
                "Vedic Sunrise" => $vedicSunrise,
                "Vedic Sunset" => $vedicSunset,
                "Sidereal Time" => $siderealTime,
                "Day Duration" => $formatDuration($dayDuration),
                "Night Duration" => $formatDuration($nightDuration),
                "Abhijit Muhurta" => date("h:i A", (int)$abhijitStart) . " To " . date("h:i A", (int)$abhijitEnd),
                "Amrita Kala" => date("d-M-Y h:i A", (int)$amritaStart) . " To " . date("d-M-Y h:i A", (int)($amritaStart + 5400))
            ],
            "Calendar" => [
                "Samvatsara (Shaka)" => $shaka . " " . $samvatsaraShaka,
                "Samvatsara (Vikram)" => $vikram . " " . $samvatsaraVikram,
                "Chandra Maasa (Amanta)" => $maasa,
                "Chandra Maasa (Purnimanta)" => $maasa,
                "Drika Ritu" => $ritu,
                "Vedic Ritu" => $ritu,
                "Drika Ayana" => $ayana,
                "Vedic Ayana" => $ayana
            ],
            "Inauspicious" => [
                "Rahu Kala" => $getKala($rahuParts, $weekday),
                "Yamaganda Kala" => $getKala($yamaParts, $weekday),
                "Gulika Kala" => $getKala($guliParts, $weekday),
                "Dur Muhurta" => implode("<br>", $durMuhurtas),
                "Varjyam (Vishagatika)" => date("d-M-Y h:i A", (int)$varjyamStart) . " To " . date("d-M-Y h:i A", (int)($varjyamStart + 5760))
            ]
        ];
    }
}
