
<?php

//nur irgendein schneller instant check meinerseits

$like = "'77.1%'";



$dbup = new mysqli("109.230.224.205", "klausel", "hgtmzkhIeoydQTffeTt5", "userpanel_database");
if ($dbup->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}
$foundArr = array();

$stmt2 = $dbup->prepare("SELECT * FROM ip_info_new WHERE ip like '77.1%'");

$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row2 = mysqli_fetch_array( $result2, MYSQLI_ASSOC)) {
    $arr = json_decode($row2['pid_arr']);
    foreach ($arr as $key => $value) {
        if ((in_array($value,$foundArr)) === false) {
            $foundArr[] = $value;
        }
    }
}


print_r($foundArr);