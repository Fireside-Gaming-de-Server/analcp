<?php
include 'includes/checkOnline.php';

$stmt = $dbl->prepare("SELECT pid, `count`  FROM test WHERE `count` >  0");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array($result)) {
    echo $row["pid"].'=>'.$row["count"];
    echo '<br/>';
}

/*
$pidArr = array();
$stmt = $dbm->prepare("SELECT pid FROM players WHERE last_seen > NOW() - INTERVAL 4 HOUR");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array($result)) {
    $pidArr[] =  $row["pid"];
}


foreach ($pidArr as $key => $value) {

    $stmt = $dbm->prepare("SELECT pid FROM players WHERE last_seen > NOW() - INTERVAL 4 HOUR");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = mysqli_fetch_array($result)) {
        $pidArr[] =  $row["pid"];
    }

    $count = 0;
    $stmt = $dbll->prepare("SELECT * FROM logs WHERE `insert` > NOW() - INTERVAL 4 HOUR && origin = ? && message like '%Belohnung%'");
    //$stmt = $dbll->prepare("SELECT * FROM logs WHERE `insert` > NOW() - INTERVAL 4 HOUR && origin = ? && type = 'Killed' && message like '%Belohnung%'");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
        $count = $count + 1;
    }


    $stmt = $dbl->prepare("INSERT INTO test (pid, `count`) VALUES (?,?)");
    $stmt->bind_param("si", $value,$count);
    $stmt->execute();

};
*/

?>