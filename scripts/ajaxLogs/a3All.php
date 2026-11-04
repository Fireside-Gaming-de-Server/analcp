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
   $searchQuery = " and (UID like '%".mysqli_real_escape_string($dbm, $searchValue)."%' or LOG like '%".mysqli_real_escape_string($dbm, $searchValue)."%') ";
}

## Total number of records without filtering
$sql = "SELECT SUM(allcount) as totalRecords FROM (
    SELECT COUNT(*) as allcount FROM log_sys_broadcast UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_chats UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_documents UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_einstempeln UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_ortung UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_pager UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_perm UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_placeables UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_sperrzone UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_restrain UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_events UNION ALL
    SELECT COUNT(*) as allcount FROM log_sys_weather UNION ALL
    SELECT COUNT(*) as allcount FROM log_player_connections UNION ALL
    SELECT COUNT(*) as allcount FROM log_player_haus UNION ALL
    SELECT COUNT(*) as allcount FROM log_player_eway UNION ALL
    SELECT COUNT(*) as allcount FROM log_player_crafting UNION ALL
    SELECT COUNT(*) as allcount FROM log_player_blueprints UNION ALL
    SELECT COUNT(*) as allcount FROM log_player_talentree UNION ALL
    SELECT COUNT(*) as allcount FROM log_player_obj_use UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_factures UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_lottery UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_casino UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_gang_add UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_gang_remove UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_add UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_remove UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_get UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_give UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_deposit UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_withdraw UNION ALL
    SELECT COUNT(*) as allcount FROM log_money_transfer UNION ALL
    SELECT COUNT(*) as allcount FROM log_enterprise UNION ALL
    SELECT COUNT(*) as allcount FROM log_enterprise_add_money UNION ALL
    SELECT COUNT(*) as allcount FROM log_enterprise_remove_money UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_carpainter UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_impound UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_spawn UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_park UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_repair UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_buy UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_sell UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_inspection UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_insurance UNION ALL
    SELECT COUNT(*) as allcount FROM log_veh_key_transfer UNION ALL
    SELECT COUNT(*) as allcount FROM log_acp_gear UNION ALL
    SELECT COUNT(*) as allcount FROM log_acp_lifesupport UNION ALL
    SELECT COUNT(*) as allcount FROM log_dmg_hospital UNION ALL
    SELECT COUNT(*) as allcount FROM log_dmg_ace UNION ALL
    SELECT COUNT(*) as allcount FROM log_dmg_knockout UNION ALL
    SELECT COUNT(*) as allcount FROM log_dmg_respawn UNION ALL
    SELECT COUNT(*) as allcount FROM log_dmg_taser UNION ALL
    SELECT COUNT(*) as allcount FROM log_dmg_damage UNION ALL
    SELECT COUNT(*) as allcount FROM log_dmg_death UNION ALL
    SELECT COUNT(*) as allcount FROM log_pitem_buy UNION ALL
    SELECT COUNT(*) as allcount FROM log_pitem_drop UNION ALL
    SELECT COUNT(*) as allcount FROM log_pitem_pickup UNION ALL
    SELECT COUNT(*) as allcount FROM log_pitem_trunk UNION ALL
    SELECT COUNT(*) as allcount FROM log_server_stats UNION ALL
    SELECT COUNT(*) as allcount FROM log_server_error UNION ALL
    SELECT COUNT(*) as allcount FROM log_server_anticheat UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_gather UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_buy UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_get UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_give UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_process UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_sell UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_trunk UNION ALL
    SELECT COUNT(*) as allcount FROM log_vitem_use
  ) as count_table";

$result = mysqli_query($dbm, $sql);
$totalRecords = mysqli_fetch_assoc($result)['totalRecords'];


// $sel = mysqli_query($dbll,"select count(*) as allcount from logs");
// $records = mysqli_fetch_assoc($sel);
// $totalRecords = $records['allcount'];

