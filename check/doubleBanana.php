<?php 
//fertig
//doubleAccCheck basiert auf playercheck api

require ("includes/mysql.php");
require_once 'scripts/arc/arc.php';
use \Nizarii\ARC;

$pid = $_GET['pid'];

$bannedPlayerArrServer = [];
$bannedPlayerArrRcon = [];
$doubleAccArr = [];
$bannedReason = "";
$guidKick = "";

$stmt = $dbpc->prepare("SELECT playerid FROM players WHERE LENGTH(playerid) > 22");
$stmt->execute();
$result = $stmt->get_result();
while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) { //doubleAccs
    if (in_array ($pid, (json_decode($zeile['playerid'])))) { //wennn pid in doubleAccArr
        $doubleAccArr = $zeile['playerid'];
    }
}

if ($doubleAccArr != []) {
    $stmt = $dbm->prepare("SELECT pid_player FROM bans");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
        $bannedPlayerArrServer[] = $zeile['pid_player'];
    }

    $stmt = $dbs->prepare("SELECT pid FROM bans WHERE NOW() < expiration_date");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
        $bannedPlayerArrRcon[] = $zeile['pid'];
    }


    foreach (json_decode($doubleAccArr) as $key => $value) {

        if ((in_array ($value, $bannedPlayerArrServer)) && ($bannedReason == "")) { 
            $bannedReason = "#49 #ntda35497734854";
        }
        if ((in_array ($value, $bannedPlayerArrRcon)) && ($bannedReason != "#48 #ntda35497734854")) { 
            $stmt = $dbs->prepare("SELECT reason,pid FROM bans WHERE pid = ?");
            $stmt->bind_param("s",$value);
            $stmt->execute(); 
            $result = $stmt->get_result();
            while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
                $reason = $zeile['reason'];
                if (strpos($reason, '#48') !== false) {
                    $bannedReason = "#48 #ntda35497734854";
                }
            }  
        }
    }
    if ($bannedReason != "") {
        foreach (json_decode($doubleAccArr) as $key => $value) {
            $steamID = $value;
            $temp = '';
            
            for ($i = 0; $i < 8; $i++) {
                $temp .= chr($steamID & 0xFF);
                $steamID >>= 8;
            }
            
            $return = md5('BE' . $temp);
            $guid = $return;
            $creatorName = "ACP";
            if ($value == $pid) {
                $guidKick = $guid;
            }
            
            if ($bannedReason == "#49 #ntda35497734854") { 
         
                if ((in_array ($value, $bannedPlayerArrRcon)) == false)  {
                    $stmt = $dbs->prepare("INSERT INTO bans (pid,guid,reason,creator_name ) VALUES (?,?,?,?)");
                    $stmt->bind_param("ssss",$steamID,$guid,$bannedReason,$creatorName);
                    $stmt->execute(); 
                }
            }
            if ($bannedReason == "#48 #ntda35497734854") { 
    
                if ((in_array ($value, $bannedPlayerArrRcon)) == false) {
                    $stmt = $dbs->prepare("INSERT INTO bans (pid,guid,reason,creator_name ) VALUES (?,?,?,?)");
                    $stmt->bind_param("ssss",$value,$guid,$bannedReason,$creatorName);
                    $stmt->execute(); 

                }
            }
        }
        if ($guidKick != "") {
            $rcon = new ARC('37.221.94.15', 'EmVN9nXARJk8tuvRq4Hc3D99r4HLvGRV', 2307, [
                'timeoutSec' => 2
            ]);
            
            $arrr =  $rcon->getPlayersArray();
            
            foreach ($arrr as $key => $value) {
                if (in_array($guidKick, $value)) {
                $rcon->kickPlayer($value[0],$bannedReason);  
                }
            } 
        }
    }
}

?>
