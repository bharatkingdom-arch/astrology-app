<?php
include 'rajju_algorithm.php';

$boy = $boy ?? '';
$girl = $girl ?? '';
?>

<!DOCTYPE html>
<html>
<head>
<style>
body {font-family: Arial;}
table {border-collapse: collapse; margin-top:20px;}
td, th {border:1px solid #999; padding:10px;}
th {background:#333; color:#fff;}
.result {font-size:20px; margin-top:20px;}
</style>
</head>

<body>


<?php
if($boy && $girl){

    list($b_type,$b_dir) = getRajju($boy);
    list($g_type,$g_dir) = getRajju($girl);

    list($points,$status) = rajjuScore($boy,$girl);

    echo "<table>";
    echo "<tr><th>Factor</th><th>Boy</th><th>Girl</th><th>Result</th></tr>";

    echo "<tr>
    <td>Rajju</td>
    <td>$b_type ($b_dir)</td>
    <td>$g_type ($g_dir)</td>
    <td>$points <br><b>$status</b></td>
    </tr>";

    echo "</table>";

    echo "<div class='result'>";
    if($points == 0){
        echo "❌ Rajju Dosha - Not Recommended";
    } elseif($points <= 2){
        echo "⚠️ Average Compatibility";
    } else {
        echo "✅ Good Compatibility";
    }
    echo "</div>";
}
?>

</body>
</html>