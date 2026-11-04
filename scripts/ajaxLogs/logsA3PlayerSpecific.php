<?php
require("../../includes/mysql.php");
function isSafeString($str) {
   // Überprüfe, ob $str ein einfacher String ist
   return is_string($str) && !preg_match('/[\'"\\\\]/', $str);
}
## Database configuration
$pid = $_GET['id'];
if (!(isSafeString($pid))) {
   exit;
}
## Read value
$draw = $_POST['draw'];
$row = (int)$_POST['start']; // Ensure it's an integer
$rowperpage = (int)$_POST['length']; // Rows display per page (Ensure it's an integer)
$columnIndex = $_POST['order'][0]['column']; // Column index
$columnName = $_POST['columns'][$columnIndex]['data']; // Column name (Validate against a whitelist)
$columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
$searchValue = mysqli_real_escape_string($dbm, $_POST['search']['value']); // Search value (Sanitize)

## Search 
$searchQuery = "";
if (!empty($searchValue)) {
   $searchQuery = " AND LOG LIKE '%$searchValue%' ";
}

## Fetch records
$tables_sql = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE 'log_%' AND table_name != 'log_acp_gear'";
$tables_result = mysqli_query($dbm, $tables_sql);

$data = array();
$tables = array();

while ($table_row = $tables_result->fetch_assoc()) {
   $tables[] = $table_row["table_name"];
}

$unionQuery = "SELECT * FROM (";
foreach ($tables as $table) {
   $unionQuery .= "SELECT * FROM " . mysqli_real_escape_string($dbm, $table) . " WHERE UID = $pid $searchQuery UNION ALL ";
}
$unionQuery = rtrim($unionQuery, "UNION ALL "); // Remove the trailing UNION ALL
$unionQuery .= ") AS logs_all";
   
   //LIMIT ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);

$result = mysqli_query($dbm, $unionQuery);
if (!$result) {
   $data[] = array(
      "id" => -1,
      "Playername" => "ERROR",
      "table" => "",
      "UID" => -1,
      "LOG" => mysqli_error($dbm), // Escape HTML output
      "zeitstempel" => -1,
      "server" => '<a class="btn btn-xs btn-default" href="#" disabled>' . -1 . '</a>',
      "player" => '<a class="btn btn-xs btn-default" href="playerShow.php?id=' . -1 . '" >' . -1 . '</a>'
   );
} else {
   while ($row_result = $result->fetch_assoc()) {
      $data[] = array(
         "id" => $row_result['id'],
         "Playername" => $row_result['Playername'],
         "table" => "",
         "UID" => $row_result['UID'],
         "LOG" => htmlspecialchars($row_result['LOG']), // Escape HTML output
         "zeitstempel" => $row_result['zeitstempel'],
         "server" => '<a class="btn btn-xs btn-default" href="#" disabled>' . $row_result['UID'] . '</a>',
         "player" => '<a class="btn btn-xs btn-default" href="playerShow.php?id=' . $row_result['UID'] . '" >' . $row_result['UID'] . '</a>'
      );
   }
   // Sort the data array by zeitstempel value
   usort($data, function($a, $b) {
      return strtotime($b['zeitstempel']) - strtotime($a['zeitstempel']);
   });
}




$response = array(
   "aaData" => $data
);

echo json_encode($response);
?>
