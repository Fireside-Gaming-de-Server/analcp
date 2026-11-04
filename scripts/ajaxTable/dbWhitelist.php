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
$sel = mysqli_query($dbm,"select count(*) as allcount from acp_whitelist");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from acp_whitelist WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from acp_whitelist WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbm, $columnName)." ".mysqli_real_escape_string($dbm, $columnSortOrder)." limit ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();

while ($row = mysqli_fetch_assoc($empRecords)) {
    $uid = $row['uid'];

    $data[] = array( 
        "uid"=>$row['uid'],
        "name"=>$row['name'],
        "timestamp"=>$row['timestamp'],
        "whitelisted"=>$row['whitelisted'],
        "whitelisted_by_name"=>$row['whitelisted_by_name'],
        "whitelisted_note"=>$row['whitelisted_note'],
        "button"=> '<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['uid'].'" ><i class="fas fa-pencil-alt"></i>    Zum Profil</a>',
        "button_owhitelist" => 'Keine Whitelist vorhanden!' 
    );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);
    
?>