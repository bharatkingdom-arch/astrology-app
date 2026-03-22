<?php
/*
Expected:
$chart[1] to $chart[12]
Each contains string (planets or content)

Optional:
$lagnaHouse = house number to highlight
*/

if (!isset($chart)) {
    $chart = [];
}

if (!isset($lagnaHouse)) {
    $lagnaHouse = null;
}

if (!isset($chartCenter)) {
    $chartCenter = "South Chart";
}
?>

<style>
.south-chart {
    width: 100%;
    max-width: 400px;
    margin: 25px auto;
    aspect-ratio: 1 / 1;
    padding: 0;
}

.south-chart table {
    width: 100%;
    height: 100%;
    border-collapse: collapse; /* IMPORTANT */
    table-layout: fixed;
    background: #e6e0cf;
}

.south-chart td {
    border: 2px solid #444; /* SAME thickness everywhere matching SVG */
    vertical-align: top;
    padding: 0;
    font-size: clamp(9px, 2.5vw, 12px); /* Scale text with container */
    background: #e6e0cf;
    color: #000 !important; /* Dark text on light background */
    width: 25%;
    height: 25%;
}

/* Flexible minimum height wrapper inside each cell */
.south-chart td .cell-content {
    height: 100%;
    padding: 4px;
    overflow: hidden;
    word-wrap: break-word;
}

.south-chart td.highlight {
    background: #fff6b3;
}

.center-box {
    text-align: center;
    font-weight: 600;
    font-size: clamp(10px, 3vw, 13px);
    line-height: 1.4;
}

/* Center box content wrapper - taller since it spans 2 rows */
.south-chart td.center-box .cell-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}
</style>
<div class="south-chart">

<table>

<tr>
<td class="<?= ($lagnaHouse==12)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[12] ?? '' ?></div>
</td>

<td class="<?= ($lagnaHouse==1)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[1] ?? '' ?></div>
</td>

<td class="<?= ($lagnaHouse==2)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[2] ?? '' ?></div>
</td>

<td class="<?= ($lagnaHouse==3)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[3] ?? '' ?></div>
</td>
</tr>

<tr>
<td class="<?= ($lagnaHouse==11)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[11] ?? '' ?></div>
</td>

<td colspan="2" rowspan="2" class="center-box">
    <div class="cell-content"><?= $chartCenter ?></div>
</td>

<td class="<?= ($lagnaHouse==4)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[4] ?? '' ?></div>
</td>
</tr>

<tr>
<td class="<?= ($lagnaHouse==10)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[10] ?? '' ?></div>
</td>

<td class="<?= ($lagnaHouse==5)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[5] ?? '' ?></div>
</td>
</tr>

<tr>
<td class="<?= ($lagnaHouse==9)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[9] ?? '' ?></div>
</td>

<td class="<?= ($lagnaHouse==8)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[8] ?? '' ?></div>
</td>

<td class="<?= ($lagnaHouse==7)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[7] ?? '' ?></div>
</td>

<td class="<?= ($lagnaHouse==6)?'highlight':'' ?>">
    <div class="cell-content"><?= $chart[6] ?? '' ?></div>
</td>
</tr>

</table>

</div>
