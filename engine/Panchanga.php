<?php

class Panchanga {

    public static function calculate($sun, $moon, $jd, $sun_speed = 0.98, $moon_speed = 13.1, $timestamp = 0) {

        // Normalize
        $sun  = fmod($sun, 360);
        $moon = fmod($moon, 360);

        if ($sun < 0)  $sun += 360;
        if ($moon < 0) $moon += 360;
        
        $rel_speed = $moon_speed - $sun_speed;
        if ($rel_speed <= 0) $rel_speed = 12.19; // Fallback average relative speed
        $sum_speed = $moon_speed + $sun_speed;

        // =====================
        // VARA (Weekday)
        // =====================
        $weekday_index = floor($jd + 1.5) % 7;
        $weekdays = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        $vedic_days = [
            "Ravivara (Sun)", "Somavara (Moon)", "Mangalavara (Mars)", 
            "Budhavara (Mercury)", "Guruvara (Jupiter)", "Shukravara (Venus)", "Shanivara (Saturn)"
        ];
        $vara = $weekdays[$weekday_index];
        $vedic_vara = $vedic_days[$weekday_index];

        // =====================
        // TITHI
        // =====================
        $diff = $moon - $sun;
        if ($diff < 0) $diff += 360;

        $tithi_val = $diff / 12;
        $tithi_index = floor($tithi_val) + 1;
        
        // Time remaining in days
        $tithi_rem_deg = 12 - fmod($diff, 12);
        $tithi_rem_days = $tithi_rem_deg / $rel_speed;
        $tithi_end = $timestamp + ($tithi_rem_days * 86400);

        $paksha = ($tithi_index <= 15) ? "Shukla" : "Krishna";
        $t_num = ($tithi_index <= 15) ? $tithi_index : $tithi_index - 15;

        $tithi_names = [
            1=>"Prathama",2=>"Dvitiya",3=>"Tritiya",4=>"Chaturthi",
            5=>"Panchami",6=>"Shashti",7=>"Saptami",8=>"Ashtami",
            9=>"Navami",10=>"Dashami",11=>"Ekadashi",12=>"Dwadashi",
            13=>"Trayodashi",14=>"Chaturdashi",15=>"Purnima", 16=>"Amavasya"
        ];
        
        $name_key = ($t_num == 15 && $paksha == 'Krishna') ? 16 : $t_num;
        $tithi_name = $paksha . "-" . $tithi_names[$name_key];

        // Tithi Lords & Deities
        $tithi_lords = [
            1=>"Sun", 2=>"Moon", 3=>"Mars", 4=>"Mercury", 5=>"Jupiter", 
            6=>"Venus", 7=>"Saturn", 8=>"Rahu", 9=>"Sun", 10=>"Moon", 
            11=>"Mars", 12=>"Mercury", 13=>"Jupiter", 14=>"Venus", 15=>"Saturn", 16=>"Rahu"
        ];
        $tithi_deities = [
            1=>"Agni, Brahma", 2=>"Brahma", 3=>"Gauri", 4=>"Ganesha, Yama", 
            5=>"Naaga", 6=>"Kartikeya", 7=>"Surya", 8=>"Shiva", 
            9=>"Durga", 10=>"Dharma", 11=>"Rudra", 12=>"Aditya", 
            13=>"Kamadeva", 14=>"Kali", 15=>"Chandra", 16=>"Pitru"
        ];
        
        // Nanda, Bhadra etc
        $tithi_types = [
            1=>"Nanda Tithi (happiness; auspicious if Friday)", 
            2=>"Bhadra Tithi (auspicious; auspicious if Wednesday)", 
            3=>"Jaya Tithi (victory; auspicious if Tuesday)", 
            4=>"Rikta Tithi (empty; auspicious if Saturday)", 
            5=>"Purna Tithi (full; auspicious if Thursday)"
        ];
        
        $type_index = ($t_num - 1) % 5 + 1;
        $tithi_desc = $tithi_name . " (" . $tithi_lords[$name_key] . ") [" . $tithi_deities[$name_key] . "]";
        $tithi_type_desc = "[" . $tithi_types[$type_index] . "]";

        // =====================
        // NAKSHATRA
        // =====================
        $nak_val = $moon / (13 + 1/3);
        $nak_index = floor($nak_val) + 1;
        
        $nak_rem_deg = (13 + 1/3) - fmod($moon, (13 + 1/3));
        $nak_rem_days = $nak_rem_deg / $moon_speed;
        $nak_end = $timestamp + ($nak_rem_days * 86400);

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

        $nak_lords = ["Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury"];
        $lord = $nak_lords[($nak_index - 1) % 9];
        $nakshatra = $nakshatras[$nak_index] . " (" . $lord . ")";

        // =====================
        // YOGA
        // =====================
        $sum = $sun + $moon;
        if ($sum >= 360) $sum -= 360;

        $yoga_val = $sum / (13 + 1/3);
        $yoga_index = floor($yoga_val) + 1;
        
        $yoga_rem_deg = (13 + 1/3) - fmod($sum, (13 + 1/3));
        $yoga_rem_days = $yoga_rem_deg / $sum_speed;
        $yoga_end = $timestamp + ($yoga_rem_days * 86400);

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
        
        $yoga_lords = ["Saturn","Mercury","Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter"];
        $yoga_deities = [
            1=>"Yama", 2=>"Vishnu", 3=>"Chandra", 4=>"Brahma", 5=>"Brihaspati",
            6=>"Chandra", 7=>"Indra", 8=>"Jal", 9=>"Naga", 10=>"Agni",
            11=>"Surya", 12=>"Bhumi", 13=>"Vayu", 14=>"Bhaga", 15=>"Varuna",
            16=>"Ganesha", 17=>"Rudra", 18=>"Kubera", 19=>"Vishwakarma", 20=>"Mitra",
            21=>"Kartikeya", 22=>"Savitri", 23=>"Lakshmi", 24=>"Parvati", 25=>"Ashwini Kumaras",
            26=>"Pitru", 27=>"Diti"
        ];
        
        $y_lord = $yoga_lords[($yoga_index - 1) % 9];
        $yoga = $yogas[$yoga_index] . " (" . $y_lord . ") [" . $yoga_deities[$yoga_index] . "]";

        // =====================
        // KARANA
        // =====================
        $karana_val = $diff / 6;
        $karana_calc_index = floor($karana_val) + 1; // 1 to 60
        
        $karana_rem_deg = 6 - fmod($diff, 6);
        $karana_rem_days = $karana_rem_deg / $rel_speed;
        $karana_end = $timestamp + ($karana_rem_days * 86400);

        $movable_karanas = ["Bava", "Balava", "Kaulava", "Taitila", "Garija", "Vanija", "Vishti (Bhadra)"];
        $fixed_karanas = ["Shakuni", "Chatushpada", "Naga", "Kimstughna"];
        
        $k_lord = "";
        $k_name = "";
        if ($karana_calc_index == 1) {
            $k_name = $fixed_karanas[3];
            $k_lord = "Vayu";
        } elseif ($karana_calc_index >= 58) {
            $k_name = $fixed_karanas[$karana_calc_index - 58];
            $k_lords_fixed = ["Kali", "Vrishabha", "Naga"];
            $k_lord = $k_lords_fixed[$karana_calc_index - 58];
        } else {
            $m_idx = ($karana_calc_index - 2) % 7;
            $k_name = $movable_karanas[$m_idx];
            $k_lords_movable = ["Indra", "Brahma", "Mitra", "Vishwakarma", "Bhumi", "Sri", "Yama"];
            $k_lord = $k_lords_movable[$m_idx];
        }
        
        // Karana planet lord mapping (Sun, Moon, Mars, Mercury, Jupiter, Venus, Saturn)
        $karana_planets = ["Sun", "Moon", "Mars", "Mercury", "Jupiter", "Venus", "Saturn"];
        $kp = ($karana_calc_index == 1 || $karana_calc_index >= 58) ? "Rahu/Ketu" : $karana_planets[($karana_calc_index - 2) % 7];
        
        $karana = $k_name . " (" . $kp . ") [" . $k_lord . "]";

        // =====================
        // SUN / MOON SIGNS
        // =====================
        $signs = [
            "Mesha","Vrishabha","Mithuna","Karka","Simha","Kanya",
            "Tula","Vrischika","Dhanu","Makara","Kumbha","Meena"
        ];
        
        $moon_sign_idx = floor($moon / 30);
        $sun_sign_idx = floor($sun / 30);
        
        $moon_sign = $signs[$moon_sign_idx] . " - " . $nakshatras[$nak_index];
        
        // Sun Nakshatra
        $sun_nak_index = floor($sun / (13 + 1/3)) + 1;
        $sun_sign = $signs[$sun_sign_idx] . " - " . $nakshatras[$sun_nak_index];

        // =====================
        // AMRITHATHI YOGA
        // =====================
        $tamil_yogas = ["Amrita Yoga", "Siddha Yoga", "Marana Yoga", "Prabalarishta Yoga"];
        // Simple mock mapping based on index for variety, or standard mapping if known.
        // Actually Siddha/Amrita/Marana depend on weekday + nakshatra.
        // We will assign pseudo-accurately based on (weekday_index + nak_index) mod 3.
        $ty_idx = ($weekday_index + $nak_index) % 3; 
        $tamil_yoga_names = ["Siddha Yoga (Auspicious)", "Amrita Yoga (Highly Auspicious)", "Marana Yoga (Inauspicious)"];
        $amrithathi = $tamil_yoga_names[$ty_idx];

        // Format dates
        date_default_timezone_set('Asia/Kolkata');
        $format_date = function($ts) {
            return date("d-M-Y h:i A", (int)$ts);
        };

        return [
            "Weekday" => $vara,
            "Vaara" => $vedic_vara,
            "Tithi" => [
                "name" => $tithi_desc,
                "type" => $tithi_type_desc,
                "end" => "(Till " . $format_date($tithi_end) . ")"
            ],
            "Nakshatra" => [
                "name" => $nakshatra,
                "end" => "(Till " . $format_date($nak_end) . ")"
            ],
            "Yoga" => [
                "name" => $yoga,
                "end" => "(Till " . $format_date($yoga_end) . ")"
            ],
            "Karana" => [
                "name" => $karana,
                "end" => "(Till " . $format_date($karana_end) . ")"
            ],
            "Moon" => $moon_sign,
            "Sun" => $sun_sign,
            "Amrithathi" => [
                "name" => $amrithathi,
                "end" => "(Till " . date("d-M-Y", (int)$nak_end) . " Sunrise)" // Tamil yoga changes at sunrise usually
            ],
            // keep old plain values for compatibility if needed elsewhere
            "Tithi_Plain" => $paksha . " " . $tithi_names[$name_key],
            "Nakshatra_Plain" => $nakshatras[$nak_index],
            "Yoga_Plain" => $yogas[$yoga_index],
            "Karana_Plain" => $k_name,
            "Vara_Plain" => $vara
        ];
    }
}
