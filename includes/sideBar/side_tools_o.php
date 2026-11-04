<?php
require ("includes/mysql.php");

$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
$stmt->bind_param("s", $_COOKIE["username"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID = $row[5];

$permission_name = "visitBanRcon";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$visitBanRcon = json_decode($groupID_check);


$permission_name = "visitBanServer";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$visitBanServer = json_decode($groupID_check);

$permission_name = "visitRealRcon";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$visitRealRcon = json_decode($groupID_check);

$permission_name = "visitPlayerOnline";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$visitPlayerOnline = json_decode($groupID_check);

$permission_name = "dbGangs";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbGangs = json_decode($groupID_check);

$permission_name = "createWhitelist";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$createWhitelist = json_decode($groupID_check);

//Permissions Edit Check
$permissionArr = [];
$permissionArr = ['ServereditMainGameParams', 'ServerRestartMainGame', 'ServereditMainSyncParams', 'ServerRestartMainSync'];
foreach ($permissionArr as $value) {
    $permission_name = $value;
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
    $stmt->bind_param("s", $permission_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $permission_row = $row[2];
    $permission_row = json_decode($permission_row);
    ${$permission_name} = false;
    if (in_array($groupID, $permission_row)) {
        ${$permission_name} = true;
    }
}


if ((in_array($groupID, $visitBanRcon))  or (in_array($groupID, $visitBanServer)) or (in_array($groupID, $visitRealRcon)) or (in_array($groupID, $visitPlayerOnline)) or (in_array($groupID, $createWhitelist))or (true)) {
    echo ' 
    <li class="nav-parent">
        <a>
            <i class="fas fa-tools"></i>
            <span>Tools</span>
        </a>
        <ul class="nav nav-children">
            ';
}

// if ((in_array($groupID, $visitBanRcon))  or (in_array($groupID, $visitBanServer)) or (in_array($groupID, $visitRealRcon)))  {
//     echo ' 
//     <li class="nav-parent">
//         <a>
//             Banlisten
//         </a>
//         <ul class="nav nav-children">
//             ';
// }

if ($ServereditMainGameParams or $ServerRestartMainGame or $ServereditMainSyncParams or $ServerRestartMainSync)  {
    echo ' 
            <li>
            <a class="nav-link" href="Servers.php">
                Server-Manager
            </a>
        </li>
            ';
}

if (in_array($groupID, $visitBanServer))  {
    echo ' 
            <li>
            <a class="nav-link" href="banListeServer.php">
                Banliste Server
            </a>
        </li>
            ';
}

// if (in_array($groupID, $visitBanRcon)) {
//     echo ' 
//         <li>
//             <a class="nav-link" href="banlisteRcon.php">
//                 Banliste Rcon
//             </a>
//         </li>
//             ';
// }


// if (in_array($groupID, $visitRealRcon))  {
//     echo ' 
//             <li>
//             <a class="nav-link" href="banlisteRealRcon.php">
//                 Banliste RealRcon
//             </a>
//         </li>
//             ';
// }

// if ((in_array($groupID, $visitBanRcon))  or (in_array($groupID, $visitBanServer)) or (in_array($groupID, $visitRealRcon))) {
//     echo '
//         </ul>
//     </li>
//     ';
// }


if (in_array($groupID, $visitPlayerOnline)) {
    echo ' 
        <li>
            <a class="nav-link" href="onlinePlayers.php">
                Spieler Online
            </a>
        </li>
            ';
}

if (in_array($groupID, $createWhitelist)) {
    echo ' 
        <li>
            <a class="nav-link" href="whitelist.php">
                Whitelist
            </a>
        </li>
            ';
}

if (in_array($groupID, $visitPlayerOnline)) {
    echo ' 
        <li>
            <a class="nav-link" href="guid-converter.php">
                GUID - Converter
            </a>
        </li>
            ';
}

// if (in_array($groupID, $visitPlayerOnline)) {
    echo ' 
        <li>
            <a class="nav-link" href="boursetable.php">
                Börsen-Preise
            </a>
        </li>
            ';
// }

if (false) {
    echo ' 
        <li>
            <a class="nav-link" href="">
                Leer
            </a>
        </li>
            ';
}


if ((in_array($groupID, $visitBanRcon))  or (in_array($groupID, $visitBanServer))  or (in_array($groupID, $visitPlayerOnline)) or (true)) {
    echo '
        </ul>
    </li>';
}




?>

