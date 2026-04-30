<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit','512M');

session_start();
ob_start();

require_once __DIR__.'/tcpdf/tcpdf.php';

/* ================= LOAD PDF SESSION ================= */

$pdfData = $_SESSION['pr_pdf_data'] ?? null;

if (!$pdfData) {
    die("PR PDF data not found. Open PRcalculator first.");
}

$prChart       = $pdfData['prChart'] ?? [];
$transitChart  = $pdfData['transitChart'] ?? [];
$centerPR      = strip_tags($pdfData['centerPR'] ?? '');
$centerTransit = strip_tags($pdfData['centerTransit'] ?? '');
$planetTable   = $pdfData['planetTable'] ?? [];
$houseTable    = $pdfData['houseTable'] ?? [];

/* ================= SOUTH CHART SVG ================= */

function generateSouthSVG($chart,$center){

    for ($i=1;$i<=12;$i++){
        if (!isset($chart[$i])) $chart[$i]='';
    }

    $svg = '<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">';

    $svg .= '<rect width="400" height="400" fill="#e6e0cf" stroke="#000" stroke-width="2"/>';

    $svg .= '
    <line x1="100" y1="0" x2="100" y2="400" stroke="#000"/>
    <line x1="300" y1="0" x2="300" y2="400" stroke="#000"/>

    <line x1="0" y1="100" x2="400" y2="100" stroke="#000"/>
    <line x1="0" y1="300" x2="400" y2="300" stroke="#000"/>

    <line x1="200" y1="0" x2="200" y2="100" stroke="#000"/>
    <line x1="200" y1="300" x2="200" y2="400" stroke="#000"/>

    <line x1="0" y1="200" x2="100" y2="200" stroke="#000"/>
    <line x1="300" y1="200" x2="400" y2="200" stroke="#000"/>
    ';

    $positions=[
        12=>[5,15],1=>[105,15],2=>[205,15],3=>[305,15],
        11=>[5,115],4=>[305,115],
        10=>[5,215],5=>[305,215],
        9=>[5,315],8=>[105,315],7=>[205,315],6=>[305,315]
    ];

    foreach($positions as $house=>$pos){

        $lines = explode("<br>",$chart[$house]);
        $y=$pos[1];

        foreach($lines as $line){

            $line = strip_tags($line);
            if(trim($line)=='') continue;

            $svg .= '<text x="'.$pos[0].'" y="'.$y.'" font-size="11">'.$line.'</text>';
            $y+=14;
        }
    }

    if(!empty($center)){

        $centerLines = explode("\n",$center);
        $cy=170;

        foreach($centerLines as $cLine){

            $cLine = trim($cLine);
            if($cLine=='') continue;

            $svg .= '<text x="200" y="'.$cy.'" text-anchor="middle" font-size="12">'.$cLine.'</text>';
            $cy+=16;
        }
    }

    $svg .= '</svg>';

    return $svg;
}

/* ================= CREATE PDF ================= */

$pdf = new TCPDF('L','mm','A4');
$pdf->SetMargins(10,10,10);
$pdf->SetAutoPageBreak(TRUE,10);
$pdf->SetFont('helvetica','',11);

/* ================= PAGE 1 : CHARTS ================= */

$pdf->AddPage();

/* ===== MAIN TITLE ===== */

$pdf->SetFont('helvetica','B',18);
$pdf->Cell(0,10,'PR (Poorva Ravi)',0,1,'C');

$pdf->SetFont('helvetica','B',14);
$pdf->Cell(0,8,'Ekashiti Pratyamsa Paddati',0,1,'C');

$pdf->Ln(5); // space before charts


/* PR CHART */

$pdf->SetFont('helvetica','B',16);
$pdf->SetXY(18,40);
$pdf->Cell(110,10,'PR South Chart',0,0,'C');

$svg1 = generateSouthSVG($prChart,$centerPR);
$pdf->ImageSVG('@'.$svg1,18,52,110,110);


/* TRANSIT CHART */

$pdf->SetFont('helvetica','B',16);
$pdf->SetXY(148,40);
$pdf->Cell(110,10,'Transit South Chart',0,0,'C');

$svg2 = generateSouthSVG($transitChart,$centerTransit);
$pdf->ImageSVG('@'.$svg2,148,52,110,110);
/* ================= PAGE 2 : TABLES ================= */

