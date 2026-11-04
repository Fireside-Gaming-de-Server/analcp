<?php 

//ersma nach hinten vershcoben weil wir mitlerweile auch ipv6 checken.

require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");

/*
//require ("includes/mysql.php");

$dbup = new mysqli("109.230.224.205", "klausel", "hgtmzkhIeoydQTffeTt5", "userpanel_database");
if ($dbup->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}
*/

$found = false;
$foundIP = "";
$foundPID = "";
$foundNote = "";
$foundID = -1;
$noDoubleMessage = array();
$stmt = $dbup->prepare("SELECT * FROM ip_range_info");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $found = false;
    $foundPID = "";
    $idRangeInfo = $row['id'];
    $like = ''.$row['ipLike'].'%';
    $whitelisted_arr = json_decode($row['whitelisted_arr']);
    $found_arr = json_decode($row['found_arr']);
    $note = $row['note'];

    $stmt2 = $dbup->prepare("SELECT pid_arr, ip FROM ip_info_new WHERE ip like ?");
    $stmt2->bind_param("s", $like);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row2 = mysqli_fetch_array( $result2, MYSQLI_ASSOC)) {
       
        $pid_found_arr = json_decode($row2['pid_arr']);
        $foundIP = $row2['ip'];
        foreach ($pid_found_arr as $key => $value) {
            if ((in_array($value,$whitelisted_arr)) === false) {
                $found =true;
                $foundPID = $value;
                $foundNote = $note;
                $foundID = $idRangeInfo;
                //var_dump($found_arr);
                if ((in_array($value,$found_arr)) === false) {
                    $likePID = '%'.$value.'%';
                    $found_arr[] = $value;
                    $found_arr_insert = json_encode($found_arr);
                    $stmt3 = $dbup->prepare("UPDATE ip_range_info SET `found_arr` = ? WHERE id = ? AND found_arr NOT LIKE ? ");
                    $stmt3->bind_param("sis", $found_arr_insert,$idRangeInfo,$likePID);
                    $stmt3->execute();
                }
            }
        }

        if ($found === true) {
        
            $startMessage = false;
    
            $stmt4 = $dbup->prepare("SELECT * FROM ip_range_info WHERE id = ? AND last_update > NOW() - INTERVAL 4 MINUTE");
            $stmt4->bind_param("i", $foundID);
            $stmt4->execute();
            $result4= $stmt4->get_result();
            while ($row4 = mysqli_fetch_array( $result4, MYSQLI_ASSOC)) {
                $startMessage = true;
            }

            if ((in_array($foundPID,$noDoubleMessage)) === true) {
                $startMessage = false;
            }
            $noDoubleMessage[] = $foundPID;
            if ($startMessage === true) {
    
                $webhookurl = "https://discord.com/api/webhooks/1025764970312515635/-LmuWzLlMQVDPCy94fTjPmM0deJiypXYYQN4uJslTAhCHI9Lnj_EsgFUWaVYHZrsN1nG";
    
                $note = $foundNote;
                $pid = $foundPID;
    
                $timestamp = date("c", strtotime("now"));
    
                $json_data = json_encode([
                    
    
                    "username" => "evo-bot",
    
                    "tts" => false,
    
                    "embeds" => [
                        [
                            // Embed Title
                            "title" => "IP-Range found",
    
                            // Embed Type
                            "type" => "rich",
    
                            // Embed Description
                            "description" => "Der Spieler mit der PID: $pid hat den Server betreten Er nutzt die IP-Range von $note",
    
                            // Timestamp of embed must be formatted as ISO8601
                            "timestamp" => $timestamp,
    
                            // Embed left border color in HEX
                            "color" => hexdec( "ff3333" ),
    
                            // Thumbnail
                            //"thumbnail" => [
                            //    "url" => "https://ru.gravatar.com/userimage/28503754/1168e2bddca84fec2a63addb348c571d.jpg?size=400"
                            //],
    
                            // Additional Fields array
                            "fields" => [
                                // Field 1
                                [
                                    "name" => "PID",
                                    "value" => "$pid",
                                    "inline" => true
                                ],
                                // Field 2
                                [
                                    "name" => "Note",
                                    "value" => "$note",
                                    "inline" => true
                                ],
                                [
                                    "name" => "IP-Adress",
                                    "value" => "$foundIP",
                                    "inline" => true
                                ],
                                [
                                    "name" => "AnalCP",
                                    "value" => "https://analcp.trinity-life.de/playerShow.php?id=$pid",
                                    "inline" => false
                                ]
                                // Etc..
                            ]
                        ]
                    ]
    
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    
    
                $ch = curl_init( $webhookurl );
                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt( $ch, CURLOPT_HEADER, 0);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    
                $response = curl_exec( $ch );
                // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
                // echo $response;
                curl_close( $ch );

                //ban und log

                $steamID = $pid;
                $temp = '';
        
                for ($i = 0; $i < 8; $i++) {
                    $temp .= chr($steamID & 0xFF);
                    $steamID >>= 8;
                }
        
                $return = md5('BE' . $temp);
                $guid = $return;

                $creator = 'Evo-SecurityBot';
                $rconBanGrund = 'Evo-Sicherheitscheck';
                $editBanRconShortNote = 'Evo-Sicherheitscheck---IP-Range';


                $foundAllreadyBanned = false;
                $stmt5 = $dbl->prepare("SELECT * FROM acp_logs WHERE user_pid = ? AND short_note = ?");
                $stmt5->bind_param("ss",$pid,$editBanRconShortNote);
                $stmt5->execute();
                $result5= $stmt5->get_result();
                while ($row5 = mysqli_fetch_array( $result4, MYSQLI_ASSOC)) {
                    $foundAllreadyBanned = true;
                }

                if ($foundAllreadyBanned === false) {
                    $stmt = $dbs->prepare("INSERT INTO bans (pid,guid,reason,creator_name ) VALUES (?,?,?,?)");
                    $stmt->bind_param("ssss",$pid,$guid,$rconBanGrund,$creator);
                    $stmt->execute(); 
    
                    $LogSupporterGroup = '-';
                    $LogUserName = '-';
                    $LogsupporterName = 'Evo-SecurityBot';
                    $LogSupporterPid = '-';
                    $LogCategory = "kickBan";
                    $LogText = ''.$LogsupporterName.' hat den Spieler mit der PID:  ['.$pid.']  per Rcon gebannt.';
                    $editBanRconShortNote = 'Evo-Sicherheitscheck---IP-Range';
                    $stmt = $dbl->prepare("INSERT INTO acp_logs (category, `text`, supporter_pid, supporter_name, supporter_group, user_pid, `user_name`, short_note) VALUES (?,?,?,?,?,?,?,?)");
                    $stmt->bind_param("ssssssss", $LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$pid,$LogUserName,$editBanRconShortNote);
                    $stmt->execute();
                }

            }
        }
    }
}

?>