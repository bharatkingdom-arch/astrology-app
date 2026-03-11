
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

/* ================= LOAD SESSION ================= */

$data = $_SESSION['kundli_data'] ?? null;

if (!$data || empty($data['planets'])) {
    echo "Please open Free Kundali first.";
    exit;
}

$planets = $data['planets'];

/* ================= SAFE BIRTH DATETIME ================= */

$birthDate = null;
$birthTime = null;

$dateKeys = ['birth_date','birthdate','dob','date'];
$timeKeys = ['birth_time','time','tob'];

foreach ($dateKeys as $k) {
    if (!empty($data[$k])) { $birthDate = $data[$k]; break; }
}
foreach ($timeKeys as $k) {
    if (!empty($data[$k])) { $birthTime = $data[$k]; break; }
}

if (isset($data['input'])) {
    foreach ($dateKeys as $k) {
        if (!empty($data['input'][$k])) { $birthDate = $data['input'][$k]; break; }
    }
    foreach ($timeKeys as $k) {
        if (!empty($data['input'][$k])) { $birthTime = $data['input'][$k]; break; }
    }
}

if (!$birthDate) $birthDate = date('Y-m-d');
if (!$birthTime) $birthTime = "00:00:00";

$birthDateTime = new DateTime("$birthDate $birthTime");

/* ================= PR LONGITUDE ================= */

if (!isset($planets['Sun']['decimal'])) {
    echo "Sun longitude missing.";
    exit;
}

$sunLon = (float)$planets['Sun']['decimal'];
$prLon = $sunLon - 30;
if ($prLon < 0) $prLon += 360;

/* ================= CONSTANTS ================= */

$nakSize  = 360 / 27;
$padaSize = $nakSize / 4;
$partSize = $padaSize / 81;

$secondsPerYear = 365.2425 * 86400;
$secondsPerPart = (int) round($partSize * $secondsPerYear);

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
"Magha","Purva Phalguni","Uttara Phalguni",
"Hasta","Chitra","Swati","Vishakha",
"Anuradha","Jyeshtha","Moola",
"Purvashadha","Uttarashadha",
"Shravana","Dhanishta","Shatabhisha",
"Purvabhadra","Uttarabhadra","Revati"
];

/* ================= PR81 FUNCTIONS ================= */

function getNavamsaSign($nakIndex,$padaIndex,$SIGNS){
    return $SIGNS[(($nakIndex*4)+$padaIndex)%12];
}

function getNavamsaStart($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL){
    $index = array_search($sign,$SIGNS);
    if(in_array($sign,$MOVABLE)) $offset=0;
    elseif(in_array($sign,$FIXED)) $offset=8;
    else $offset=4;
    return $SIGNS[($index+$offset)%12];
}

function generateNavamsaSequence($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL){
    $start = getNavamsaStart($sign,$SIGNS,$MOVABLE,$FIXED,$DUAL);
    $startIndex = array_search($start,$SIGNS);
    $seq=[];
    for($i=0;$i<9;$i++){
        $seq[]=$SIGNS[($startIndex+$i)%12];
    }
    return $seq;
}