$pdf->AddPage();

$html='
<style>
table{border-collapse:collapse;width:100%;}
th{background:#f4d03f;padding:6px;font-size:11px;}
td{padding:6px;text-align:center;font-size:10px;}
</style>
';

$html.='<h3>Planetary Table</h3>
<table border="1">
<tr>
<th>Planet</th>
<th>Longitude</th>
<th>Sign</th>
<th>Sign Lord</th>
<th>Nakshatra</th>
<th>Pada</th>
<th>Part</th>
<th>Main</th>
<th>Sub</th>
<th>SubSub</th>
</tr>';

foreach($planetTable as $planet=>$row){

$html.="<tr>
<td>{$planet}</td>
<td>".number_format($row['longitude'],4)."°</td>
<td>{$row['sign']}</td>
<td>{$row['signLord']}</td>
<td>{$row['nak']}</td>
<td>{$row['pada']}</td>
<td>{$row['part']}</td>
<td>{$row['main']}</td>
<td>{$row['sub']}</td>
<td>{$row['subsub']}</td>
</tr>";
}

$html.='</table><br><br>';

$html.='<h3>House Table</h3>
<table border="1">
<tr>
<th>House</th>
<th>Longitude</th>
<th>Sign</th>
<th>Sign Lord</th>
<th>Nakshatra</th>
<th>Pada</th>
<th>Part</th>
<th>Main</th>
<th>Sub</th>
<th>SubSub</th>
</tr>';

foreach($houseTable as $house=>$row){

$label=$house;
if(preg_match('/House\s*(\d+)/i',$house,$m)){
$label=$m[1];
}

$html.="<tr>
<td>{$label}</td>
<td>".number_format($row['longitude'],4)."°</td>
<td>{$row['sign']}</td>
<td>{$row['signLord']}</td>
<td>{$row['nak']}</td>
<td>{$row['pada']}</td>
<td>{$row['part']}</td>
<td>{$row['main']}</td>
<td>{$row['sub']}</td>
<td>{$row['subsub']}</td>
</tr>";
}

$html.='</table>';

$pdf->writeHTML($html,true,false,true,false,'');

/* ================= PAGE 3 : PR DASA TREE ================= */

$pdf->AddPage();

require_once __DIR__.'/engine/PRDasaEngine.php';

$tree = $_SESSION['pr_dasa_tree'] ?? [];

if(empty($tree)){
$data = $_SESSION['kundli_data'] ?? [];
$tree = buildPRDasaTree($data);
$_SESSION['pr_dasa_tree']=$tree;
}

$pdf->SetFont('helvetica','B',16);
$pdf->Cell(0,10,'PR Dasa Tree (120 Years)',0,1,'L');
$pdf->Ln(5);

foreach($tree as $nakName=>$nakData){

$pdf->SetFont('helvetica','B',12);

$pdf->Cell(
0,
8,
$nakName.' ['.
$nakData['start']->format('Y-m-d H:i:s').
' — '.
$nakData['end']->format('Y-m-d H:i:s').']',
0,
1
);

foreach($nakData['padas'] as $pada=>$padaData){

$pdf->SetFont('helvetica','B',11);

$pdf->Cell(
0,
7,
'Pada '.$pada.' ['.
$padaData['start']->format('Y-m-d H:i:s').
' — '.
$padaData['end']->format('Y-m-d H:i:s').']',
0,
1
);

$html='
<table border="1" cellpadding="4">
<tr>
<th width="10%">Part</th>
<th width="25%">Start</th>
<th width="25%">End</th>
<th width="13%">Main</th>
<th width="13%">Sub</th>
<th width="14%">SubSub</th>
</tr>
';

foreach($padaData['parts'] as $row){

$html.='<tr>
<td>'.$row['part'].'</td>
<td>'.$row['start']->format('Y-m-d H:i:s').'</td>
<td>'.$row['end']->format('Y-m-d H:i:s').'</td>
<td>'.$row['lords']['main'].'</td>
<td>'.$row['lords']['sub'].'</td>
<td>'.$row['lords']['subsub'].'</td>
</tr>';

}

$html.='</table><br>';

$pdf->writeHTML($html,true,false,true,false,'');

unset($html);

}

$pdf->Ln(4);

}

ob_end_clean();
$pdf->Output('PR_Report.pdf','I');
exit;
