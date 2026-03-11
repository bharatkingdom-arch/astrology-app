
<?php

class PRCoreEngine
{
    public static function calculate($data)
    {
        if (!isset($data['planets'])) {
            return [];
        }

        $planets = $data['planets'];
        $houses  = $data['houses'] ?? [];

        /* ================= ZODIAC DATA ================= */

        $SIGNS = [
            "Aries","Taurus","Gemini","Cancer",
            "Leo","Virgo","Libra","Scorpio",
            "Sagittarius","Capricorn","Aquarius","Pisces"
        ];

        $SIGN_LORD = [
            "Aries"=>"Mars","Taurus"=>"Venus","Gemini"=>"Mercury","Cancer"=>"Moon",
            "Leo"=>"Sun","Virgo"=>"Mercury","Libra"=>"Venus","Scorpio"=>"Mars",
            "Sagittarius"=>"Jupiter","Capricorn"=>"Saturn",
            "Aquarius"=>"Saturn","Pisces"=>"Jupiter"
        ];

        $MOVABLE = ["Aries","Cancer","Libra","Capricorn"];
        $FIXED   = ["Taurus","Leo","Scorpio","Aquarius"];
        $DUAL    = ["Gemini","Virgo","Sagittarius","Pisces"];

        $NAK_NAMES = [
            "Ashwini","Bharani","Krittika","Rohini","Mrigashira",
            "Ardra","Punarvasu","Pushya","Ashlesha",
            "Magha","P-phalguni","U-phalguni",
            "Hasta","Chitra","Swati","Vishakha",
            "Anuradha","Jyeshtha","Moola",
            "P-ashadha","U-ashadha",
            "Shravana","Dhanishta","Shatabhisha",
            "P-bhadra","U-bhadra","Revati"
        ];

        $NAK_LORDS = [
            "Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury",
            "Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury",
            "Ketu","Venus","Sun","Moon","Mars","Rahu","Jupiter","Saturn","Mercury"
        ];

        /* ================= NAVAMSA FUNCTIONS ================= */

        $getNavamsaSign = function($nakIndex,$padaIndex) use ($SIGNS) {
            return $SIGNS[(($nakIndex*4)+$padaIndex)%12];
        };

        $getNavamsaStart = function($sign) use ($SIGNS,$MOVABLE,$FIXED,$DUAL) {
            $index = array_search($sign,$SIGNS);
            if(in_array($sign,$MOVABLE)) $offset=0;
            elseif(in_array($sign,$FIXED)) $offset=8;
            else $offset=4;
            return $SIGNS[($index+$offset)%12];
        };

        $generateNavamsaSequence = function($sign) use ($SIGNS,$getNavamsaStart) {
            $start = $getNavamsaStart($sign);
            $startIndex = array_search($start,$SIGNS);
            $seq=[];
            for($i=0;$i<9;$i++){
                $seq[]=$SIGNS[($startIndex+$i)%12];
            }
            return $seq;
        };

        $generatePR81 = function($nakIndex,$padaIndex) 
            use ($SIGN_LORD,$generateNavamsaSequence,$getNavamsaSign) {

            $navSign  = $getNavamsaSign($nakIndex,$padaIndex);
            $mainLord = $SIGN_LORD[$navSign];

            $subSigns = $generateNavamsaSequence($navSign);

            $result=[];
            $part=1;

            foreach($subSigns as $subSign){

                $subLord=$SIGN_LORD[$subSign];
                $subSubSigns = $generateNavamsaSequence($subSign);

                foreach($subSubSigns as $subSubSign){

                    $result[$part]=[
                        "main"=>$mainLord,
                        "sub"=>$subLord,
                        "subsub"=>$SIGN_LORD[$subSubSign]
                    ];
                    $part++;
                }
            }
            return $result;
        };

        /* ================= FULL PR ANALYSIS ================= */

        $getFullPR = function($lon)
            use ($SIGNS,$SIGN_LORD,$NAK_NAMES,$NAK_LORDS,$generatePR81) {

            $signIndex = floor($lon/30);
            $signName  = $SIGNS[$signIndex];
            $signLord  = $SIGN_LORD[$signName];

            $nakSize=360/27;
            $padaSize=$nakSize/4;
            $partSize=$padaSize/81;

            $nakIndex=floor($lon/$nakSize);
            $nakName=$NAK_NAMES[$nakIndex];
            $starLord=$NAK_LORDS[$nakIndex];

            $withinNak=fmod($lon,$nakSize);
            $padaIndex=floor($withinNak/$padaSize)+1;

            $withinPada=fmod($withinNak,$padaSize);
            $partIndex=floor($withinPada/$partSize)+1;

            $pr81=$generatePR81($nakIndex,$padaIndex-1);

            return [
                "longitude"=>round($lon,6),
                "sign"=>$signName,
                "signLord"=>$signLord,
                "nak"=>$nakName,
                "star"=>$starLord,
                "pada"=>$padaIndex,
                "part"=>$partIndex,
                "main"=>$pr81[$partIndex]["main"] ?? '',
                "sub"=>$pr81[$partIndex]["sub"] ?? '',
                "subsub"=>$pr81[$partIndex]["subsub"] ?? ''
            ];
        };

        /* ================= ADD PR PLANET ================= */

        if (isset($planets['Sun']['decimal'])) {
            $sunLon = (float)$planets['Sun']['decimal'];
            $prLon = $sunLon - 30;
            if ($prLon < 0) $prLon += 360;

            $planets['PR'] = ['decimal'=>$prLon];
        }

        /* ================= BUILD PLANET TABLE ================= */

        $planetTable = [];

        foreach ($planets as $planet=>$info) {

            if (!isset($info['decimal'])) continue;

            $planetTable[$planet] =
                $getFullPR((float)$info['decimal']);
        }

        /* ================= BUILD HOUSE TABLE ================= */

        $houseTable = [];

        foreach ($houses as $house=>$info) {

            if (!isset($info['decimal'])) continue;

            $houseTable[$house] =
                $getFullPR((float)$info['decimal']);
        }

        return [
            'planet_table' => $planetTable,
            'houses'       => $houseTable
        ];
    }
}
