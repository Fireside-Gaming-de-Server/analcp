<?php
require ("includes/mysql.php");

$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
$stmt->bind_param("s", $_COOKIE["username"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID = $row[5];

$permission_name = "dbSpielerLevelid";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbSpielerLevelid = json_decode($groupID_check);


$permission_name = "dbSpielerMoney";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbSpielerMoney = json_decode($groupID_check);


$permission_name = "dbVehicles";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbVehicles = json_decode($groupID_check);

$permission_name = "dbGangs";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbGangs = json_decode($groupID_check);

$permission_name = "dbCopInformation";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbCopInformation = json_decode($groupID_check);

$permission_name = "dbMedInformation";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbMedInformation = json_decode($groupID_check);

$permission_name = "dbDoubleAcc";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$dbDoubleAcc = json_decode($groupID_check);



/*
if(false) {

    echo '
    <li class="nav-parent">
        <a>
            <i class="fa fa-database" aria-hidden="true"></i>
            <span>Datenbanken</span>
        </a>
        <ul class="nav nav-children">
            <li class="nav-parent">
                <a>
                    Spieler
                </a>
                <ul class="nav nav-children">
                    <li>
                        <a class="nav-link" href="datenbankSpielerLevelId.php">
                            <span>Level + IDs</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="datenbankenSpielerMoneyTime.php">
                            <span>Money + Playtime</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="nav-link" href="dbVehicles.php">
                    <span>Vehicles</span>
                </a>
            </li>
            </ul>
    </li>
        ';
}
*/

if ((in_array($groupID, $dbSpielerLevelid))  or (in_array($groupID, $dbSpielerMoney))  or (in_array($groupID, $dbVehicles)) or (in_array($groupID, $dbGangs)) or (in_array($groupID, $dbCopInformation)) or (in_array($groupID, $dbMedInformation)) or (in_array($groupID, $dbDoubleAcc))) {
    echo '
    <li class="nav-parent">
        <a>
            <i class="fa fa-database" aria-hidden="true"></i>
            <span>Datenbanken</span>
        </a>
        <ul class="nav nav-children">
            ';
}

// if ((in_array($groupID, $dbSpielerLevelid))  or (in_array($groupID, $dbSpielerMoney))) {
//     echo '
//     <li class="nav-parent">
//         <a>
//             Spieler
//         </a>
//         <ul class="nav nav-children">
//             ';
// }

if (in_array($groupID, $dbSpielerLevelid)) {
    echo '
        <li>
            <a class="nav-link" href="datenbankSpielerLevelId">
                Players
            </a>
        </li>
            ';
}





// if (in_array($groupID, $dbSpielerMoney))  {
//     echo '
//             <li>
//             <a class="nav-link" href="datenbankenSpielerMoneyTime.php">
//                 Money + Playtime
//             </a>
//         </li>
//             ';
// }

// if ((in_array($groupID, $dbSpielerLevelid))  or (in_array($groupID, $dbSpielerMoney))) {
//     echo '
//         </ul>
//     </li>
//     ';
// }


if (in_array($groupID, $dbVehicles)) {
    echo '
        <li>
            <a class="nav-link" href="datenbankVehicles">
                Vehicles
            </a>
        </li>
            ';
}

if (in_array($groupID, $dbGangs)) {
    echo '
        <li>
            <a class="nav-link" href="datenbankGangs">
                Gangs
            </a>
        </li>
            ';
}

if (in_array($groupID, $dbGangs)) {
    echo '
        <li>
            <a class="nav-link" href="datenbankEnreprise">
                Firmen
            </a>
        </li>
            ';
}

if (in_array($groupID, $dbCopInformation )) {
    echo '
        <li>
            <a class="nav-link" href="datenbankCop">
                Cop Datenbank
            </a>
        </li>
            ';
}

if (in_array($groupID, $dbMedInformation )) {
    echo '
        <li>
            <a class="nav-link" href="datenbankMed">
                Medic Datenbank
            </a>
        </li>
            ';
}

if (in_array($groupID, $dbDoubleAcc)) {
    echo '
        <li>
            <a class="nav-link" href="datenbankDoubleAcc">
                Doppel Account
            </a>
        </li>
            ';
}



if ((in_array($groupID, $dbSpielerLevelid))  or (in_array($groupID, $dbSpielerMoney))  or (in_array($groupID, $dbVehicles)) or (in_array($groupID, $dbGangs)) or (in_array($groupID, $dbCopInformation)) or (in_array($groupID, $dbMedInformation)) or (in_array($groupID, $dbDoubleAcc))) {
    echo '
        </ul>
    </li>';
}
