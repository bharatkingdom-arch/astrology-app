<?php
include 'rajju_algorithm.php';

$boy = $boy ?? '';
$girl = $girl ?? '';
?>

<?php
if($boy && $girl){

    list($b_type,$b_dir) = getRajju($boy);
    list($g_type,$g_dir) = getRajju($girl);

    list($points,$status) = rajjuScore($boy,$girl);

    echo "<div class='match-scorecard'>";
    echo "<h3>✦ Rajju Dosha Check</h3>";
    echo "<table class='match-table'>";
    echo "<tr><th>Factor</th><th>Boy</th><th>Girl</th><th>Result</th></tr>";

    $badge_class = ($points == 0) ? "badge-bad" : (($points <= 2) ? "badge-avg" : "badge-good");

    echo "<tr>
    <td>Rajju</td>
    <td>$b_type ($b_dir)</td>
    <td>$g_type ($g_dir)</td>
    <td><span class='score-highlight'>$points</span> / 4 <br><span class='badge $badge_class'>$status</span></td>
    </tr>";

    echo "</table>";

    echo "</div>";
}
?>