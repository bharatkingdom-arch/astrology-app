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
    height: 110px;
    vertical-align: top;
    padding: 6px;
    font-size: 14px;
    background: #d6cdb9;
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
</style>
<div class="south-chart">

<table>

<tr>
<td class="<?= ($lagnaHouse==12)?'highlight':'' ?>">
    <?= $chart[12] ?? '' ?>
</td>

<td class="<?= ($lagnaHouse==1)?'highlight':'' ?>">
    <?= $chart[1] ?? '' ?>
</td>

<td class="<?= ($lagnaHouse==2)?'highlight':'' ?>">
    <?= $chart[2] ?? '' ?>
</td>

<td class="<?= ($lagnaHouse==3)?'highlight':'' ?>">
    <?= $chart[3] ?? '' ?>
</td>
</tr>

<tr>
<td class="<?= ($lagnaHouse==11)?'highlight':'' ?>">
    <?= $chart[11] ?? '' ?>
</td>

<td colspan="2" rowspan="2" class="center-box">
    <?= $chartCenter ?>
</td>

<td class="<?= ($lagnaHouse==4)?'highlight':'' ?>">
    <?= $chart[4] ?? '' ?>
</td>
</tr>

<tr>
<td class="<?= ($lagnaHouse==10)?'highlight':'' ?>">
    <?= $chart[10] ?? '' ?>
</td>

<td class="<?= ($lagnaHouse==5)?'highlight':'' ?>">
    <?= $chart[5] ?? '' ?>
</td>
</tr>

<tr>
<td class="<?= ($lagnaHouse==9)?'highlight':'' ?>">
    <?= $chart[9] ?? '' ?>
</td>

<td class="<?= ($lagnaHouse==8)?'highlight':'' ?>">
    <?= $chart[8] ?? '' ?>
</td>

<td class="<?= ($lagnaHouse==7)?'highlight':'' ?>">
    <?= $chart[7] ?? '' ?>
</td>

<td class="<?= ($lagnaHouse==6)?'highlight':'' ?>">
    <?= $chart[6] ?? '' ?>
</td>
</tr>

</table>

</div>
