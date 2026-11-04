<?php 
include 'includes/function.php';
include 'includes/mysql.php';


$steamID = "76561198224625100";
$temp = '';

for ($i = 0; $i < 8; $i++) {
    $temp .= chr($steamID & 0xFF);
    $steamID >>= 8;
}

$return = md5('BE' . $temp);
$guid = $return;

$banned = beBanCheck($guid);

var_dump( $banned);

/*
$key = 'E80A7EE14BDA059B80C9DE0F045186AA';

$counterNotVisible = 0;

$counter = 0;
$pid_str = '';
$countNotVisible = 0;

$stmt = $dbm->prepare("SELECT pid FROM players");
$stmt->execute();
$result= $stmt->get_result();
while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {
    $counter = $counter + 1;
    if ($counter > 99) {
        $counter = 1;
        $pid_str = '';
    }

    if ($pid_str === '') {
        $pid_str = $row['pid'];
    } else {
        $pid_str = ''.$pid_str.','.$row['pid'].'';
    }

    if ($counter === 99) {
        $request = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$key&steamids=$pid_str";
        $response = @file_get_contents($request);
        $response = json_decode($response,true);
        $response = $response['response']['players'];
        var_dump($response);
        foreach ($response as $key => $value) {
            if ($value['communityvisibilitystate'] == 1) {
                $countNotVisible = $countNotVisible + 1;
            }
        }
    }
}

echo $countNotVisible;
*/
/*
$visibilityState =  intval($response['communityvisibilitystate']);



echo $counterNotVisible;




//echo date("Y-m-d","1429994787");


/*
//list of friends
$request = "http://api.steampowered.com/ISteamUser/GetFriendList/v0001/?key=$key&steamid=$steamID&relationship=friend";
$response = @file_get_contents($request);

print_r($response);
*/

// list of games
//calls appIDs

/*
$request = "http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/?key=$key&steamid=$steamID";
$response = @file_get_contents($request);

print_r($response);
*/


// bans / useeless just calls banned or not

/*
$request = "http://api.steampowered.com/ISteamUser/GetPlayerBans/v1//?key=$key&steamids=$steamID&format=json";
$response = @file_get_contents($request);

print_r($response);
*/


//bancheckSocket by freeman 
/*
$beBanCheckSocket = NULL;
function beBanCheck($guid) {
    $server = gethostbyname('arma31.battleye.com');
    $port = 2344;
    global $beBanCheckSocket;
    if (is_null($beBanCheckSocket)) {
        if(!($beBanCheckSocket = socket_create(AF_INET, SOCK_DGRAM, 0))) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);        
            throw new Exception("Couldn't create socket: [$errorcode] $errormsg");
        }
    }
    $sock = $beBanCheckSocket;   

    $msg = "..6.." . $guid;
    if( ! socket_sendto($sock, $msg , strlen($msg) , 0 , $server , $port)) {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);        
        throw new Exception("Could not send data: [$errorcode] $errormsg");
    }
        
    if(socket_recvfrom ( $sock , $reply , 2045 , 0, $host, $port  ) === FALSE)  {
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);        
        throw new Exception("Could not receive data: [$errorcode] $errormsg");
    }

    if ($reply === "..6.") return false;
    $replyBanned = "..6.Global Ban #" . substr($guid, 0, 6);
    if ($reply === $replyBanned ) return true;

    throw new Exception("Malformed BattlEye response: '$reply'. Expexted '$replyBanned' or '..6.'");
}

$guid = 'f2eaa00a0eb5301ad4a479edddc8a121';
if (beBanCheck($guid) === true) {
    echo "<br>Ban Status $guid: " . json_encode(beBanCheck($guid));
} 



if (beBanCheck($guid) === true) {
    echo "<br>Ban Status $guid: " . json_encode(beBanCheck($guid));
};


$guid = '4922b069b9afe4270a88c6619fcd6836';
$request = "https://beguid-converter.allianceapps.io/v3/$guid"; 
$response = @file_get_contents($request);
$response = json_decode($response,true);
$data = $response['data'];

$guid = $data['guid'];
$pid = $data['steamid'];
echo $pid;
*/

?>
