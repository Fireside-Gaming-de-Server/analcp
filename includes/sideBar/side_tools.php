<?php
require ("includes/mysql.php");


//Permissions Edit Check
$stmt = $dbl->prepare("SELECT * FROM acp_permissions");
$stmt->execute();
$result = $stmt->get_result();
$permissions = array();

while ($row = $result->fetch_assoc()) {
    $groupIDs = json_decode($row["group_ids"]);
    ${$row["permission_name"]} = false;
    if (in_array($groupID, $groupIDs)) {
        ${$row["permission_name"]} = true;
        array_push($permissions, $row["permission_name"]);
    }
}

    echo ' 
    <li class="nav-parent">
        <a>
            <i class="fas fa-tools"></i>
            <span>Tools</span>
        </a>
        <ul class="nav nav-children">
            ';

$NeededRights = array();
array_push($NeededRights, "ServerRestartArmaHaupt","ServerEditArmaHaupt","ServerRestartArmaHauptSync","ServerEditArmaHauptSync","ServerRestartArmaDev","ServerEditArmaDev","ServerRestartFiveMDev","ServerEditFiveMDev");
if ((count(array_intersect($NeededRights, $permissions)) > 0))  {
    echo ' 
            <li>
            <a class="nav-link" href="Servers.php">
                Server-Manager
            </a>
        </li>
            ';
}

$NeededRights = array();
array_push($NeededRights, "visitBanServer");
if ((count(array_intersect($NeededRights, $permissions)) > 0))  {
    echo ' 
            <li>
            <a class="nav-link" href="banListeServer.php">
                Banliste Server
            </a>
        </li>
            ';
}

$NeededRights = array();
array_push($NeededRights, "visitPlayerOnline");
if ((count(array_intersect($NeededRights, $permissions)) > 0))  {
    echo ' 
        <li>
            <a class="nav-link" href="onlinePlayers.php">
                Spieler Online
            </a>
        </li>
            ';
}

$NeededRights = array();
array_push($NeededRights, "createWhitelist");
if ((count(array_intersect($NeededRights, $permissions)) > 0))  {
    echo ' 
        <li>
            <a class="nav-link" href="whitelist.php">
                Whitelist
            </a>
        </li>
            ';
}

$NeededRights = array();
array_push($NeededRights, "visitPlayerOnline");
if ((count(array_intersect($NeededRights, $permissions)) > 0))  {
    echo ' 
        <li>
            <a class="nav-link" href="guid-converter.php">
                GUID - Converter
            </a>
        </li>
            ';
}

$NeededRights = array();
array_push($NeededRights, "visitBoursetable");
if ((count(array_intersect($NeededRights, $permissions)) > 0))  {
    echo ' 
        <li>
            <a class="nav-link" href="boursetable.php">
                Börsen-Preise
            </a>
        </li>
            ';
}



echo '
    </ul>
</li>';




?>

