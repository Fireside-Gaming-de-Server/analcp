
<?php
// cronejob */2 * * * *
//fertig
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");

$stmt = $dbm->prepare("SELECT onlinePLayers, civs, cops, medics FROM rcon WHERE id=1");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$onlinePlayers = $row[0];
$civCount = $row[1];
$copCount = $row[2];
$medCount = $row[3];

$stmt = $dbl->prepare("UPDATE cache_onlinePlayers SET onlinePLayers = ?, civs = ?, cops = ?, medics = ? WHERE id=1");
$stmt->bind_param("siii", $onlinePlayers, $civCount, $copCount, $medCount);
$stmt->execute();

?>