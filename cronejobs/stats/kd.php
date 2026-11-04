<?php 
// cronejob  */60 * * * * 
//fertig

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/function.php");

//alle player in der local_DB?
$playersArrKD = [];
$stmt = $dbl->prepare("SELECT pid FROM stats_kd");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $playersArrKD[] = $row['pid'];   
}

$playersArr = [];
$stmt = $dbm->prepare("SELECT pid FROM players WHERE last_seen > NOW() - INTERVAL 1 DAY");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $playersArr[] = $row['pid'];   
}

$insertPidString = "";

foreach ($playersArr as $key => $value) {
    if ((in_array($value, $playersArrKD)) === false) {
        if ($insertPidString === "") {
            $insertPidString = '('.$value.')';
        } else {
            $insertPidString = ''.$insertPidString.',('.$value.')';
        }
    }    
}

if ($insertPidString !== "") {
    $stmt = $dbl->prepare('INSERT INTO stats_kd (pid) VALUES '.$insertPidString.'');
    $stmt->execute();
}

// we Need an MessageArray

$messageArr = [];
$stmt = $dbll->prepare("SELECT * FROM logs WHERE type = 'Killed' && message like '%wurde von%' && message NOT LIKE '%null%'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $arr = [];
    $arr[] = $row['message'];
    $arr[] = $row['insert'];
    $messageArr[] = $arr;   
}


foreach ($messageArr as $key => $value) {
    $string = $value[0];
    $date = $value[1];
    $Arr = explode("(", $string);
    $pidDead = explode(")", $Arr[1]); 
    $pidDead = $pidDead[0];
    $pidKiller = explode(")", $Arr[2]); 
    $pidKiller = $pidKiller[0];
    if ($pidDead === $pidKiller) {
        $stmt = $dbl->prepare("SELECT last_update, self_kills FROM stats_kd WHERE pid = ? && last_update < ?");
        $stmt->bind_param("ss", $pidDead, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        if ($row != []) {
            $self_kills = $row[1] + 1;
            $stmt = $dbl->prepare("UPDATE stats_kd SET last_update = ?, self_kills = ? WHERE pid = ?");
            $stmt->bind_param("sis", $date, $self_kills, $pidDead );
            $stmt->execute();
        }
    } else {
        $stmt = $dbl->prepare("SELECT last_update, tode FROM stats_kd WHERE pid = ? && last_update < ?");
        $stmt->bind_param("ss", $pidDead, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        if ($row != []) {
            $tode = $row[1] + 1;
            $stmt = $dbl->prepare("UPDATE stats_kd SET last_update = ?, tode = ? WHERE pid = ?");
            $stmt->bind_param("sis", $date, $tode, $pidDead );
            $stmt->execute();
        }

        $stmt = $dbl->prepare("SELECT last_update, kills FROM stats_kd WHERE pid = ? && last_update < ?");
        $stmt->bind_param("ss", $pidKiller, $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        if ($row != []) {
            $tode = $row[1] + 1;
            $stmt = $dbl->prepare("UPDATE stats_kd SET last_update = ?, kills = ? WHERE pid = ?");
            $stmt->bind_param("sis", $date, $tode, $pidKiller );
            $stmt->execute();
        }
    }
}


?>