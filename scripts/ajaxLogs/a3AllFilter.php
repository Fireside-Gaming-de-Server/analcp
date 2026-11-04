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
   //$searchQuery = " and (owner like '%".$searchValue."%' or id like '%".$searchValue."%' or tag like '%".$searchValue."%' or  name like '%".$searchValue."%') ";
   $searchQuery = " and (type like '%".mysqli_real_escape_string($dbll, $searchValue)."%' or message like '%".mysqli_real_escape_string($dbll, $searchValue)."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($dbll,"select count(*) as allcount from logs");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbll,"select count(*) as allcount from logs WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from logs WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbll, $columnName)." ".mysqli_real_escape_string($dbll, $columnSortOrder)." limit ".mysqli_real_escape_string($dbll, $row).",".mysqli_real_escape_string($dbll, $rowperpage);
$empRecords = mysqli_query($dbll, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {
   $data[] = array( 
        "id"=>$row['id'],
        "insert"=>$row['insert'],
        "type"=>$row['type'],
        "message"=>$row['message'],
        "player"=>'<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['origin'].'" >'.$row['origin'].'</a>' 
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);
    
?>