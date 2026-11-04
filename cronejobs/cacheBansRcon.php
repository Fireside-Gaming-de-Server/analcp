<?php

// cronejob  */5 * * * *
// fertig


require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
require_once '/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/scripts/arc/arc.php';

use \Nizarii\ARC;

$rcon = new ARC('95.217.78.20', 'G94ov0kI8jvR33u0VdlDQjRBw8', 2408, [
    'timeoutSec' => 2
]);

$arr =  $rcon->getBansArray();
$guidArr = [];

$stmt = $dbl->prepare("SELECT guid FROM cache_bannedPlayerRcon");
$stmt->execute();
$result = $stmt->get_result();
while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $guidArr[] = $zeile['guid'];
}

foreach ($arr as $key => $value) {
    $guid = $value[1];
    $reason = $value[3];
    if ((in_array ($guid, $guidArr)) == false) {
        $stmt = $dbl->prepare("INSERT INTO cache_bannedPlayerRcon (guid,reason) VALUES (?,?)");
        $stmt->bind_param("ss", $guid, $reason);
        $stmt->execute();
    }
}


?>