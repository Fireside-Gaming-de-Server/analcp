<?php
require ("../../includes/mysql.php");
## Database configuration

## Read value
$draw = $_POST['draw'];
$row = $_POST['start'];
$rowperpage = $_POST['length']; // Rows display per page
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = $_POST['search']['value']; // Search value

function isPlayerOnline($dbm, $pid) {
    $stmt = $dbm->prepare("SELECT onlinePlayers FROM rcon WHERE id=1");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $playersArr = '' . $row[0] . '';
    $online = false;
    if ($playersArr != str_replace($pid, "", $playersArr)) {
        $online = true;
    }
    return $online;
}


## Search
$searchQuery = "";
if($searchValue != ''){
   //$searchQuery = " and (uid like '%".$searchValue."%' or name like '%".$searchValue."%' or pid like '%".$searchValue."%' or  beguid like '%".$searchValue."%') ";
   $searchQuery = " and (players.uid like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or players.pid like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or players.beguid like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or perso.Spielername like '%".mysqli_real_escape_string($dbm, $searchValue)."%') ";
}


## Total number of records without filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from players");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
// $sel = mysqli_query($dbm,"select count(*) as allcount from players WHERE 1 ".$searchQuery);
// $records = mysqli_fetch_assoc($sel);
// $totalRecordwithFilter = $records['allcount'];

## Fetch records

$empQuery = "SELECT
players.pid,
GROUP_CONCAT(DISTINCT perso.Spielername SEPARATOR ', ') AS Spielername,
players.uid,
players.bankacc,
players.cash,
players.coplevel,
players.mediclevel,
players.dojlevel,
players.towlevel,
players.adminlevel,
players.last_seen
FROM players
JOIN perso ON players.pid = perso.SpielerUID
WHERE 1 ".$searchQuery." GROUP BY players.pid order by ".mysqli_real_escape_string($dbm, $columnName)." ".mysqli_real_escape_string($dbm, $columnSortOrder)." limit ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {
   $temp = '';
   $pid = $row['pid'];
   for ($i = 0; $i < 8; $i++) {
      $temp .= chr($pid & 0xFF);
      $pid >>= 8;
   }
   $beguid = md5('BE' . $temp);
   $money = number_format($row['bankacc'] + $row['cash']) . " $";
   $data[] = array(
      "name"=>$row['Spielername'],
      "uid"=>$row['uid'],
      "pid"=>$row['pid'],
      "guid"=>$beguid,
      "money"=>$money,
      "cash"=>$row['cash'],
      "bankacc"=>$row['bankacc'],
      "coplevel"=>$row['coplevel'],
      "mediclevel"=>$row['mediclevel'],
      "dojlevel"=>$row['dojlevel'],
      "towlevel"=>$row['towlevel'],
      "adminlevel"=>$row['adminlevel'],
      "last_seen"=>$row['last_seen'],
      "button"=> '<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['pid'].'" ><i class="fas fa-pencil-alt"></i>    Zum Profil</a>'
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);

?>