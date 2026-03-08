<?php

class Panchanga {

    public static function calculate($sun, $moon, $jd) {

        // Normalize
        $sun  = fmod($sun, 360);
        $moon = fmod($moon, 360);

        if ($sun < 0)  $sun += 360;
        if ($moon < 0) $moon += 360;

        // =====================
        // TITHI
        // =====================
        $diff = $moon - $sun;
        if ($diff < 0) $diff += 360;

        $tithi_index = floor($diff / 12) + 1;

        $paksha = ($tithi_index <= 15) ? "Shukla" : "Krishna";
        $tithi_number = ($tithi_index <= 15) ? $tithi_index : $tithi_index - 15;

        $tithi_names = [
            1=>"Pratipada",2=>"Dvitiya",3=>"Tritiya",4=>"Chaturthi",
            5=>"Panchami",6=>"Shashti",7=>"Saptami",8=>"Ashtami",
            9=>"Navami",10=>"Dashami",11=>"Ekadashi",12=>"Dwadashi",
            13=>"Trayodashi",14=>"Chaturdashi",15=>"Purnima/Amavasya"
        ];

        $tithi = $paksha . " " . $tithi_names[$tithi_number];

        // =====================
        // NAKSHATRA
        // =====================
        $nak_index = floor($moon / (13 + 1/3)) + 1;

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

        $nakshatra = $nakshatras[$nak_index];

        // =====================
        // YOGA
        // =====================
        $sum = $sun + $moon;
        if ($sum >= 360) $sum -= 360;

        $yoga_index = floor($sum / (13 + 1/3)) + 1;

        $yogas = [
            1=>"Vishkumbha",2=>"Priti",3=>"Ayushman",4=>"Saubhagya",
            5=>"Shobhana",6=>"Atiganda",7=>"Sukarman",8=>"Dhriti",
            9=>"Shoola",10=>"Ganda",11=>"Vriddhi",12=>"Dhruva",
            13=>"Vyaghata",14=>"Harshana",15=>"Vajra",
            16=>"Siddhi",17=>"Vyatipata",18=>"Variyana",
            19=>"Parigha",20=>"Shiva",21=>"Siddha",
            22=>"Sadhya",23=>"Shubha",24=>"Shukla",
            25=>"Brahma",26=>"Indra",27=>"Vaidhriti"
        ];

        $yoga = $yogas[$yoga_index];

        // =====================
        // KARANA
        // =====================
        $karana_index = floor($diff / 6);

        $karanas = [
            "Bava","Balava","Kaulava","Taitila","Garija",
            "Vanija","Vishti","Bava","Balava","Kaulava",
            "Taitila","Garija","Vanija","Vishti"
        ];

        $karana = $karanas[$karana_index % 7];

        // =====================
        // VARA
        // =====================
        $weekday_index = floor($jd + 1.5) % 7;

        $weekdays = [
            "Sunday","Monday","Tuesday",
            "Wednesday","Thursday","Friday","Saturday"
        ];

        $vara = $weekdays[$weekday_index];

        return [
            "Tithi" => $tithi,
            "Nakshatra" => $nakshatra,
            "Yoga" => $yoga,
            "Karana" => $karana,
            "Vara" => $vara
        ];
    }
}
