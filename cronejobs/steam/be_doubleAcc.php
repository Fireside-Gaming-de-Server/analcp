<?php 
//cronejob 32 * * * *
// fertig

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
include '/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/function.php';

//be banned pids
$banned_array = array();
$stmt = $dbl->prepare("SELECT pid FROM be_banned");
$stmt->execute();
$result= $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $banned_array[] = $row['pid'];
}

foreach($banned_array as $key => $value) {
    $pidLike = '%'.$value.'%';
    $pidArr_finnish = array();

    $stmt = $dbpc->prepare("SELECT playerid FROM players WHERE playerid like ?");
    $stmt->bind_param("s", $pidLike);
    $stmt->execute();
    $result= $stmt->get_result();
    while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
       $pidArr = json_decode($row['playerid']);

        if ((count($pidArr)) >= 2) {
            foreach($pidArr as $key2 => $value2) {
                if (in_array($value2,$pidArr_finnish) === false) {
                    $pidArr_finnish[] = $value2;    
                }
                $pidLike = '%'.$value2.'%';
                $stmt2 = $dbup->prepare("SELECT pid_arr FROM ip_info_new WHERE pid_arr like ?");
                $stmt2->bind_param("s", $pidLike);
                $stmt2->execute();
                $result2= $stmt2->get_result();
                while ($row2 = mysqli_fetch_array( $result2, MYSQLI_ASSOC)) {
                    if ((count(json_decode($row2['pid_arr']))) >= 2) {
                        $ipArr = json_decode($row2['pid_arr']);
                        foreach ($ipArr as $key3 => $value3) {
                            if (in_array($value3,$pidArr_finnish) === false) {
                                $pidArr_finnish[] = $value3;
                            }
                        }
                    }
                }
            }
            $pidArr_finnish = json_encode($pidArr_finnish);
            $stmt = $dbl->prepare("UPDATE be_banned SET double_arr = ? WHERE pid = ?");
            $stmt->bind_param("ss",$pidArr_finnish ,$value);
            $stmt->execute(); 
        }
    }
}


?>