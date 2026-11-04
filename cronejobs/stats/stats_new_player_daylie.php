<?php 
//cronejob  59 23 * * *
//fertig

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");

$unique = 0;
$stmt = $dbm->prepare("SELECT count(uid) AS count FROM players WHERE last_seen > NOW() - INTERVAL 1 DAY");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $unique = $row['count'];
}

$newPlayerCount = 0;
$pidArr = [];
$stmt = $dbm->prepare("SELECT pid from players WHERE insert_time > NOW() - INTERVAL 1 DAY");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {

    $newPlayerCount = $newPlayerCount + 1;
    $pidArr[] = $row['pid'];

}

$pidArr = json_encode($pidArr);
$stmt = $dbl->prepare("INSERT INTO stats_new_player_daylie (uninque_players, new_players ,new_players_arr) VALUES (?,?,?)");
$stmt->bind_param("iis", $unique, $newPlayerCount, $pidArr  );
$stmt->execute();

?>