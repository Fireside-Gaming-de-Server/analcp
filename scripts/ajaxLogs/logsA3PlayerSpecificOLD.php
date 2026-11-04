<?php
require ("../../includes/mysql.php");
## Database configuration
$pid = $_GET['id'];
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
   $searchQuery = " and (UID like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or LOG like '%".mysqli_real_escape_string($dbm, $searchValue)."%') ";
} else {
   $searchQuery = " and UID=$pid";
}


## Fetch records

$LogTablesQuery = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'log_%'";
$LogTablesResult = mysqli_query($dbm, $LogTablesQuery);

// Loop through the tables and get the entries
$data = array();
if ($LogTablesResult->num_rows > 0) {
   while($table_row = $LogTablesResult->fetch_assoc()) {
      $table_name = $table_row["table_name"];
      if ($table_name != 'log_acp_gear') {
         $entries_sql = "SELECT * FROM $table_name WHERE 1 $searchQuery";
         $entries_result = mysqli_query($dbm, $entries_sql);

         
         while($entry_row = $entries_result->fetch_assoc()) {
            $data[] = array( //Fill the Array
               "id"=>$entry_row['id'],
               "Playername"=>$entry_row['Playername'],
               "UID"=>$entry_row['UID'],
               "LOG"=>$entry_row['LOG'],
               "zeitstempel"=>$entry_row['zeitstempel'],
               "server"=>'<a class="btn btn-xs btn-default" href="#" disabled>' . $entry_row['UID'] . '</a>',
               "player"=>'<a class="btn btn-xs btn-default" href="playerShow.php?id='.$entry_row['UID'].'" >'.$entry_row['UID'].'</a>'
            );
         }
      }
   }
}

// Sort the data array by zeitstempel value
usort($data, function($a, $b) {
   return strtotime($b['zeitstempel']) - strtotime($a['zeitstempel']);
});

$response = array(
  "aaData" => $data
);

echo json_encode($response);
    
?>