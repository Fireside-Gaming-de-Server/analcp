<?php 

//cronejob 59 23 * * * 
//fertig

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/function.php");


$playersArr = [];
$stmt = $dbm->prepare("SELECT pid FROM players WHERE last_seen > NOW() - INTERVAL 1 DAY ");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $playersArr[] = $row['pid'];   
}


foreach ($playersArr as $key => $value) {
    $stmt = $dbl->prepare("SELECT SUM(amount_money) AS amount_money FROM stats_item_sell WHERE pid = ?");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
        $stmt = $dbl->prepare("INSERT INTO stats_farmed_daylie  (farmed_money, pid) VALUES (?,?)");
        $stmt->bind_param("is", $row['amount_money'],$value);
        $stmt->execute();
    }
}


?>