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
    max-width: 520px;
    margin: 25px auto;
}

.south-chart table {
    width: 100%;
    border-collapse: collapse; /* IMPORTANT */
    table-layout: fixed;
    background: #d6cdb9;
}

.south-chart td {
    border: 2px solid #2b2b2b; /* SAME thickness everywhere */
    vertical-align: top;
    padding: 0;
    font-size: 14px;
    background: #d6cdb9;
}

/* Fixed height wrapper inside each cell */
.south-chart td .cell-content {
    height: 110px;
    overflow: hidden;
    padding: 6px;
}

.south-chart td.highlight {
    background: #f0e39a;
}

.center-box {
    text-align: center;
    font-weight: 600;
    font-size: 13px;
    line-height: 1.6;
}

/* Center box content wrapper - taller since it spans 2 rows */
.south-chart td.center-box .cell-content {
    height: 220px;
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
