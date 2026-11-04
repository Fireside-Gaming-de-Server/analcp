<?php

// cronejob  */2 * * * *
// fertig

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");

$bannedPlayerArrServer = [];
$bannedPlayerArrRcon = [];

$stmt = $dbm->prepare("SELECT pid_player FROM bans WHERE plakate > 0 AND NOW() < ban_end");
$stmt->execute();
$result = $stmt->get_result();
while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $bannedPlayerArrServer[] = $zeile['pid_player'];
}

$bannedPlayerArrServer = json_encode($bannedPlayerArrServer);

$stmt = $dbs->prepare("SELECT pid FROM bans WHERE NOW() < expiration_date");
$stmt->execute();
$result = $stmt->get_result();
while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $bannedPlayerArrRcon[] = $zeile['pid'];
}

$bannedPlayerArrRcon = json_encode($bannedPlayerArrRcon);

$stmt = $dbl->prepare("UPDATE cache_bannedPlayer SET bannedPlayerArrServer = ?, bannedPlayerArrRcon = ? WHERE id = 1");
$stmt->bind_param("ss", $bannedPlayerArrServer, $bannedPlayerArrRcon);
$stmt->execute();


?>