## Total number of record with filtering
$sel = mysqli_query($dbm,"SELECT SUM(allcount) as allcount FROM (
    SELECT COUNT(*) as allcount FROM log_sys_broadcast WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_chats WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_documents WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_einstempeln WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_ortung WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_pager WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_perm WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_placeables WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_sperrzone WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_restrain WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_events WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_sys_weather WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_player_connections WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_player_haus WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_player_eway WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_player_crafting WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_player_blueprints WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_player_talentree WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_player_obj_use WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_factures WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_lottery WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_casino WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_gang_add WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_gang_remove WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_add WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_remove WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_get WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_give WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_deposit WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_withdraw WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_money_transfer WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_enterprise WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_enterprise_add_money WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_enterprise_remove_money WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_carpainter WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_impound WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_spawn WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_park WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_repair WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_buy WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_sell WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_inspection WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_insurance WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_veh_key_transfer WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_acp_gear WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_acp_lifesupport WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_dmg_hospital WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_dmg_ace WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_dmg_knockout WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_dmg_respawn WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_dmg_taser WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_dmg_damage WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_dmg_death WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_pitem_buy WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_pitem_drop WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_pitem_pickup WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_pitem_trunk WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_server_stats WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_server_error WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_server_anticheat WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_gather WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_buy WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_get WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_give WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_process WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_sell WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_trunk WHERE 1 $searchQuery UNION ALL 
    SELECT COUNT(*) as allcount FROM log_vitem_use WHERE 1 $searchQuery
 ) as count_table");
$records = mysqli_fetch_assoc($sel);
$totalRecordwithFilter = $records['allcount'];

// $sel = mysqli_query($dbll,"select count(*) as allcount from logs WHERE 1 ".$searchQuery);
// $records = mysqli_fetch_assoc($sel);
// $totalRecordwithFilter = $records['allcount'];

## Fetch records
$empQuery = "SELECT * FROM (
    SELECT * FROM log_sys_broadcast WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_chats WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_documents WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_einstempeln WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_ortung WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_pager WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_perm WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_placeables WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_sperrzone WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_restrain WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_events WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_sys_weather WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_player_connections WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_player_haus WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_player_eway WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_player_crafting WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_player_blueprints WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_player_talentree WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_player_obj_use WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_factures WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_lottery WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_casino WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_gang_add WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_gang_remove WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_add WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_remove WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_get WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_give WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_deposit WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_withdraw WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_money_transfer WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_enterprise WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_enterprise_add_money WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_enterprise_remove_money WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_carpainter WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_impound WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_spawn WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_park WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_repair WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_buy WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_sell WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_inspection WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_insurance WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_veh_key_transfer WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_acp_gear WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_acp_lifesupport WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_dmg_hospital WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_dmg_ace WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_dmg_knockout WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_dmg_respawn WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_dmg_taser WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_dmg_damage WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_dmg_death WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_pitem_buy WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_pitem_drop WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_pitem_pickup WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_pitem_trunk WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_server_stats WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_server_error WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_server_anticheat WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_gather WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_buy WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_get WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_give WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_process WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_sell WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_trunk WHERE 1 $searchQuery UNION ALL
    SELECT * FROM log_vitem_use WHERE 1 $searchQuery
) AS logs_all
ORDER BY ".mysqli_real_escape_string($dbm, $columnName)." ".mysqli_real_escape_string($dbm, $columnSortOrder)." 
LIMIT ".mysqli_real_escape_string($dbm, $row).",".mysqli_real_escape_string($dbm, $rowperpage);
$empRecords = mysqli_query($dbm, $empQuery);


// $empQuery = "select * from logs WHERE 1 ".$searchQuery."  and type != 'memoryhack' and type != 'SuspiciousUser' and type != 'anticheat' order by ".mysqli_real_escape_string($dbll, $columnName)." ".mysqli_real_escape_string($dbll, $columnSortOrder)." limit ".mysqli_real_escape_string($dbll, $row).",".mysqli_real_escape_string($dbll, $rowperpage);
// $empRecords = mysqli_query($dbll, $empQuery);
$data = array();
while ($row = mysqli_fetch_assoc($empRecords)) {
   $data[] = array( 
        "id"=>$row['id'],
        "Playername"=>$row['Playername'],
        "UID"=>$row['UID'],
        "LOG"=>$row['LOG'],
        "zeitstempel"=>$row['zeitstempel'],
        // if ($row['UID'] <= "765611") {
            // "server"=>'<a class="btn btn-xs btn-default" href="#" disabled>' . $row['UID'] . '</a>',
        // } else {
            "player"=>'<a class="btn btn-xs btn-default" href="playerShow.php?id='.$row['UID'].'" >'.$row['UID'].'</a>'
        // }
   );
}

$response = array(
  "aaData" => $data
);

echo json_encode($response);

?>