function generatePR81($nakIndex,$padaIndex,$SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL){
    $navSign  = getNavamsaSign($nakIndex,$padaIndex,$SIGNS);
    $mainLord = $SIGN_LORD[$navSign];
    $subSigns = generateNavamsaSequence($navSign,$SIGNS,$MOVABLE,$FIXED,$DUAL);

    $result=[];
    $part=1;

    foreach($subSigns as $subSign){
        $subLord=$SIGN_LORD[$subSign];
        $subSubSigns = generateNavamsaSequence($subSign,$SIGNS,$MOVABLE,$FIXED,$DUAL);

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
}

/* ================= BUILD 120 YEAR TREE ================= */

$birthNakIndex = floor($prLon / $nakSize);
$nakStartLon = $birthNakIndex * $nakSize;

$distanceDegrees = $prLon - $nakStartLon;
if ($distanceDegrees < 0) $distanceDegrees += 360;

$elapsedSeconds = (int) round($distanceDegrees * $secondsPerYear);

$nakStartTime = clone $birthDateTime;
$nakStartTime->sub(new DateInterval('PT'.$elapsedSeconds.'S'));

$endTime = clone $nakStartTime;
$endTime->add(new DateInterval('P120Y'));

$currentLon  = $nakStartLon;
$currentTime = clone $nakStartTime;

$tree = [];

while ($currentTime < $endTime) {

    $nakIndex = floor($currentLon / $nakSize);
    $nakName  = $NAK_NAMES[$nakIndex];

    $withinNak = fmod($currentLon, $nakSize);
    $padaIndex = floor($withinNak / $padaSize) + 1;

    $withinPada = fmod($withinNak, $padaSize);
    $partIndex  = floor($withinPada / $partSize) + 1;

    $start = clone $currentTime;

    $pr81 = generatePR81($nakIndex,$padaIndex-1,$SIGNS,$SIGN_LORD,$MOVABLE,$FIXED,$DUAL);
    $lords = $pr81[$partIndex];

    $currentTime->add(new DateInterval('PT'.$secondsPerPart.'S'));
    $end = clone $currentTime;

    if (!isset($tree[$nakName])) {
        $tree[$nakName] = ['start'=>$start,'end'=>$end,'padas'=>[]];
    } else {
        $tree[$nakName]['end'] = $end;
    }

    if (!isset($tree[$nakName]['padas'][$padaIndex])) {
        $tree[$nakName]['padas'][$padaIndex] = ['start'=>$start,'end'=>$end,'parts'=>[]];
    } else {
        $tree[$nakName]['padas'][$padaIndex]['end'] = $end;
    }

    $tree[$nakName]['padas'][$padaIndex]['parts'][] = [
        'part'=>$partIndex,
        'start'=>$start,
        'end'=>$end,
        'lords'=>$lords
    ];

    $currentLon += $partSize;
    if ($currentLon >= 360) $currentLon -= 360;
}

require '../header.php';
?>

<style>
.pr-container{max-width:1200px;margin:30px auto;font-family:Arial;}
.pr-card{background:#fff;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.08);margin-bottom:18px;border:1px solid #eee;}
.pr-header{padding:15px 20px;cursor:pointer;font-weight:bold;background:#f7f7f7;}
.pr-content{display:none;padding:15px;border-top:1px solid #eee;}
.pr-pada{margin-top:14px;border:1px solid #ddd;border-radius:8px;}
.pr-pada-header{padding:10px 15px;cursor:pointer;background:#fafafa;font-weight:600;}
.pr-pada-content{display:none;padding:12px 15px;}

.pr-part-header,
.pr-part{
display:grid;
grid-template-columns:60px 190px 190px 120px 120px 120px;
gap:20px;
align-items:center;
font-size:13px;
}

.pr-part-header{
font-weight:bold;
border-bottom:2px solid #ddd;
padding-bottom:6px;
margin-bottom:6px;
}

.pr-part{
padding:6px 0;
border-bottom:1px dashed #eee;
}
</style>

<div class="pr-container">
<h2>PR Dasa Expandable Tree (120 Years)</h2>
<p style="display:none;">
<strong>Start From:</strong> <?= $nakStartTime->format('Y-m-d H:i:s') ?>
</p>

<?php foreach ($tree as $nakName=>$nakData): ?>
<div class="pr-card">
<div class="pr-header" onclick="toggleBox(this)">
<?= $nakName ?> [<?= $nakData['start']->format('Y-m-d H:i:s') ?> --- <?= $nakData['end']->format('Y-m-d H:i:s') ?>]
</div>

<div class="pr-content">

<?php 
ksort($nakData['padas']);   // 🔥 ADD THIS LINE
foreach ($nakData['padas'] as $pada=>$padaData): 
?>
<div class="pr-pada">

<div class="pr-pada-header" onclick="togglePada(this)">
Pada <?= $pada ?> [<?= $padaData['start']->format('Y-m-d H:i:s') ?> --- <?= $padaData['end']->format('Y-m-d H:i:s') ?>]
</div>

<div class="pr-pada-content">

<div class="pr-part-header">
<span>Part</span>
<span>Start</span>
<span>End</span>
<span>Main</span>
<span>Sub</span>
<span>SubSub</span>
</div>

<?php foreach ($padaData['parts'] as $row): ?>
<div class="pr-part">
<span><?= $row['part'] ?></span>
<span><?= $row['start']->format('Y-m-d H:i:s') ?></span>
<span><?= $row['end']->format('Y-m-d H:i:s') ?></span>
<span><b><?= $row['lords']['main'] ?></b></span>
<span><?= $row['lords']['sub'] ?></span>
<span><?= $row['lords']['subsub'] ?></span>
</div>
<?php endforeach; ?>

</div>
</div>
<?php endforeach; ?>

</div>
</div>
<?php endforeach; ?>

</div>

<script>
function toggleBox(el){
const content=el.nextElementSibling;
content.style.display=content.style.display==="block"?"none":"block";
}

function togglePada(el){
const content=el.nextElementSibling;
content.style.display=content.style.display==="block"?"none":"block";
}
</script>

<?php
/* SAVE PR DASA TREE FOR PDF */
$_SESSION['pr_dasa_tree'] = $tree;

require '../bottom.php';
?>
