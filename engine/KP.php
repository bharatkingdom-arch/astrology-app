<?php

class KP
{
    /* ================= NAKSHATRA DATA ================= */

    private static $nakshatraNames = [
        "Ashwini","Bharani","Krittika","Rohini","Mrigashira","Ardra",
        "Punarvasu","Pushya","Ashlesha","Magha","Purva Phalguni","Uttara Phalguni",
        "Hasta","Chitra","Swati","Vishakha","Anuradha","Jyeshtha",
        "Mula","Purva Ashadha","Uttara Ashadha","Shravana","Dhanishta",
        "Shatabhisha","Purva Bhadrapada","Uttara Bhadrapada","Revati"
    ];

    private static $nakshatraLords = [
        "Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury",
        "Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury",
        "Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury"
    ];

    private static $vimshottari = [
        "Ketu"    => 7,
        "Venus"   => 20,
        "Sun"     => 6,
        "Moon"    => 10,
        "Mars"    => 7,
        "Rahu"    => 18,
        "Jupiter" => 16,
        "Saturn"  => 19,
        "Mercury" => 17
    ];

    /* =================================================== */

    public static function calculateAll($planets)
    {
        $results = [];

        foreach ($planets as $planet => $planetData) {

            $decimal = self::extractDecimal($planetData);

            $results[$planet] = self::calculateSingle($decimal);
        }

        return $results;
    }

    /* =================================================== */

    private static function extractDecimal($planetData)
    {
        if (is_array($planetData) && isset($planetData['decimal'])) {
            return floatval($planetData['decimal']);
        }

        return floatval($planetData);
    }

    /* =================================================== */

    private static function calculateSingle($longitude)
    {
        $longitude = floatval($longitude);

        // Normalize safely to 0–360
        $longitude = fmod($longitude, 360);
        if ($longitude < 0) {
            $longitude += 360;
        }

        // Fix boundary case like 360.0000
        if ($longitude == 360) {
            $longitude = 0;
        }

        $nakSize = 13 + (1/3); // 13°20'
        $nakNo = floor($longitude / $nakSize);

        if ($nakNo > 26) {
            $nakNo = 26;
        }

        $nakName  = self::$nakshatraNames[$nakNo];
        $starLord = self::$nakshatraLords[$nakNo];

        /* ================= DEGREE INSIDE NAKSHATRA ================= */

        $nakStart      = $nakNo * $nakSize;
        $balanceDeg    = $longitude - $nakStart;
        $balanceMin    = $balanceDeg * 60;  // convert to minutes
        $nakTotalMin   = 800;               // 13°20' = 800 minutes

        /* ================= SUB LORD ================= */

        $subLord = self::findSub($balanceMin, $nakTotalMin);

        /* ================= SUB-SUB LORD ================= */

        $subSubLord = self::findSubSub($balanceMin, $subLord, $nakTotalMin);

        return [
            "longitude"    => round($longitude, 6),
            "nakshatra"    => $nakName,
            "star_lord"    => $starLord,
            "sub_lord"     => $subLord,
            "sub_sub_lord" => $subSubLord
        ];
    }

    /* =================================================== */

    private static function findSub($balanceMin, $totalMin)
    {
        $acc = 0;

        foreach (self::$vimshottari as $planet => $years) {

            $portion = ($years / 120) * $totalMin;

            if ($balanceMin >= $acc && $balanceMin < ($acc + $portion)) {
                return $planet;
            }

            $acc += $portion;
        }

        return "";
    }

    /* =================================================== */

    private static function findSubSub($balanceMin, $subLord, $totalMin)
    {
        if (!isset(self::$vimshottari[$subLord])) {
            return "";
        }

        // Find sub start position
        $subStart = 0;

        foreach (self::$vimshottari as $planet => $years) {

            $portion = ($years / 120) * $totalMin;

            if ($planet == $subLord) break;

            $subStart += $portion;
        }

        $withinSub = $balanceMin - $subStart;

        $subYears   = self::$vimshottari[$subLord];
        $subPortion = ($subYears / 120) * $totalMin;

        $acc = 0;

        foreach (self::$vimshottari as $planet => $years) {

            $portion = ($years / 120) * $subPortion;

            if ($withinSub >= $acc && $withinSub < ($acc + $portion)) {
                return $planet;
            }

            $acc += $portion;
        }

        return "";
    }
}
