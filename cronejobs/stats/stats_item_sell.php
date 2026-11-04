<?php 


//ronejob */70 * * * *
//fertig


require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/cronejobs/stats/buy_sell_arr.php");


$notLike = '%(["%';
$stmt = $dbll->prepare("SELECT `message` , `insert` FROM logs WHERE `insert` > NOW() - INTERVAL 70 MINUTE && `type` = 'buy_sell' && `message` like '%verkauft%' && `message` NOT LIKE '%Fahrzeug%' && `message` NOT LIKE ? && `message` NOT LIKE '%ein%' ");
$stmt->bind_param("s", $notLike);
$stmt->execute();
$result = $stmt->get_result();
$bigArr = [];
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $arr = [];
    $arr[] = $row["message"];
    $arr[] = $row["insert"];
    $bigArr[] = $arr;
}

foreach ($bigArr as $key => $value) {

    $message = $value[0];
    $insert = $value[1];
    $arr = explode('(',$message);
    $arr = explode(')',$arr[1]);

    $pid = $arr[0];

    $arr = explode ('hat ',$arr[1]);
    $arr = explode (' ',$arr[1]);
    $amount = $arr[0];
    $item = $arr[1];
    $money = intval(str_replace(',','',$arr[3]));;

    if (array_key_exists($item,$buySellArr)) {
        $item = $buySellArr[''.$item .''];
    }
    
    $stmt = $dbl->prepare("SELECT  `item` , `amount_item`, `amount_money` , `last_update` FROM stats_item_sell WHERE pid = ? && item = ?");
    $stmt->bind_param("ss", $pid, $item);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    if ($row == []) {
        $stmt = $dbl->prepare("INSERT INTO stats_item_sell (`pid`, `item` , `amount_item`, `amount_money` , `last_update`) VALUES (?,?,?,?,?)");
        $stmt->bind_param("ssiis", $pid, $item, $amount, $money, $insert);
        $stmt->execute();
    } else {
        if ($insert > $row[3]) {
            $money = $money + $row[2];
            $amount = $amount + $row[1];
            $insert = $row[3];
            $stmt = $dbl->prepare("UPDATE stats_item_sell SET amount_item = ?, amount_money = ?, last_update = ? WHERE pid = ? && item = ?");
            $stmt->bind_param("iisss", $amount, $money, $insert, $pid, $item);
            $stmt->execute();
        }
    }
}


?>

