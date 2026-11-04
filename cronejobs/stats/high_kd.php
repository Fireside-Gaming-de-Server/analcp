<?php 
require ("/var/www/analcp.trinity-life.de/includes/mysql.php");

$arr = array();
$stmt = $dbl->prepare("SELECT pid, kills, tode, kills / tode AS kdr FROM stats_kd WHERE (kills / tode) >= '1.8' AND kills >= '8'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $arr[] = array(
        "pid" => $row['pid'],
        "kills" => $row['kills'],
        "tode" => $row['tode'],
        "kdr" => $row['kdr']
    );
}

$stmt = $dbm->prepare("DELETE FROM dc_spectatelist");
$stmt->execute();

$reason = "kd";
foreach ($arr as $key => $value) {
    $kdr = round($value['kdr'], 2);
    $stmt = $dbm->prepare("INSERT INTO dc_spectatelist (pid,kills,tode,kdr,reason) VALUES (?,?,?,?,?)");
    $stmt->bind_param("siiss", $value['pid'],$value['kills'],$value['tode'],$kdr,$reason);
    $stmt->execute();
}

?>