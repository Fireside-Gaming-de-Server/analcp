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
   $searchQuery = " and (owner like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or id like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or tag like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or  name like '%".mysqli_real_escape_string($dbm, $searchValue)."%') ";
}


## Total number of records without filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from entreprises");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from entreprises WHERE 1 ".$searchQuery);
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from entreprises WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbm, $columnName)." ".mysqli_real_escape_string($dbm, $columnSortOrder)." limit ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();


while ($row = mysqli_fetch_assoc($empRecords)) {
   $data[] = array(
        "id"=>$row['id'],
        "name"=>$row['name'],
        "pdg_pid"=>$row['pdg_pid'],
        "pdg_name"=>$row['pdg_name'],
        "max_stock"=>$row['max_stock'],
        "max_stock_2"=>$row['max_stock_2'],
        "bankacc"=>$row['bankacc'],
        "position"=>$row['position'],
        "type"=>$row['type'],
        "active"=>$row['active'],
        "sealed"=>$row['sealed'],
        "members"=>$row['members'],
        "items_stock"=>$row['items_stock'],
        "items_stock_2"=>$row['items_stock_2'],
        "insert_time"=>$row['insert_time'],
        "button"=> '<a class="btn btn-xs btn-default" href="EntrepriseShow.php?id='.$row['id'].'" ><i class="fas fa-pencil-alt"></i>    Zur Firma</a>'
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);

?>