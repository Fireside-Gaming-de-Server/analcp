<?php 
//cronejob */15 * * * *
// fertig 

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");


// houses Worth
$stmt = $dbl->prepare("SELECT civs, cops, medics FROM cache_onlinePlayers WHERE id = '1'");
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $civs = $row['civs'];
    $cops = $row['cops'];
    $medics = $row['medics'];
    $all = $civs + $cops + $medics;

    $stmt = $dbl->prepare("INSERT INTO stats_player_online (all_players, civ, cop, med) VALUES (?,?,?,?)");
    $stmt->bind_param("iiii", $all, $civs, $cops, $medics);
    $stmt->execute();


}


?>