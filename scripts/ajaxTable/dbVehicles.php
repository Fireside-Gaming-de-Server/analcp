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
    //$searchQuery = " and (id like '%".$searchValue."%' or pid like '%".$searchValue."%' or type like '%".$searchValue."%' or side like '%".$searchValue."%') ";
    $searchQuery = " and (pid like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or id like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or side like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or type like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or immatriculation like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or classname like '%".mysqli_real_escape_string($dbm, $searchValue)."%') ";
}

## Total number of records without filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from vehicles");
$records = mysqli_fetch_assoc($sel);
$totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbm,"select count(*) as allcount from vehicles WHERE 1 ".$searchQuery);

$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "select * from vehicles WHERE 1 ".$searchQuery." order by ".mysqli_real_escape_string($dbm, $columnName)." ".mysqli_real_escape_string($dbm, $columnSortOrder)." limit ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);
$data = array();

// Get vehicle information
$stmt = $dbm->prepare("SELECT classname, displayname FROM acp_vehicles");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $vehicle_info_array[] = $row;
}


function resolve_displayname($name, $info_array) {
    $return = $name;
    foreach ($info_array as $info) {
        if (isset($info['varname'])) {
            $lookfor = $info['varname'];
        } elseif (isset($info['classname'])) {
            $lookfor = $info['classname'];
        } else {
            $lookfor = "false";
        }
        

        if ($lookfor == $name) {
            $return = $info['displayname'];
            break;
        }
    }
    return $return;
}

while ($row = mysqli_fetch_assoc($empRecords)) {
    $name = resolve_displayname($row['classname'], $vehicle_info_array);

    $data[] = array( 
        "id"=>$row['id'],
        "pid"=>$row['pid'],
        "side"=>$row['side'],
        "classname"=>$row['classname'],
        "name"=>$name,
        "type"=>$row['type'],
        "alive"=>$row['alive'],
        "blacklist"=>$row['blacklist'],
        "trunkupgrade"=>$row['trunkupgrade'],
        "active"=>$row['active'],
        "plate"=>$row['plate'],
        "color"=>$row['color'],
        "inventory"=>$row['inventory'],
        "gear"=>$row['gear'],
        "fuel"=>$row['fuel'],
        "damage"=>$row['damage'],
        "insert_time"=>$row['insert_time'],
        "controlTech"=>$row['controlTech'],
        "immatriculation"=>trim(trim((string) ($row['immatriculation'] ?? '')), '"'),
        "fourriere"=>$row['fourriere'],
        "entreprise_id"=>$row['entreprise_id'],
        "meters"=>$row['meters'],
        "custom_color"=>$row['custom_color'],
        "custom_rvmat"=>$row['custom_rvmat'],
        "insurance"=>$row['insurance'],
        "dateInsurance"=>$row['dateInsurance'],
        "contract_number"=>$row['contract_number'],
        "expiration_date"=>$row['expiration_date'],
        "old_owner"=>$row['old_owner'],
        "booms"=>$row['booms'],
        "button"=> '<a class="btn btn-xs btn-default" href="vehicleShow.php?id='.$row['id'].'" ><i class="fas fa-pencil-alt"></i>    Zum Fahrzeug</a>' 
   );
}

$response = array(
  "draw" => (int) $draw,
  "recordsTotal" => (int) $totalRecords,
  "recordsFiltered" => (int) $totalRecordwithFilter,
  "aaData" => $data
);

echo json_encode($response);
    
?>