<?php
$nakshatra_data = [
"Dhanishta"=>["rasi"=>"Makara","lord"=>"Mars","vashya"=>"Chatushpada","yoni"=>"Lion","gana"=>"Rakshasa","nadi"=>"Madhya"]
];
function getRasiFromNakshatra($nak,$pada){
    switch($nak){
        case "Krittika": return ($pada==1)?"Mesha":"Vrishabha";
        case "Mrigashira": return ($pada<=2)?"Vrishabha":"Mithuna";
        case "Punarvasu": return ($pada<=3)?"Mithuna":"Karka";
        case "Uttara Phalguni": return ($pada==1)?"Simha":"Kanya";
        case "Chitra": return ($pada<=2)?"Kanya":"Tula";
        case "Vishakha": return ($pada<=3)?"Tula":"Vrischika";
        case "Uttara Ashadha": return ($pada==1)?"Dhanu":"Makara";
        case "Dhanishta": return ($pada<=2)?"Makara":"Kumbha";
        case "Purva Bhadrapada": return ($pada<=3)?"Kumbha":"Meena";
        default:
            global $nakshatra_data;
            return $nakshatra_data[$nak]['rasi'];
    }
}
echo getRasiFromNakshatra("Dhanishta", 4);
?>
