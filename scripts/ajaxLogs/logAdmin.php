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
if ($searchValue != '') {
    $searchValue = mysqli_real_escape_string($dbm, $searchValue);
    $searchValueArray = explode(" ", $searchValue);
    $conditions = array();
    foreach ($searchValueArray as $value) {
        $conditions[] = "(LOG like '%$value%' or Playername like '%$value%' or UID like '%$value%')";
    }
    $searchQuery = "and (" . implode(" OR ", $conditions) . ")";
}

## Fetch records
$empQuery = "SELECT * from log_admintool WHERE 1 " . $searchQuery . " order by " . mysqli_real_escape_string($dbm, $columnName) . " " . mysqli_real_escape_string($dbm, $columnSortOrder) . " limit " . mysqli_real_escape_string($dbm, $row) . "," . mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {

    $data[] = array( 
        "id"=>$row['id'],
        "Playername"=>$row['Playername'],
        "UID"=>$row['UID'],
        "LOG"=>$row['LOG'],
        "zeitstempel"=>$row['zeitstempel'],
        "player"=>'<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['UID'].'" >'.$row['Playername'].'</a>' 
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);
    
?>