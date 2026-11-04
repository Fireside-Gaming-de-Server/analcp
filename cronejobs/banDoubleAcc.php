<?php 

// cronejob */4 * * * * /usr/bin/php -f /usr/local/lsws/trinity_web/analcp.trinity-life.de/html/cronejobs/banDoubleAcc.php
//fertig
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
require_once '/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/scripts/arc/arc.php';

use \Nizarii\ARC;

$bannedPlayerArrServer = [];
$bannedPlayerArrRcon = [];
$guidKick = "";


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

$stmt = $dbpc->prepare("SELECT playerid FROM players WHERE LENGTH(playerid) > 22");
$stmt->execute();
$result = $stmt->get_result();
while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) { //doubleAccsBigArr
    $row = json_decode($zeile['playerid']);
    $toBanArr =[];
    $banFound = false;
    foreach ($row as $key => $value) { //doubleAccsSmallArr
        if ((in_array ($value, $bannedPlayerArrRcon)) || (in_array ($value, $bannedPlayerArrServer))) { 
            $banFound = true;
        }
    }
    if ($banFound == true) {
        foreach ($row as $key => $value) { //doubleAccsSmallArr
            if (((in_array ($value, $bannedPlayerArrRcon)) || (in_array ($value, $bannedPlayerArrServer))) == false) {
                $steamID = $value;
                $temp = '';
                
                for ($i = 0; $i < 8; $i++) {
                    $temp .= chr($steamID & 0xFF);
                    $steamID >>= 8;
                }
                
                $return = md5('BE' . $temp);
                $guid = $return;
                $bannedReason = "#50 #ntda35497734854";
                $creatorName = "ACP";

                $stmt = $dbs->prepare("INSERT INTO bans (pid,guid,reason,creator_name ) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss",$value,$guid,$bannedReason,$creatorName);
                $stmt->execute(); 

                $rcon = new ARC('51.89.96.109', 'G94ov0kI8jvR33u0VdlDQjRBw8', 2308, [
                    'timeoutSec' => 2
                ]);
                
                $arrr =  $rcon->getPlayersArray();
                
                foreach ($arrr as $key33 => $value33) {
                    if (in_array($guid, $value33)) {
                    $rcon->kickPlayer($value33[0],$bannedReason);  
                    }
                } 
            }
        }
    }
}

//mysqli_free_result( $db_erg );

/*
    if ($toBanArr != []) {
        $bannedReason = "";
        foreach ($row as $key => $value) {
            if ((in_array ($value, $bannedPlayerArrServer)) && ($bannedReason == "")) { 
                $bannedReason = "#49 #ntda35497734854";
            }
            if ((in_array ($value, $bannedPlayerArrRcon)) && ($bannedReason != "#48 #ntda35497734854")) { 
                $stmt = $dbs->prepare("SELECT reason,pid FROM bans WHERE pid = ?");
                $stmt->bind_param("s",$banFound);
                $stmt->execute(); 
                $result = $stmt->get_result();
                while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
                    $reason = $zeile['reason'];
                    if (strpos($reason, '#48') !== false) {
                        $bannedReason = "#48 #ntda35497734854";
                    } else {
                        $bannedReason = "#49 #ntda35497734854";
                    }
                }  
            }
        }
        
        foreach ($row as $key => $value) {
            if (in_array ($value, $bannedPlayerArrRcon)) { 
            } else {
                $steamID = $value;
                $temp = '';
                
                for ($i = 0; $i < 8; $i++) {
                    $temp .= chr($steamID & 0xFF);
                    $steamID >>= 8;
                }
                
                $return = md5('BE' . $temp);
                $guid = $return;
                $creatorName = "ACP";
                $guidKick = $guid;
                echo $value;
                echo $guid;
                echo $bannedReason;
                echo $creatorName;
                echo ',,,';

                
                $stmt = $dbs->prepare("INSERT INTO bans (pid,guid,reason,creator_name ) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss",$value,$guid,$bannedReason,$creatorName);
                $stmt->execute(); 
                
            }
        }
        */



?>
