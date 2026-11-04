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
   $searchQuery = " and (category like '%".mysqli_real_escape_string($dbl, $searchValue)."%' or supporter_pid like '%".mysqli_real_escape_string($dbl, $searchValue)."%' or timestamp like '%".mysqli_real_escape_string($dbl, $searchValue)."%' or supporter_name like '%".mysqli_real_escape_string($dbl, $searchValue)."%' or user_pid like '%".mysqli_real_escape_string($dbl, $searchValue)."%' or  user_name like '%".mysqli_real_escape_string($dbl, $searchValue)."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($dbl,"select count(*) as allcount from acp_logs");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbl,"select count(*) as allcount from acp_logs WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from acp_logs WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbl,$columnName)." ".mysqli_real_escape_string($dbl,$columnSortOrder)." limit ".mysqli_real_escape_string($dbl,$row).",".mysqli_real_escape_string($dbl,$rowperpage);
$empRecords = mysqli_query($dbl, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {

    $user = "";
    if ($row['user_name'] == "-") {
        if ($row['user_pid'] == "-") {
            $user = "-";
        } else {
            $user = '<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['user_pid'].'" >'.$row['user_pid'].'</a>';
        } 
    } else { 
        $user = '<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['user_pid'].'" >'.$row['user_name'].'</a>';
    }

   $data[] = array( 

        "id"=>$row['id'],
        "category"=>$row['category'],
        "text"=> $row['text'],
        "supporter"=> '<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['supporter_pid'].'" >'.$row['supporter_name'].'</a>', 
        "user"=> $user,
        "timestamp"=>$row['timestamp']
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);
    
?>