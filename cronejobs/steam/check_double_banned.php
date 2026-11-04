<?php 

//cronejob 36 * * * *
// fertig

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
include '/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/function.php';


$whitelist_arr = array();
$stmt = $dbs->prepare("SELECT pid FROM whitelist_be_ban");
$stmt->execute();
$result = $stmt->get_result();
while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $whitelist_arr[] = $zeile['pid'];
}


$bannedArr = array();
$stmt = $dbl->prepare("SELECT bannedPlayerArrRcon FROM cache_bannedPlayer WHERE id=1");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $bannedArr = json_decode($row['bannedPlayerArrRcon']);
}

$bannedArrRcon = array();
$stmt = $dbl->prepare("SELECT `guid` FROM cache_bannedPlayerRcon");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $bannedArrRcon[] = $row['guid'];
}


$stmt = $dbl->prepare("SELECT double_arr FROM be_banned WHERE double_arr NOT LIKE '%[]%'");
$stmt->execute();
$result= $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $arr = json_decode($row['double_arr']);

    foreach ($arr as $key => $value) {
        $banned = false;

        $pid = $value;
        $steamID = $pid;
        $temp = '';

        for ($i = 0; $i < 8; $i++) {
            $temp .= chr($steamID & 0xFF);
            $steamID >>= 8;
        }

        $return = md5('BE' . $temp);
        $guid = $return;

        if (in_array($pid,$bannedArr) === true) {
            $banned = true;
        }
        if (in_array($guid,$bannedArrRcon) === true) {
            $banned = true;
        }

        if ($banned === false) {
            if ((in_array($pid,$whitelist_arr)) === false) {
                $creator = 'Evo-SecurityBot';
                $rconBanGrund = 'Evo-Sicherheitscheck';
                
                $stmt = $dbs->prepare("INSERT INTO bans (pid,guid,reason,creator_name ) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss",$pid,$guid,$rconBanGrund,$creator);
                $stmt->execute(); 

                $LogSupporterGroup = '-';
                $LogUserName = '-';
                $LogsupporterName = 'Evo-SecurityBot';
                $LogSupporterPid = '-';
                $LogCategory = "kickBan";
                $LogText = ''.$LogsupporterName.' hat den Spieler mit der PID:  ['.$pid.']  per Rcon gebannt.';
                $editBanRconShortNote = 'Evo-Sicherheitscheck---BE Global';
                $stmt = $dbl->prepare("INSERT INTO acp_logs (category, `text`, supporter_pid, supporter_name, supporter_group, user_pid, `user_name`, short_note) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->bind_param("ssssssss", $LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$pid,$LogUserName,$editBanRconShortNote);
                $stmt->execute();
            }
        }
    }
}
?>