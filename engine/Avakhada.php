<?php

class Avakhada {

    private static $nakshatras = [

        ["Ashwini","Ketu","Deva","Adi","Kshatriya","Ashwa","Fire","Chu Che Cho La"],
        ["Bharani","Venus","Manushya","Madhya","Vaishya","Gaja","Earth","Li Lu Le Lo"],
        ["Krittika","Sun","Rakshasa","Antya","Kshatriya","Sheep","Fire","A E U Ea"],
        ["Rohini","Moon","Manushya","Adi","Vaishya","Serpent","Earth","O Va Vi Vu"],
        ["Mrigashira","Mars","Deva","Madhya","Brahmin","Serpent","Air","Ve Vo Ka Ki"],
        ["Ardra","Rahu","Manushya","Antya","Shudra","Dog","Water","Ku Gha Ing Cha"],
        ["Punarvasu","Jupiter","Deva","Adi","Kshatriya","Cat","Water","Ke Ko Ha Hi"],
        ["Pushya","Saturn","Deva","Madhya","Kshatriya","Sheep","Water","Hu He Ho Da"],
        ["Ashlesha","Mercury","Rakshasa","Antya","Brahmin","Cat","Water","De Du De Do"],
        ["Magha","Ketu","Rakshasa","Adi","Kshatriya","Rat","Fire","Ma Mi Mu Me"],
        ["Purva Phalguni","Venus","Manushya","Madhya","Brahmin","Rat","Fire","Mo Ta Ti Tu"],
        ["Uttara Phalguni","Sun","Manushya","Antya","Kshatriya","Cow","Fire","Te To Pa Pi"],
        ["Hasta","Moon","Deva","Adi","Vaishya","Buffalo","Earth","Pu Sha Na Tha"],
        ["Chitra","Mars","Rakshasa","Madhya","Kshatriya","Tiger","Fire","Pe Po Ra Ri"],
        ["Swati","Rahu","Deva","Antya","Vaishya","Buffalo","Air","Ru Re Ro Ta"],
        ["Vishakha","Jupiter","Rakshasa","Adi","Kshatriya","Tiger","Fire","Ti Tu Te To"],
        ["Anuradha","Saturn","Deva","Madhya","Shudra","Deer","Water","Na Ni Nu Ne"],
        ["Jyeshtha","Mercury","Rakshasa","Antya","Brahmin","Deer","Water","No Ya Yi Yu"],
        ["Mula","Ketu","Rakshasa","Adi","Brahmin","Dog","Fire","Ye Yo Bha Bhi"],
        ["Purva Ashadha","Venus","Manushya","Madhya","Kshatriya","Monkey","Water","Bu Dha Bha Dha"],
        ["Uttara Ashadha","Sun","Manushya","Antya","Kshatriya","Mongoose","Earth","Bhe Bho Ja Ji"],
        ["Shravana","Moon","Deva","Adi","Vaishya","Monkey","Earth","Ju Je Jo Gha"],
        ["Dhanishta","Mars","Rakshasa","Madhya","Kshatriya","Lion","Earth","Ga Gi Gu Ge"],
        ["Shatabhisha","Rahu","Rakshasa","Antya","Vaishya","Horse","Air","Go Sa Si Su"],
        ["Purva Bhadrapada","Jupiter","Manushya","Adi","Brahmin","Lion","Fire","Se So Da Di"],
        ["Uttara Bhadrapada","Saturn","Manushya","Madhya","Kshatriya","Cow","Water","Du Tha Jha Da"],
        ["Revati","Mercury","Deva","Antya","Brahmin","Elephant","Water","De Do Cha Chi"]
    ];

    public static function calculate($moonLongitude) {

        $nakIndex = floor($moonLongitude / 13.333333);
        $nakOffset = $moonLongitude % 13.333333;
        $pada = floor($nakOffset / 3.333333) + 1;

        $nak = self::$nakshatras[$nakIndex];

        return [
            "Nakshatra" => $nak[0],
            "Nakshatra Lord" => $nak[1],
            "Gan" => $nak[2],
            "Nadi" => $nak[3],
            "Varna" => $nak[4],
            "Yoni" => $nak[5],
            "Tatva" => $nak[6],
            "Name Alphabet" => $nak[7],
            "Pada" => $pada
        ];
    }
}
