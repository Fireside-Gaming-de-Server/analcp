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

## Search 
$searchQuery = "";
if($searchValue != ''){
   $searchQuery = " and (uid like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or name like '%".mysqli_real_escape_string($dbm, $searchValue)."%') ";
}


## Total number of records without filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from players");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from players WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from players WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbm, $columnName)." ".mysqli_real_escape_string($dbm, $columnSortOrder)." limit ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {
    $play_time = $row['playtime'];

    $play_time = str_replace('"[', "", $play_time);
    $play_time = str_replace(']"', "", $play_time);
    $play_time_arr = explode(',', $play_time);
    $cop_time = round($play_time_arr[0] / 60);
    $med_time = round($play_time_arr[1] / 60);
    $civ_time = round($play_time_arr[2] / 60);

    $data[] = array( 
        "uid"=>$row['uid'],
        "name"=>$row['name'],
        "bankacc"=>$row['bankacc'],
        "cash"=>$row['cash'],
        "civ-time"=>$civ_time,
        "cop-time"=>$cop_time,
        "med-time"=>$med_time,
        "insert_time"=>$row['insert_time'],
        "last_seen"=>$row['last_seen'],
        "button"=> '<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['pid'].'" ><i class="fas fa-pencil-alt"></i>    Zum Profil</a>' 
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);
    
?>