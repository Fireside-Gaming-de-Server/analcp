<?php
require ("../../includes/mysql.php");
## Database configuration
include '../../includes/function.php';

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
   $searchQuery = " and (PersoID like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or 
        Spielername like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or
        Originalname like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or 
        Spielerfraktion like '%".mysqli_real_escape_string($dbm, $searchValue)."%')";
}


## Total number of records without filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from perso");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from perso WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from perso WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbm, $columnName)." ".mysqli_real_escape_string($dbm, $columnSortOrder)." limit ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {
   $data[] = array(
        "PersoID" => $row['PersoID'],
        "SpielerUID" => $row['SpielerUID'],
        "Spielername" => $row['Spielername'],
        "Originalname" => $row['Originalname'],
        "Spielerfraktion" => $row['Spielerfraktion'],
        "Ausstelldatum" => $row['Ausstelldatum'],
        "phone_number" => $row['phone_number'],
        "VPCBirthDate" => $row['VPCBirthDate'],
        "VPCHeight" => $row['VPCHeight'],
        "VPCSex" => $row['VPCSex'],
        "button" => '<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['SpielerUID'].'" ><i class="fas fa-pencil-alt"></i> Zum Spieler</a>'
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);

?>