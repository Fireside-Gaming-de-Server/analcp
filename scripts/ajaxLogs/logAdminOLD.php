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
   $searchQuery = " and (message like '%".mysqli_real_escape_string($dbm, $searchValue)."%')";
}

## Total number of records without filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from adminlogs");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from adminlogs WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from adminlogs WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbm,$columnName)." ".mysqli_real_escape_string($dbm,$columnSortOrder)." limit ".mysqli_real_escape_string($dbm,$row).",".mysqli_real_escape_string($dbm,$rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = array( 
        "id"=>$row['id'],
        "name_admin"=>$row['name_admin'],
        "uid_admin"=>$row['uid_admin'],
        "name_player"=>$row['name_player'],
        "uid_player"=>$row['uid_player'],
        "log"=>$row['log'],
        "created_at"=>$row['created_at'],
        "player"=>'<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['uid_player'].'" >'.$row['name_player'].'</a>' 
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);
    
?>