<?php 

// cronejob 30 * * * *
// fertig

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
include '/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/function.php';

$pid_array_entry = array();
$pid_array_no_entry = array();
$pid_array_allready_checked = array();

//pids ina db
$stmt = $dbm->prepare("SELECT pid FROM players");
$stmt->execute();
$result= $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $pid_array_entry[] = $row['pid'];
}

//pids nichjt ina db
$stmt = $dbpc->prepare("SELECT playerid FROM players");
$stmt->execute();
$result= $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $rowArr = json_decode($row['playerid']);
                                                        
    if (count($rowArr) >= 2) {
        foreach ($rowArr as $key => $value) {
            if ((in_array($value,$pid_array_entry)) === false) {
                $pid_array_no_entry[] = $value;
            }
        }
    }
}

//pids allready checked
$stmt = $dbl->prepare("SELECT pid FROM be_banned");
$stmt->execute();
$result= $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $pid_array_allready_checked[] = $row['pid'];
}

foreach ($pid_array_entry as $key => $value) {
    if ((in_array($value,$pid_array_allready_checked)) === false) {
        $pid = $value;
        $steamID = $pid;
        $temp = '';

        for ($i = 0; $i < 8; $i++) {
            $temp .= chr($steamID & 0xFF);
            $steamID >>= 8;
        }

        $return = md5('BE' . $temp);
        $guid = $return;
        

        if (beBanCheck($guid) === true) {
            $db_entry = 0;
            
            $stmt = $dbl->prepare("INSERT INTO be_banned (`pid`, `guid`, `db_entry`) VALUES (?,?,?)");
            $stmt->bind_param("ssi", $pid,$guid,$db_entry);
            $stmt->execute();
        }
    }
}

foreach ($pid_array_no_entry as $key => $value) {
    if ((in_array($value,$pid_array_allready_checked)) === false) {
        $pid = $value;
        $steamID = $pid;
        $temp = '';

        for ($i = 0; $i < 8; $i++) {
            $temp .= chr($steamID & 0xFF);
            $steamID >>= 8;
        }

        $return = md5('BE' . $temp);
        $guid = $return;
        

        if (beBanCheck($guid) === true) {
            $db_entry = 1;
            
            $stmt = $dbl->prepare("INSERT INTO be_banned (`pid`, `guid`, `db_entry`) VALUES (?,?,?)");
            $stmt->bind_param("ssi", $pid,$guid,$db_entry);
            $stmt->execute();
        }
    }
}






?>