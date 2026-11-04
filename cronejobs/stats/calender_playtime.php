<?php 

// cronejob */4 * * * *
//fertig
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");


$stmt = $dbm->prepare("SELECT pid,playtime FROM players WHERE last_seen > NOW() - INTERVAL 5 MINUTE");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    
    $playTimeArr = explode(",", str_replace("]", "", str_replace("[", "", str_replace("\"", "", $row['playtime']))));
    $playTime = $playTimeArr[0] + $playTimeArr[1] + $playTimeArr[2];

    $found = false;
    $stmt2 = $dbup->prepare("SELECT * FROM calender_playtime WHERE date_stamp = curdate() AND pid = ?");
    $stmt2->bind_param("s", $row['pid']);
    $stmt2->execute();
    $result2= $stmt2->get_result();
    while ($row2 = mysqli_fetch_array( $result2, MYSQLI_ASSOC)) {
        $found = true;
        $stmt3 = $dbup->prepare("UPDATE calender_playtime SET playtime_actually = ? WHERE id = ?");
        $stmt3->bind_param("ii",$playTime,$row2['id']);
        $stmt3->execute();
    }

    if ($found === false) {
        $stmt4 = $dbup->prepare("INSERT INTO calender_playtime (pid, playtime_first, playtime_actually) VALUES (?,?,?)");
        $stmt4->bind_param("sii", $row['pid'],$playTime,$playTime);
        $stmt4->execute();
    }
}

?>
