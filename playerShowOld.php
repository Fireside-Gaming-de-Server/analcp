<?php
//Log und Online Check
include 'includes/checkOnline.php';
include 'includes/Config_vItems.php';



function ArrayToString($Array) {
    $ArrayJson = json_encode($Array);
    $String = str_replace('"', '`', $ArrayJson);
    return '"' . $String . '"';
}

function StringToArray($String) {
    $json = str_replace('`', '"', $String);
    $Array = json_decode(trim($json, '"'), true);
    return $Array;
}

function isPlayerOnline($dbm, $pid) {
    $stmt = $dbm->prepare("SELECT onlinePlayers FROM rcon WHERE id=1");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $playersArr = '' . $row[0] . '';
    $online = false;
    if ($playersArr != str_replace($pid, "", $playersArr)) {
        $online = true;
    }
    return $online;
}

$LogSupporterPid = $row[7];
$LogSupporterName = $row[1];
$LogSupporterGroup = $userGroupname;

//Permission
$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
$stmt->bind_param("s", $_COOKIE["username"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID = $row[5];

$permission_name = "playerShowVisit";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$groupID_check = json_decode($groupID_check);

if (in_array($groupID, $groupID_check)) {
	//passt
} else {
	header("Location: dashboard.php");
}




//Permissions Edit Check
$permissionArr = [];
$permissionArr = ['playersEditNotes', 'playersEditMoney', 'playersEditLevelCop', 'playersEditLevelSwat','playersEditLevelFbi','playersEditLevelDea','playersEditLevelMed','playersEditLevelFd','playersEditLevelDoj','playersEditLevelVolunteers','playersEditLeveltow','playersEditLevelRm','playersEditLevelTaxi','playersEditLevelJcm','playersEditLevelRedgwh','playersEditLevelGCPAT','playersEditLevelAdmin', 'playersEditName', 'playersEditGambling', 'playersEditLicenseCiv', 'playersEditGear', 'playersEditNotesDelete', 'playersEditLicenseCop', 'playersEditLicenseMed', 'playersLogsA3', 'playerKick', 'playerBan', 'playerUnban', 'playersEditTsWarn1', 'playersEditTsWarn2', 'playersEditTsWarn3', 'unbugVehiclePlayer', 'playersEditAdminMessage', 'playersEditRevive', 'playersEditPosition', 'Item_give', 'createWhitelist', 'playersEdittfarDisable'];
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



//permissions Fertsch
$id = $_GET['id'];
if (isset($id)) {
	$length = strlen($id);
	$pid = "";
	if ($length === 17) {
		$pid = $id;
	} else {
		if ($length <= 5) {
			$stmt = $dbm->prepare("SELECT pid FROM players WHERE uid=?");
			$stmt->bind_param("i", $id);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_row();
			$pid = $row[0];
		}
	}
} else {
	header("Location: dashboard.php");
	exit();
}

$permission_name = "playerShowVisitOwn";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$groupID_check = json_decode($groupID_check);
if ((in_array($groupID, $groupID_check)) OR $LogSupporterPid != $pid) {
	//Alles OK
} else {
	header("Location: dashboard.php");
	exit();
}


/*
if ($result = $mysqli->query($query)) {

    while ($obj = $result->fetch_object()) {
        printf ("%s (%s)\n", $obj->Name, $obj->CountryCode);
    }
*/

$stmt = $dbm->prepare("SELECT * FROM acp_vitems");
$stmt->execute();
$viteminforesult = $stmt->get_result();
$viteminfoarray = array();
while ($row = $viteminforesult->fetch_assoc()) {
    $viteminfoarray[$row["classname"]] = [
        "displayname" => $row["displayname"],
        "weight" => $row["weight"],
    ];
}


$stmt = $dbm->prepare("SELECT * FROM players WHERE pid=?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_assoc($result)) {
	$id			 		= $row["uid"];
	$name				= $row["name"];
	$alias				= StringToArray($row["aliases"]);
	$cash				= $row["cash"];
	$bank				= $row["bankacc"];
	$copLevel			= $row["coplevel"];
	$swatLevel			= $row["swatlevel"];
	$fbiLevel			= $row["fbilevel"];
	$deaLevel			= $row["dealevel"];
	$medLevel			= $row["mediclevel"];
	$fdLevel			= $row["fdlevel"];
	$dojLevel			= $row["dojlevel"];
	$volunteersLevel	= $row["volunteerslevel"];
	$towlevel			= $row["towlevel"];
	$rmLevel			= $row["rmlevel"];
	$taxiLevel			= $row["taxilevel"];
	$jcmLevel			= $row["jcmlevel"];
	$redgwhLevel		= $row["redgwhlevel"];
	$AutohausMMLevel	= $row["autohausmmlevel"];
	$civ_gearArr		= $row["civ_gear"];
	$cop_gearArr		= $row["cop_gear"];
	$med_gearArr		= $row["med_gear"];
	$civ_statsArr		= StringToArray($row["civ_stats"]);
	$cop_statsArr		= StringToArray($row["cop_stats"]);
	$med_statsArr		= StringToArray($row["med_stats"]);
	$arrested			= $row["arrested"];
	$adminLevel			= $row["adminlevel"];
	$gamblingBlacklist	= $row["gamble_blacklist"];
	$blacklist			= $row["blacklist"];
	$civ_alive			= $row["civ_alive"];
	$civ_positionArr	= $row["civ_position"];
	$playTime			= StringToArray($row["playtime"]);
	$firstSeen			= $row["insert_time"];
	$lastSeen			= $row["last_seen"];
	$gang_id			= $row["gang_id"];
	$gang_perm_id		= $row["gang_perm_id"];

	$expLevel			= $row["exp_level"];
	$expTotal			= $row["exp_total"];
	$expPerkPoints 		= $row["exp_perkPoints"];
	$expPerks 			= $row["exp_perks"];

	$lottery_win 		= $row["lottery_win"];
	$driving_questions 	= $row["driving_questions"];

    $civlicenseArray = StringToArray($row["civ_licenses"]);

    $coplicenseArray = StringToArray($row["cop_licenses"]);

    $medlicenseArray = StringToArray($row["med_licenses"]);

	$guid =$row["beguid"];

}
//Perso
$stmt = $dbm->prepare("SELECT * FROM perso WHERE SpielerUID = ?");
$stmt->bind_param("s", $pid);
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	// Get Gang Data
	$stmt = $dbm->prepare("SELECT name FROM gangs WHERE id=?");
	$stmt->bind_param("s", $row['gang_id']);
	$stmt->execute();
	$result2 = $stmt->get_result();
	$row2 = mysqli_fetch_array($result2);
	if (isset($row2['name'])) {
		$GangName = $row2['name'];
	} else {
		$GangName = "Kein Gang";
	}


	/////////////////////////////
	$rowString = str_replace('`', '"', $row['Persodaten']);
	$jsonArr = json_decode(trim($rowString, '"'), true);

	if (!empty($rowString)) {
		if (json_last_error() == JSON_ERROR_NONE) {
			if (is_array($jsonArr)) {
				$Persoinfo = $jsonArr;
			} else {
				$Persoinfo = ["Not Array"];
			}
		} else {
			error_log('Fehler beim Dekodieren von JSON: ' . json_last_error_msg());
		}
	} else {
		$Persoinfo = [""];
	}
	// Daten zum Array hinzufügen
	$PersoDatenArray[] = [
		'Spielerfraktion' => $row['Spielerfraktion'],
		'Spielername' => $row['Spielername'],
		'gang_id' => $row['gang_id'],
		'GangName' => $GangName,
		'phone_number' => $row['phone_number'],
		'call_value' => $row['call_value'],
		'sms_value' => $row['sms_value'],
		'DateOfBirth' => $Persoinfo[0][0] . '.' . $Persoinfo[0][1] . '.' . $Persoinfo[0][2],
		'Size' => $Persoinfo[1],
		'Weight' => $Persoinfo[2],
		'Residence' => $Persoinfo[3],
		'Streetname' => $Persoinfo[4],
		'HouseNumber' => $Persoinfo[5],
		'Eyecolor' => $Persoinfo[6],
		'Gender' => $Persoinfo[7],
		'Nationality' => $Persoinfo[8],
		'Character' => $Persoinfo[9]
	];
}

$namenArray = array_column($PersoDatenArray, 'Spielername');
$namenString = implode(', ', $namenArray);


//Enterprice Müll

// SELECT * FROM entreprises WHERE members::text LIKE CONCAT('%', ?, '%')
$stmt = $dbm->prepare("SELECT * FROM entreprises WHERE members LIKE CONCAT('%', ?, '%')");
$stmt->bind_param("s", $pid);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
	$CompanyID = $row["id"];
	$CompanyName = $row["name"];
	$CompanyOwnerPID = $row["pdg_pid"];
	$CompanyOwnerName = $row["pdg_name"];
	$CompanyBank = $row["bankacc"];
	$CompanyType = $row["type"];
	$CompanyActive = $row["active"];
	$CompanySealed = $row["sealed"];
	$CompanyInsertTime = $row["insert_time"];
	$CompanyPosition = $row["position"];

    $CompanyMembersArr = StringToArray($row["members"]);

    $CompanyPriceArr = StringToArray($row["prices"]);

    $CompanyStockArr = StringToArray($row["items_stock"]); //Format [["Item1",Anzahl1],["Item2",Anzahl2]]
	$CompanyMaxStock = $row["max_stock"];

    $CompanyStockPrivateArr = StringToArray($row["items_stock_2"]); //Format [["Item1",Anzahl1],["Item2",Anzahl2]]
	$CompanyMaxPrivateStock = $row["max_stock_2"];
}
foreach ($CompanyMembersArr as $value) {
	$MemberPid = $value[0];
	$MemberName = $value[1];
	$MemberGroup = $value[2];
}




//Whitelist Stuff
$stmt = $dbm->prepare("SELECT name, whitelisted, whitelisted_by, whitelisted_by_name, whitelisted_note FROM acp_whitelist WHERE uid=?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$rowStringWhitelist = $row[0];
$isWhitelisted = $row[1];
$WhitelistLastEditBy = $row[2];
$WhitelistLastEditByName = $row[3];
$WhitelistNote = $row[4];

$jsonArrWhitelist = StringToArray($row[0]);

if (!empty($rowStringWhitelist)) {
	$WhitelistExists = TRUE;
	if (json_last_error() == JSON_ERROR_NONE) {
		if (is_array($jsonArrWhitelist)) {
			$WhitelistNameArray = $jsonArrWhitelist;
		} else {
			$WhitelistNameArray = ["Not Array"];
		}
	} else {
		error_log('Fehler beim Dekodieren von JSON: ' . json_last_error_msg());
	}
} else {
	$WhitelistExists = FALSE;
	$WhitelistNameArray = [""];
}

//logVars
$LogUserPid = $pid;
$LogUserName = $name;

//guidShit
if ($guid === null) {
	$steamID = $pid;
	$temp = '';

	for ($i = 0; $i < 8; $i++) {
		$temp .= chr($steamID & 0xFF);
		$steamID >>= 8;
	}

	$return = md5('BE' . $temp);
	$guid = $return;
	$stmt = $dbm->prepare("UPDATE players SET beguid = ? WHERE pid=?");
	$stmt->bind_param("ss", $guid, $pid);
	$stmt->execute();
};

//gangShit
// $gangID = $row[26];
// $gangName = "Keine Gang";
// $gangTag = "Keine Gang";
// if ($gangID >= 1) {
// 	$stmt = $dbm->prepare("SELECT name, tag FROM gangs WHERE id=?");
// 	$stmt->bind_param("i", $gangID);
// 	$stmt->execute();
// 	$result = $stmt->get_result();
// 	$row = $result->fetch_row();
// 	$gangName = $row[0];
// 	if ($row[1] === "") {
// 	} else {
// 		$gangTag = $row[1];
// 	}
// }

// if ($gangID >= 1) {

// 	$stmt = $dbm->prepare("SELECT gang_id , uid FROM players WHERE pid=?");
// 	$stmt->bind_param("i", $LogSupporterPid);
// 	$stmt->execute();
// 	$result = $stmt->get_result();
// 	$row = $result->fetch_row();

// 	if ($gangID == $row[0] && ($_SESSION["rightsArr"]["visit_gang_Playersite"] != true)) {

// 		header("Location: dashboard.php");
// 		exit();
// 	}
// }

/*
//lockerShit
$stmt = $dbm->prepare("SELECT civ_level, cop_level, med_level FROM locker WHERE playerid=?");
$stmt->bind_param("s", $pid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$civ_locker = $row[0];
$cop_locker = $row[1];
$med_locker = $row[2];
$civ_lockerStr = strval($civ_locker);
*/

//OnlineShit
$stmt = $dbm->prepare("SELECT onlinePlayers FROM rcon WHERE id=1");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$playersArr = '' . $row[0] . '';
$online = false;
if ($playersArr != str_replace($pid, "", $playersArr)) {
	$online = true;
}




$licensepricestmt = $dbm->prepare("SELECT varname,displayname,price FROM acp_licenses");
$licensepricestmt->execute();
$licensepriceresult = $licensepricestmt->get_result();
$license_info_array = array();
while ($row = $licensepriceresult->fetch_assoc()) {
	$license_info_array[] = $row;
}

if (isset($_POST["test"])) {
	$message = "Hello, World!";
	echo "<script>alert('$message');</script>";
}

//Lizenzen Change Civ
if (isset($_POST["changeLicenseCivSubmit"])) {
	$licensechangearray = array();
	$ChangedLicensesOld = array();
	$ChangedLicensesNew = array();
	foreach ($civlicenseArray as $value) {
		$newLicenseValue = $_POST['license_' . $value[0]];
		if ($newLicenseValue != $value[1]) { // New != Old
			$ChangedLicensesOld[] = "[`" . $value[0] . "`," . $value[1] . "]";
			$ChangedLicensesNew[] = "[`" . $value[0] . "`," . $newLicenseValue . "]";
		}

		$licensechangearray[] = "[`" . $value[0] . "`," . $newLicenseValue . "]";
	}

	$licensechangestring = '"[' . implode(',', $licensechangearray) . ']"';
	$licensechangestringOld = '"[' . implode(',', $ChangedLicensesOld) . ']"';
	$licensechangestringNew = '"[' . implode(',', $ChangedLicensesNew) . ']"';



	// if (($playersEditLicenseCiv) && (count($ChangedLicensesNew) > 0)) {
	// 	echo "TRUE";
	// } else {
	// 	echo "FALSE";
	// }
	// exit;
	if (($playersEditLicenseCiv) && (count($ChangedLicensesNew) > 0)) {
			$stmt = $dbm->prepare("UPDATE players SET civ_licenses=? WHERE pid=?");
			$stmt->bind_param("ss", $licensechangestring, $pid);
			$stmt->execute();

		if (isPlayerOnline($dbm, $pid)) {
			$typeAcp = '"CivLizenzBearbeitet"';
			$valueAcp = $licensechangestringNew;

			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
			$stmt->execute();
		}
		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat die Civ-Lizenzen von [' . $LogUserPid . '] "' . $LogUserName . '" bearbeitet. Vorher: ' . $licensechangestringOld . ' Nacher: ' . $licensechangestringNew . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		header("Refresh:0");
	}
}

if (isset($_POST["changeLicenseCopSubmit"])) {
	$licensechangearray = array();
	$ChangedLicensesOld = array();
	$ChangedLicensesNew = array();
	foreach ($coplicenseArray as $value) {
		$newLicenseValue = $_POST['license_' . $value[0]];
		if ($newLicenseValue != $value[1]) { // New != Old
			$ChangedLicensesOld[] = "[`" . $value[0] . "`," . $value[1] . "]";
			$ChangedLicensesNew[] = "[`" . $value[0] . "`," . $newLicenseValue . "]";
		}

		$licensechangearray[] = "[`" . $value[0] . "`," . $newLicenseValue . "]";
	}

	$licensechangestring = '"[' . implode(',', $licensechangearray) . ']"';
	$licensechangestringOld = '"[' . implode(',', $ChangedLicensesOld) . ']"';
	$licensechangestringNew = '"[' . implode(',', $ChangedLicensesNew) . ']"';


	// echo $licensechangestringNew;
	// exit;
	if (($playersEditLicenseCop) && (count($ChangedLicensesNew) > 0)) {
			$stmt = $dbm->prepare("UPDATE players SET cop_licenses=? WHERE pid=?");
			$stmt->bind_param("ss", $licensechangestring, $pid);
			$stmt->execute();

		if (isPlayerOnline($dbm, $pid)) {
			$typeAcp = '"CopLizenzBearbeitet"';
			$valueAcp = $licensechangestringNew;

			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
			$stmt->execute();
		}
		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat die Cop-Lizenzen von [' . $LogUserPid . '] "' . $LogUserName . '" bearbeitet. Vorher: ' . $licensechangestringOld . ' Nacher: ' . $licensechangestringNew . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		header("Refresh:0");
	}
}

//Lizenzen change Med
if (isset($_POST["changeLicenseMedSubmit"])) {
	$licensechangearray = array();
	$ChangedLicensesOld = array();
	$ChangedLicensesNew = array();
	foreach ($medlicenseArray as $value) {
		$newLicenseValue = $_POST['license_' . $value[0]];
		if ($newLicenseValue != $value[1]) { // New != Old
			$ChangedLicensesOld[] = "[`" . $value[0] . "`," . $value[1] . "]";
			$ChangedLicensesNew[] = "[`" . $value[0] . "`," . $newLicenseValue . "]";
		}

		$licensechangearray[] = "[`" . $value[0] . "`," . $newLicenseValue . "]";
	}

	$licensechangestring = '"[' . implode(',', $licensechangearray) . ']"';
	$licensechangestringOld = '"[' . implode(',', $ChangedLicensesOld) . ']"';
	$licensechangestringNew = '"[' . implode(',', $ChangedLicensesNew) . ']"';


	// echo $licensechangestringNew;
	// exit;
	if (($playersEditLicenseMed) && (count($ChangedLicensesNew) > 0)) {
			$stmt = $dbm->prepare("UPDATE players SET med_licenses=? WHERE pid=?");
			$stmt->bind_param("ss", $licensechangestring, $pid);
			$stmt->execute();

		if (isPlayerOnline($dbm, $pid)) {
			$typeAcp = '"MedLizenzBearbeitet"';
			$valueAcp = $licensechangestringNew;

			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
			$stmt->execute();
		}
		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat die Med-Lizenzen von [' . $LogUserPid . '] "' . $LogUserName . '" bearbeitet. Vorher: ' . $licensechangestringOld . ' Nacher: ' . $licensechangestringNew . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		header("Refresh:0");
	}
}

// Kick
if (isset($_POST["playerKickSubmit"])) {
    if ($playerKick) {
        $typeAcp = '"Kick"';
        $valueAcp = '["' . $_POST['playerKickMessage'] . '"]';
        $stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
        $stmt->execute();

        $LogCategory = "kickPlayer";
        $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat denn Spieler ['.$LogUserPid.'] '.$LogUserName.' gekickt.';
        log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName,$valueAcp);
    }
}

// Ban
if (isset($_POST["playerBanSubmit"])) {
    if ($playerBan) {
        $typeAcp = '"Ban"';
        $valueAcp = '["' . $_POST['playerBanMessage'] . '"]';
        $stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
        $stmt->execute();

        $stmt = $dbm->prepare("SELECT * FROM acp_bans WHERE uid=?");
        $stmt->bind_param("s", $pid);
        $playerid = $pid;
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $banVorher = 'UID: "'.$row['uid'].'" Name: "'.$row['name'].'" Ban Start: "'.$row['ban_start'].'" Ban Ende: "'.$row['ban_end'].'" S-UID: "'.$row['banned_by'].'" Suporrter: "'.$row['banned_by_name'].'" Grund: "'.$row['ban_reason'].'" Info: "'.$row['info'];
        }

        if ($result->num_rows > 0) {
            $stmt = $dbm->prepare("UPDATE acp_bans SET name = ?, ban_end = ?, banned_by = ?, banned_by_name = ?, ban_reason = ?, info = ? WHERE uid = ?");
            $stmt->bind_param("sssssss", $namenString, $_POST['playerBanTimeEnd'], $LogSupporterPid, $LogSupporterName, $_POST['playerBanMessage'], $_POST['playerBanNote'], $pid);
            $stmt->execute();

            $LogCategory = "editBanPlayer";
            $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Ban von: [' . $pid . '] "' . $namenString . '" aktualisiert. Vorher: '.$banVorher.' Nacher: "' .$_POST['playerBanTimeEnd'];
            log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $namenString, $_POST['playerBanMessage']);
            header("Refresh:0");

        } elseif (!$result->num_rows > 0) {
            $stmt = $dbm->prepare("INSERT INTO acp_bans (uid, name, ban_end, banned_by, banned_by_name, ban_reason, info) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $pid, $namenString, $_POST['playerBanTimeEnd'], $LogSupporterPid, $LogSupporterName, $_POST['playerBanMessage'], $_POST['playerBanNote']);
            $stmt->execute();

            $LogCategory = "createBanPlayer";
            $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat [' . $pid . '] "' . $namenString . '" bis "'.$_POST['playerBanTimeEnd'].' gebant.';
            log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $namenString,$_POST['playerBanMessage']);
            header("Refresh:0");

        }
    }
}

if (isset($_POST["playerUnbanSubmit"])) {
    if ($playerUnban) {
        $stmt = $dbm->prepare("SELECT * FROM acp_bans WHERE uid=?");
        $stmt->bind_param("s", $pid);
        $playerid = $pid;
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $banVorher = 'UID: "'.$row['uid'].'" Name: "'.$row['name'].'" Ban Start: "'.$row['ban_start'].'" Ban Ende: "'.$row['ban_end'].'" S-UID: "'.$row['banned_by'].'" Suporrter: "'.$row['banned_by_name'].'" Grund: "'.$row['ban_reason'].'" Info: "'.$row['info'];
        }

        if ($result->num_rows > 0) {
            $sql = "DELETE FROM acp_bans WHERE uid = $pid";
            if (mysqli_query($dbm, $sql)) {
                $LogCategory = "unbanPlayer";
                $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Ban von: [' . $pid . '] "' . $namenString . '" aufehoben. Aufgehobener Ban: '.$banVorher.'';
                log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
                header("Refresh:0");
            } else {
                error_log("Fehler bei 'playerUnbanSubmit'! <> " . mysqli_error($dbm));
                $LogCategory = "unbanPlayerError";
                $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat beim versuch: [' . $pid . '] "' . $namenString . '" zu entbannen einen Error ausgelöst.';
                log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
                header("Refresh:0");
            }
        } elseif (!$result->num_rows > 0) {
            $LogCategory = "unbanPlayerTry";
            $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat versucht: [' . $pid . '] "' . $namenString . '" zu entbannen.';
            log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
            header("Refresh:0");
        }
    }
}

// Bans
$bannedServer = false;
$stmt = $dbm->prepare("SELECT * FROM acp_bans WHERE uid=?");
$stmt->bind_param("s", $pid);
$playerid = $pid;
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
	$bannedServer = true;
}

$bannedRcon = false;
// $stmt = $dbl->prepare("SELECT bannedPlayerArrRcon FROM cache_bannedPlayer WHERE id=1");
// $stmt->execute();
// $result = $stmt->get_result();
// $row = mysqli_fetch_array($result);
// if (in_array($pid, json_decode($row[0]))) {
// 	$bannedRcon = true;
// }

$bannedRealRcon = false;

// $stmt = $dbl->prepare("SELECT guid FROM cache_bannedPlayerRcon");
// $stmt->execute();
// $result = $stmt->get_result();
// $row = mysqli_fetch_array($result);
// if (in_array($guid, $row)) {
// 	$bannedRealRcon = true;
// }

//Modals/////////////////////////
// Support (Notiz) Einträge
if (isset($_POST["noteSubmit"])) {
	if ($playersEditNotes) {

		$noteText = translate4($_POST['noteTextName']);
		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat eine Notiz für [' . $LogUserPid . '] "' . $LogUserName . '" geschrieben. NotizText: ' . $_POST['noteTextName'] . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
		header("Refresh:0");
	}
}

// Support (Notiz) Einträge Löschen Button
if (isset($_POST["deleteNoteSubmit"])) {
	if ($playersEditNotesDelete) {

		$stmt = $dbl->prepare("SELECT note, supID, supName  FROM acp_notes WHERE id=?");
		$stmt->bind_param("s", $_POST['noteDeleteSubmit']);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = mysqli_fetch_array($result);
		$noteToDelete = $row[0];
		$ownerIDToDelete = $row[1];
		$ownernameToDelete = $row[2];

		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat eine Notiz von [' . $LogUserPid . '] "' . $LogUserName . '" gelöscht. NotizText: ' . $noteToDelete . ' NotizOwner: [' . $ownerIDToDelete . '] ' . $ownernameToDelete . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$stmt = $dbl->prepare("DELETE FROM acp_notes WHERE id=?");
		$stmt->bind_param("i", $_POST['noteDeleteSubmit']);
		$stmt->execute();
		header("Refresh:0");
	}
}

//Geld geben
if (isset($_POST["giveMoneySubmit"])) {
	if ($playersEditMoney) {
		$stmt = $dbm->prepare("UPDATE players SET cash=?, bankacc=? WHERE pid=?");
		$stmt->bind_param("iis", $_POST['giveCash'], $_POST['giveBank'], $pid);
		$stmt->execute();

		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat das Geld von  [' . $LogUserPid . '] "' . $LogUserName . '" bearbeitet. Vorher: Cash: ' . number_format($cash, 0, ',', '.') . ', Bank: ' . number_format($bank, 0, ',', '.') . '  Nacher: Cash: ' . number_format($_POST['giveCash'], 0, ',', '.') . ', Bank: ' . number_format($_POST['giveBank'], 0, ',', '.') . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
		header("Refresh:0");
	}
}

//Level Edit
//Berechtigungscheck
$editLevelArray = [
    "cop" => $playersEditLevelCop,
    "swat" => $playersEditLevelSwat,
    "fbi" => $playersEditLevelFbi,
    "dea" => $playersEditLevelDea,
    "med" => $playersEditLevelMed,
    "fd" => $playersEditLevelFd,
    "doj" => $playersEditLevelDoj,
    "volunteers" => $playersEditLevelVolunteers,
    "tow" => $playersEditLeveltow,
    "rm" => $playersEditLevelRm,
    "taxi" => $playersEditLevelTaxi,
    "jcm" => $playersEditLevelJcm,
    "redgwh" => $playersEditLevelRedgwh,
    "autohausMM" => $playersEditLevelAutohausMM,
    "admin" => $playersEditLevelAdmin
];

$changeLevelButton = false;
$updateLevelArray = [];

foreach ($editLevelArray as $key => $value) {
    if ($value) {
        $updateLevelArray[$key] = $_POST['give' . ucfirst($key) . 'LevelName'];
        $changeLevelButton = true;
    } else {
        $updateLevelArray[$key] = ${$key . "Level"};
    }
}

if (isset($_POST["giveLevelSubmit"]) && $changeLevelButton) {
    $stmt = $dbm->prepare("
	UPDATE players SET
	coplevel=?,
	swatlevel=?,
	fbilevel=?,
	dealevel=?,
	mediclevel=?,
	fdlevel=?,
	dojlevel=?,
	volunteerslevel=?,
	towlevel=?,
	rmlevel=?,
	taxilevel=?,
	jcmlevel=?,
	redgwhlevel=?,
	autohausmmlevel=?,
	adminlevel=?
	WHERE pid=?");
    $stmt->bind_param("ssssssssssssssss",
        $updateLevelArray["cop"],
        $updateLevelArray["swat"],
        $updateLevelArray["fbi"],
        $updateLevelArray["dea"],
        $updateLevelArray["med"],
        $updateLevelArray["fd"],
        $updateLevelArray["doj"],
        $updateLevelArray["volunteers"],
        $updateLevelArray["tow"],
        $updateLevelArray["rm"],
        $updateLevelArray["taxi"],
        $updateLevelArray["jcm"],
        $updateLevelArray["redgwh"],
        $updateLevelArray["autohausMM"],
        $updateLevelArray["admin"],
        $pid
    );
    $stmt->execute();

    $LogCategory = "changePlayer";
    $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat die Level von [' . $LogUserPid . '] "' . $LogUserName . '" bearbeitet. Vorher: Cop-Level ' . $copLevel . ', SWAT-Level ' . $swatLevel . ', FBI-Level ' . $fbiLevel . ', DEA-Level ' . $deaLevel . ', Med-Level ' . $medLevel . ', FD-Level ' . $fdLevel . ', DoJ-Level ' . $dojLevel . ', V-Level ' . $volunteersLevel . ', tow-Level ' . $towlevel . ', RM-Level ' . $rmLevel . ', Taxi-Level ' . $taxiLevel . ', JCM-Level ' . $jcmLevel . ', RedGWH-Level ' . $redgwhLevel . ', AutohausMM-Level ' . $AutohausMMLevel . ', Admin-Level ' . $adminLevel . '  Nacher: Cop-Level ' . $updateLevelArray["cop"] . ', SWAT-Level ' . $updateLevelArray["swat"] . ', FBI-Level ' . $updateLevelArray["fbi"] . ', DEA-Level ' . $updateLevelArray["dea"] . ', Med-Level ' . $updateLevelArray["med"] . ', FD-Level ' . $updateLevelArray["fd"] . ', DoJ-Level ' . $updateLevelArray["doj"] . ', V-Level ' . $updateLevelArray["volunteers"] . ', tow-Level ' . $updateLevelArray["tow"] . ', RM-Level ' . $updateLevelArray["rm"] . ', Taxi-Level ' . $updateLevelArray["taxi"] . ', JCM-Level ' . $updateLevelArray["jcm"] . ', RedGWH-Level ' . $updateLevelArray["redgwh"] . ', AutohausMM-Level ' . $updateLevelArray["autohausMM"] . ', Admin-Level ' . $updateLevelArray["admin"] . ' ';
    log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
    header("Refresh:0");
}


//Name ändern
// if (isset($_POST["changeNameSubmit"])) {
// 	if ($playersEditName) {
// 		$translatedCiv = str_replace($nameCiv, ($_POST["changeNameCiv"]), ($rowNameChange["idcard_data_civ"]));
// 		$translatedCop = str_replace($nameCop, ($_POST["changeNameCop"]), ($rowNameChange["idcard_data_cop"]));
// 		$translatedMed = str_replace($nameMed, ($_POST["changeNameMed"]), ($rowNameChange["idcard_data_med"]));
// 		if (($rowNameChange["idcard_data_civ"]) == '"[]"') {
// 			$nameCiv = $_POST["changeNameCiv"];
// 			$translatedCiv = '"[`' . $nameCiv . '`,`06.07.1954`,`dshadhafdafshdhfsad`,`Malden`,`STR_capps_gender_male`,`dfsahhaffhdahfdas`,`75764 78654`,`AB+`]"';
// 		}
// 		if (($rowNameChange["idcard_data_cop"]) == '"[]"') {
// 			$nameCop = $_POST["changeNameCop"];
// 			$translatedCop = '"[`' . $nameCop . '`,`06.07.1954`,`dshadhafdafshdhfsad`,`Malden`,`STR_capps_gender_male`,`dfsahhaffhdahfdas`,`75764 78654`,`AB+`]"';
// 		}
// 		if (($rowNameChange["idcard_data_cop"]) == '"[]"') {
// 			$nameMed = $_POST["changeNameMed"];
// 			$translatedMed = '"[`' . $nameMed . '`,`06.07.1954`,`dshadhafdafshdhfsad`,`Malden`,`STR_capps_gender_male`,`dfsahhaffhdahfdas`,`75764 78654`,`AB+`]"';
// 		}
// 		$stmt = $dbm->prepare("UPDATE players SET idcard_data_civ=?, idcard_data_cop=?, idcard_data_med=? WHERE pid=?");
// 		$stmt->bind_param("ssss", $translatedCiv, $translatedCop, $translatedMed, $pid);
// 		$stmt->execute();

// 		$LogCategory = "changePlayer";
// 		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Namen von  [' . $LogUserPid . '] "' . $LogUserName . '" bearbeitet. Vorher: Civ-Name "' . $nameCiv . '", Cop-Name "' . $nameCop . '," Med-Name "' . $nameMed . '" Nacher:Civ-Name "' . $_POST["changeNameCiv"] . '", Cop-Name "' . $_POST["changeNameCop"] . '", Med-Name "' . $_POST["changeNameMed"] . '"  ';
// 		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
// 		header("Refresh:0");
// 	}
// }

//Gamblick Blacklist Change
if (isset($_POST["changeBlacklistSubmitName"])) {
	if ($playersEditGambling) {
		$stmt = $dbm->prepare("UPDATE players SET gamble_blacklist = ? WHERE pid=?");
		$stmt->bind_param("ss", $_POST['changeBlacklistName'], $pid);
		$stmt->execute();

		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat die Gambling Blacklist von  [' . $LogUserPid . '] "' . $LogUserName . '" bearbeitet. Vorher: ' . $gamblingBlacklist . ' Nacher: ' . $_POST['changeBlacklistName'] . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		if ($_POST['changeBlacklistName'] === 1) {
			$noteText = 'Gambling Blacklist gesetzt';
		} else {
			$noteText = 'Gambling Blacklist Freigegeben';
		}
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

//Whitelist Change
if (isset($_POST["addWhitelistNameSubmit"])) {
	if ($createWhitelist) {
		$WhitelistNames = $_POST['addWhitelistNameValue'];
		$whitelistNote = $_POST['whitelistNote'];

		if (isset($_POST["whitelistCheckbox"]) && $_POST["whitelistCheckbox"] == "on") {
			// Die Checkbox wurde ausgewählt (Zustand 1)
			$WhitelistCheckbox = 1;
		} else {
			// Die Checkbox wurde nicht ausgewählt (Zustand 0)
			$WhitelistCheckbox = 0;
		}
		// Die Werte aus dem Array ausgeben
		$TempArray = array();
		foreach ($WhitelistNames as $wert) {
			$TempArray[] = "`" . $wert . "`";
		}
		$WhitelistNames = '"[' . implode(",", $TempArray) . ']"';

		if ($WhitelistExists) {
			$stmt = $dbm->prepare("UPDATE acp_whitelist SET name=?, whitelisted=?, whitelisted_note=?, whitelisted_by=?, whitelisted_by_name=? WHERE uid=?");
			$stmt->bind_param("ssssss", $WhitelistNames,$WhitelistCheckbox, $whitelistNote, $LogSupporterPid, $LogSupporterName, $pid);
		} else {
			$stmt = $dbm->prepare("INSERT INTO acp_whitelist (name,whitelisted,whitelisted_note,whitelisted_by, whitelisted_by_name,uid) VALUES (?,?,?,?,?,?)");
			$stmt->bind_param("ssssss", $WhitelistNames,$WhitelistCheckbox, $whitelistNote, $LogSupporterPid, $LogSupporterName, $pid);
		}

		$stmt->execute();



		$LogCategory = "editWhitelist";
		$LogText = '[' . $_POST['supporterGroup'] . '] ' . $_POST['supporterName'] . ' hat die Whitelist von: [' . $pid . '] "' . $WhitelistNames . '" aktualisiert auf: "' . 'Whitelist: ' . $WhitelistCheckbox . 'Namen: ' . $WhitelistNames . 'Kommentar: ' . $whitelistNote;
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Whitelist aktualisiert auf: "' . 'Whitelist: ' . ' ' . $WhitelistCheckbox . ' Namen: ' . $WhitelistNames . ' Kommentar: ' . translate4($whitelistNote);
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

//civGear
if (isset($_POST["civGearSubmit"])) {
	if ($playersEditGear) {
		$valueAcp = '"' . $_POST['gearCiv'] . '"';
		$typeAcp = '"GearErsetzen"';
		if (isPlayerOnline($dbm, $pid)) {
			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		} else {
			$stmt = $dbm->prepare("UPDATE players SET civ_gear = ? WHERE pid=?");
			$stmt->bind_param("ss", $valueAcp, $pid);
		}

		$stmt->execute();

		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat das Civ-Gear von  [' . $LogUserPid . '] "' . $LogUserName . '" überschrieben.  Vorher: ' . $civ_gearArr . ' Nacher: ' . $_POST['gearCiv'] . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Civ-Gear ersetzt.  Vorher: ' . translate4($civ_gearArr) . ' Nacher: ' . translate4($_POST['gearCiv']) . '';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}
//copGear
if (isset($_POST["copGearSubmit"])) {
	if ($playersEditGear) {
		$valueAcp = '"' . $_POST['gearCop'] . '"';
		$typeAcp = '"GearErsetzen"';
		if (isPlayerOnline($dbm, $pid)) {
			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		} else {
			$stmt = $dbm->prepare("UPDATE players SET cop_gear = ? WHERE pid=?");
			$stmt->bind_param("ss", $valueAcp, $pid);
		}

		$stmt->execute();

		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat das Cop-Gear von  [' . $LogUserPid . '] "' . $LogUserName . '" überschrieben.  Vorher: ' . $civ_gearArr . ' Nacher: ' . $_POST['gearCop'] . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Cop-Gear ersetzt.  Vorher: ' . translate4($cop_gearArr) . ' Nacher: ' . translate4($_POST['gearCop']) . '';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}
//medGear
if (isset($_POST["medGearSubmit"])) {
	if ($playersEditGear) {
		$typeAcp = '"GearErsetzen"';
		$valueAcp = '"' . $_POST['gearMed'] . '"';
		if (isPlayerOnline($dbm, $pid)) {
			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		} else {
			$stmt = $dbm->prepare("UPDATE players SET med_gear = ? WHERE pid=?");
			$stmt->bind_param("ss", $valueAcp, $pid);
		}

		$stmt->execute();

		$LogCategory = "changePlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat das Med-Gear von  [' . $LogUserPid . '] "' . $LogUserName . '" überschrieben.  Vorher: ' . $civ_gearArr . ' Nacher: ' . $_POST['gearMed'] . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Med-Gear ersetzt.  Vorher: ' . translate4($cop_gearArr) . ' Nacher: ' . translate4($_POST['gearMed']) . '';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}



// require_once 'scripts/arc/arc.php';

// use \Nizarii\ARC;
//rconKickSubmit
// if (isset($_POST["rconKickSubmit"])) {
// 	if ($kickPlayerRcon) {


// 		$rcon = new ARC('185.117.3.122', 'EmVN9nXARJk8tuvRq4Hc3D99r4HLvGRV', 2307, [
// 			'timeoutSec' => 2
// 		]);
// 		$rconKickGrund = $_POST["rconKickText"] . " | @" . $LogSupporterName;
// 		$arrr =  $rcon->getPlayersArray();

// 		foreach ($arrr as $key => $value) {
// 			if (in_array($guid, $value)) {
// 				$rcon->kickPlayer($value[0], $rconKickGrund);
// 			}
// 		}

// 		$LogCategory = "kickBan";
// 		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" per rcon gekickt. ';

// 		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
// 		header("Refresh:0");
// 	}
// }

// if (isset($_POST["unbanRconSubmit"])) {
// 	if ($unbanRealRcon) {

// 		$rcon = new ARC('185.117.3.122', 'EmVN9nXARJk8tuvRq4Hc3D99r4HLvGRV', 2307, [
// 			'timeoutSec' => 2
// 		]);
// 		$arrr =  $rcon->getBansArray();

// 		foreach ($arrr as $key => $value) {
// 			if (in_array($guid, $value)) {
// 				$rcon->removeBan(intval($value[0]));
// 				$rcon->loadBans();
// 			}
// 		}

// 		$LogCategory = "kickBan";
// 		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" von der realRCON-Liste(autodetect) entbannt. ';

// 		$editUnbanBanRealRconShortNote = $_POST["editUnbanBanRealRconShortNote"];
// 		$stmt = $dbl->prepare("INSERT INTO acp_logs (category, text, supporter_pid, supporter_name, supporter_group, user_pid, user_name, short_note) VALUES (?,?,?,?,?,?,?,?)");
// 		$stmt->bind_param("ssssssss", $LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName, $editUnbanBanRealRconShortNote);
// 		$stmt->execute();

// 		header("Refresh:0");
// 	}
// }

// if (isset($_POST["banRconSubmit"])) {
// 	if ($banRealRcon) {

// 		$rcon = new ARC('185.117.3.122', 'EmVN9nXARJk8tuvRq4Hc3D99r4HLvGRV', 2307, [
// 			'timeoutSec' => 2
// 		]);
// 		$rconBanGrund = $_POST["banRconText"] . " | @" . $LogSupporterName;

// 		$arrr =  $rcon->getPlayersArray();

// 		foreach ($arrr as $key => $value) {
// 			if (in_array($guid, $value)) {
// 				$rcon->kickPlayer($value[0], $rconBanGrund);
// 			}
// 		}

// 		sleep(2);

// 		$rcon->addBan($guid, $rconBanGrund, 0);
// 		$rcon->loadBans();


// 		$LogCategory = "kickBan";
// 		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" per realRCON gebannt. ';

// 		$editBanRealRconShortNote = $_POST["editBanRealRconShortNote"];
// 		$stmt = $dbl->prepare("INSERT INTO acp_logs (category, text, supporter_pid, supporter_name, supporter_group, user_pid, user_name, short_note) VALUES (?,?,?,?,?,?,?,?)");
// 		$stmt->bind_param("ssssssss", $LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName, $editBanRealRconShortNote);
// 		$stmt->execute();

// 		header("Refresh:0");
// 	}
// }

// if (isset($_POST["editBanRconSubmit"])) {
// 	if ($editBanRcon) {

// 		$rcon = new ARC('185.117.3.122', 'EmVN9nXARJk8tuvRq4Hc3D99r4HLvGRV', 2307, [
// 			'timeoutSec' => 2
// 		]);
// 		$rconBanGrund = '' . $_POST["editBanRconText"] . ' | ' . $LogUserName . '';

// 		$arrr =  $rcon->getPlayersArray();

// 		foreach ($arrr as $key => $value) {
// 			if (in_array($guid, $value)) {
// 				$rcon->kickPlayer($value[0], $rconBanGrund);
// 			}
// 		}

// 		$stmt = $dbs->prepare("INSERT INTO bans (pid,guid,reason,creator_name ) VALUES (?,?,?,?)");
// 		$stmt->bind_param("ssss", $pid, $guid, $rconBanGrund, $LogSupporterName);
// 		$stmt->execute();

// 		$LogCategory = "kickBan";
// 		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" per Rcon gebannt. ';

// 		$editBanRconShortNote = $_POST["editBanRconShortNote"];
// 		$stmt = $dbl->prepare("INSERT INTO acp_logs (category, text, supporter_pid, supporter_name, supporter_group, user_pid, user_name, short_note) VALUES (?,?,?,?,?,?,?,?)");
// 		$stmt->bind_param("ssssssss", $LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName, $editBanRconShortNote);
// 		$stmt->execute();
// 		header("Refresh:0");
// 	}
// }

// if (isset($_POST["editBanServerSubmit"])) {
// 	if ($editBanServer) {

// 		$stmt = $dbm->prepare("INSERT INTO bans (pid_player,name_player,banned_from_name,banned_from_pid,reason,plakate,ban_end ) VALUES (?,?,?,?,?,?,DATE_ADD(NOW(), INTERVAL ? HOUR))");
// 		$stmt->bind_param("sssssii", $pid, $name, $LogSupporterName, $LogSupporterPid, $_POST["serverBanGrund"], $_POST["serverBanPlakate"], $_POST["serverBanTime"]);
// 		$stmt->execute();

// 		sleep(1);
// 		$typeAcp = '"PlakateSetzen"';
// 		$valueAcp = 1;
// 		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
// 		$stmt->bind_param("ssis", $pid, $typeAcp, $valueAcp, $LogSupporterName);
// 		$stmt->execute();


// 		$LogCategory = "kickBan";
// 		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" für ' . $_POST["serverBanTime"] . ' Stunden und ' . $_POST["serverBanPlakate"] . ' Plakate per ServerBan gebannt. ';
// 		$serverBanShortNote = $_POST["serverBanShortNote"];
// 		$stmt = $dbl->prepare("INSERT INTO acp_logs (category, text, supporter_pid, supporter_name, supporter_group, user_pid, user_name, short_note) VALUES (?,?,?,?,?,?,?,?)");
// 		$stmt->bind_param("ssssssss", $LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName, $serverBanShortNote);
// 		$stmt->execute();
// 		header("Refresh:0");
// 	}
// }

if (isset($_POST["tsWarn1Submit"])) {
	if ($playersEditTsWarn1) {

		$typeAcp = '"TSWarning1"';
		$valueAcp = 1;
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssis", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" TS-Warning 1 ausgesprochen. ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'TS3-Warning Stufe 1 ausgelöst';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

if (isset($_POST["tsWarn2Submit"])) {
	if ($playersEditTsWarn2) {

		$typeAcp = '"TSWarning2"';
		$valueAcp = 1;
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssis", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" TS-Warning 2 ausgesprochen. ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'TS3-Warning Stufe 2 ausgelöst';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

if (isset($_POST["tsWarn3Submit"])) {
	if ($playersEditTsWarn3) {

		$typeAcp = '"TSWarning3"';
		$valueAcp = 1;
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssis", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" TS-Warning 3 ausgesprochen. ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'TS3-Warning Stufe 3 ausgelöst';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}


if (isset($_POST["tfarDisableSubmit"])) {
	if ($playersEdittfarDisable) {

		$typeAcp = '"TFARCheckDisable"';
		$valueAcp = 1;
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssis", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" die TFAR ueberpruefung deaktiviert. ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'TFAR Überprüfung deaktiviert';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

if (isset($_POST["changeMoneyOnlineSubmit"])) {
	if ($playersEditMoney) {

		$typeAcp = '"GeldErsatz"';
		$valueAcp = $_POST["changeMoneyOnlineValue"];
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssis", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '" Geld erstattet Summe: ' . $valueAcp . '. ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Geld Online erstattet: ' . $valueAcp;
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

if (isset($_POST["changeKofferraumOnlineSubmit"])) {
	if ($unbugVehiclePlayer) {

		$typeAcp = '"KofferraumEntbuggen"';
		$valueAcp = 1;
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Spieler  [' . $LogUserPid . '] "' . $LogUserName . '"seinen Kofferraum entbuggt. ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Kofferraum entbuggt';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

if (isset($_POST["playersEditAdminMessageSubmit"])) {
	if ($playersEditAdminMessage) {

		$typeAcp = '"AdminMessage"';
		$valueAcp = '["' . $_POST["playersEditAdminMessageValue"] . ' | ' . $LogSupporterName . '"]';
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat dem Spieler  [' . $LogUserPid . '] "' . $LogUserName . '"eine Admin-Nachricht geschrieben. Nachricht: ' . $valueAcp . ' ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Adminnachricht: ' . $valueAcp;
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

if (isset($_POST["revivePlayerSubmit"])) {
	if ($playersEditRevive) {

		$typeAcp = '"RevivePlayer"';
		$valueAcp = 1;
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat dem Spieler  [' . $LogUserPid . '] "' . $LogUserName . ' mittels Life-Support revived ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Wiederbelebung Angefordert';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

if (isset($_POST["playersEditPositionSubmit"])) {
	if ($playersEditPosition) {

		$typeAcp = '"PositionReset"';
		$valueAcp = '[]';
		$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
		$stmt->execute();

		$LogCategory = "playerChange";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat dem Spieler  [' . $LogUserPid . '] "' . $LogUserName . ' mittels Life-Support das Spawnmenü erneut gegeben ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'Spawnauswahl Angefordert';
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
	}
}

// if (isset($_POST["SubmitGiveItem"])) {
// 	if ($_SESSION["rightsArr"]["Item_give"] == true) {
// 		if ($_SESSION["rightsArr"]["give_" .  $_REQUEST["VarNameItem"]] == true) {
// 			#Arr   [0,`LMG_Zafir_F`,2,-1]
// 			$stmt = $dbm->prepare("SELECT civ_locker , cop_locker , med_locker FROM locker WHERE playerid=?");
// 			$stmt->bind_param("s", $pid);
// 			$stmt->execute();
// 			$result = $stmt->get_result();
// 			$row = mysqli_fetch_array($result);
// 			$row = str_replace(']"', "]", str_replace('"[', "[", (translate2($row[$_REQUEST["LockerSelect"]]))));
// 			$row = json_decode($row);

// 			$locker = '"[';
// 			$u = 0;

// 			$ok = true;
// 			foreach ($row as $key => $val) {

// 				$meng = $val[2];

// 				if ($_REQUEST["VarNameItem"] == $val[1]) {
// 					$meng = $val[2] + $_REQUEST["ItemAmount"];
// 					$ok = false;
// 				}
// 				if ($u >= 1) {
// 					$locker .= ",[$val[0],`$val[1]`,$meng,$val[3]]";
// 				} else {
// 					$locker .= "[$val[0],`$val[1]`,$meng,$val[3]]";
// 				}
// 				$u++;
// 				unset($meng);
// 				unset($val);
// 			}

// 			if ($ok == true) {
// 				$itemToAdd = $_REQUEST["VarNameItem"];
// 				$MengeToAdd = $_REQUEST["ItemAmount"];

// 				if ($u >= 1) {
// 					$locker .= ",";
// 				}
// 				$locker .= "[0,`$itemToAdd`,$MengeToAdd,-1]";
// 			}

// 			$locker .= ']"';

// 			$playerid = $pid;
// 			$ToLocker = $_REQUEST["LockerSelect"];

// 			if (($ToLocker == "civ_locker") || ($ToLocker == "med_locker") || ($ToLocker == "cop_locker")) {


// 				$field = $_REQUEST['LockerSelect'];
// 				$stmt = $dbm->prepare("UPDATE locker SET `$ToLocker` = '$locker' WHERE playerid = ?");
// 				$stmt->bind_param("s",  $playerid);
// 				$stmt->execute();

// 				$LogCategory = "Give Item Locker";
// 				$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat dem Spieler  [' . $LogUserPid . '] "' . $LogUserName .  '"  Das item ' . $_REQUEST["VarNameItem"] . ' mit der Menge: ' . $_REQUEST["ItemAmount"] . "  ins Schließfach $ToLocker gelegt";

// 				log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
// 				header("Refresh:0");
// 			}
// 		}
// 	}
// }

function addVitems($dbm, $pid, $vitems) {
    $stmt = $dbm->prepare("SELECT civ_gear FROM players WHERE pid=?");
    $stmt->bind_param("s", $pid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

	$string = $row["civ_gear"];
    $json = str_replace('`', '"', $string);
    $civGear = json_decode(trim($json, '"'), true);




    // Prüfen, ob der vItems-Bereich vorhanden ist und erstellen, falls nicht vorhanden
    if (!isset($civGear[1])) {
        $civGear[0] = [];
        $civGear[1] = [];
    }

    foreach ($vitems as $vitem) {
        $found = false;
        for ($i = 0; $i < count($civGear[1]); $i++) {
            if ($civGear[1][$i][0] === $vitem[0]) {
                $civGear[1][$i][1] += $vitem[1];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $civGear[1][] = $vitem;
        }
    }


    $updatedCivGear = ArrayToString($civGear);
    $stmt = $dbm->prepare("UPDATE players SET civ_gear = ? WHERE pid = ?");
    $stmt->bind_param("ss", $updatedCivGear, $pid);
    $stmt->execute();
}

if (isset($_POST["SubmitGiveItemPLayer"])) {
	if ($_SESSION["rightsArr"]["Item_give"] == true) {
		$itemArray = [];
		$itemArrayforLog = [];
		foreach ($viteminfoarray as $key => $value) {
			if (isset($_POST["ItemGive_" . $key])) {
				$amount = (int)$_POST["ItemGive_" . $key];
				if ($amount != 0) {
					$itemArray[] = [$key, $amount];
					$itemArrayforLog[] = [$value["displayname"], $amount];
				}
			}
		}
		$typeAcp = '"ItemErsetzen"';
		$valueAcp =  ArrayToString($itemArray);

		if (isPlayerOnline($dbm, $pid)) {
			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
			$stmt->execute();
		} else {
			addVitems($dbm, $pid, $itemArray);
		}



		$LogCategory = "Give Item";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat dem Spieler  [' . $LogUserPid . '] "' . $LogUserName .  '"  Folgende Items gegeben: ' . ArrayToString($itemArrayforLog);
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


		$noteName = translate4($name);
		$playtimeInsert = round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0);
		$noteText = 'V-Items Gegeben:' . ArrayToString($itemArrayforLog);
		$stmt = $dbl->prepare("INSERT INTO acp_notes (note, playerName, playerID, playerPlaytime, supName, supID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssiss", $noteText, $noteName, $pid, $playtimeInsert, $username, $userPID);
		$stmt->execute();

		header("Refresh:0");
		// }
	}
}

if (isset($_POST["deleteSchuldeneSubmit"])) {
	if ($_SESSION["rightsArr"]["del_supportschulden"]) {
		$ToDel = $_POST["DelSchuldenSubmit"];
		$stmt = $dbm->prepare("SELECT * FROM supportschulden WHERE id=?");
		$stmt->bind_param("s", $ToDel);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = mysqli_fetch_array($result);
		$Money = $row["amount"];
		$Text = $row["reason"];
		$tillDate = $row["till_date"];

		$LogCategory = "Schulden Del";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat ein Schuldeneintrag von [' . $LogUserPid . '] "' . $LogUserName . '" gelöscht. Schulden: ' . $Money . ' /  schulden Text: ' . $Text .  ' / Zurück zahlen bis:' . $tillDate;
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$stmt = $dbm->prepare("DELETE FROM supportschulden WHERE id=?");
		$stmt->bind_param("i", $ToDel);
		$stmt->execute();
		header("Refresh:0");
	}
}

if (isset($_POST["createSchuldeneSubmit"])) {
	if ($_SESSION["rightsArr"]["Add_support_schulden"] == true) {
		$amount = $_POST["schuldenhoehe"];
		$till_Date = $_POST["SelectDate"];
		$share = $_POST["shareProzent"];
		$reason = $_POST["schuldReason"];

		$till_Date = strtotime($till_Date);
		$till_Date = date("Y-m-d", $till_Date);

		$LogCategory = "Schulden Add";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat ein Schuldeneintrag von [' . $LogUserPid . '] "' . $LogUserName . '" hinzugefügt. Schulden: ' . $Money . ' /  schulden Rate: ' . $share .  ' / Zurück zahlen bis: ' . $till_Date . ' Grund: ' . $reason;
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

		$stmt = $dbm->prepare("INSERT INTO supportschulden (pid,reason,amount,share,till_Date) VALUES (?,?,?,?,?)");
		$stmt->bind_param("sssss", $pid, $reason, $amount, $share, $till_Date);
		$stmt->execute();
		#header("Refresh:0");
	}
}


//LogVisit
$LogCategory = "SiteActivity";
$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat sich die die Seite von dem Spieler [' . $LogUserPid . '] "' . $LogUserName . '" angeschaut';
log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);

function webhook($m, $webhookurl)
{
	#Check
	$timestamp = date("c", strtotime("now"));
	$json_data = json_encode([
		"tts" => false,
		"embeds" => [
			[
				// Embed Title
				"title" => "Info",

				// Embed Type
				"type" => "rich",

				// Embed Description
				"description" => "$m",

				// URL of title link
				//"url" => $url,

				// Timestamp of embed must be formatted as ISO8601
				"timestamp" => $timestamp,

				// Embed left border color in HEX
				"color" => hexdec("8000FF"),

				/*"image" => [
                     "url" => $thumbnail
                 ],*/
			]
		]
	], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

	$ch = curl_init($webhookurl);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$response = curl_exec($ch);
	// If you need to debug, or find out why you can't send message uncomment line below, and execute script.
	// echo $response;
	curl_close($ch);
}

?>

<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
<html>

<head>
	<!-- Basic -->
	<meta charset="UTF-8">

	<title><?php echo "DB von: " . $namenString . "";?></title>
	<meta name="keywords" content="HTML5 Admin Template" />
	<meta name="description" content="Porto Admin - Responsive HTML5 Template">
	<meta name="author" content="okler.net">

	<!-- Mobile Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

	<!-- Web Fonts  -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:100,300,400,600,800,900|Shadows+Into+Light" rel="stylesheet" type="text/css">

	<!-- Vendor CSS -->
	<link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" href="vendor/animate/animate.css">

	<link rel="stylesheet" href="vendor/font-awesome/css/all.css" />
	<link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" />
	<link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

	<!-- Specific Page Vendor CSS -->
	<link rel="stylesheet" href="vendor/pnotify/pnotify.custom.css" />
	<link rel="stylesheet" href="vendor/select2/css/select2.css" />
	<link rel="stylesheet" href="vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
	<link rel="stylesheet" href="vendor/datatables/media/css/dataTables.bootstrap4.css" />

	<!-- Theme CSS -->
	<link rel="stylesheet" href="css/theme.css" />

	<!-- Skin CSS -->
	<link rel="stylesheet" href="css/skins/default.css" />

	<!-- Theme Custom CSS -->
	<link rel="stylesheet" href="css/custom.css">

	<style>
	.switch {
	position: relative;
	display: inline-block;
	width: 60px;
	height: 34px;
	}

	.switch input {
	opacity: 0;
	width: 0;
	height: 0;
	}

	.slider {
	position: absolute;
	cursor: pointer;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-color: #ccc;
	-webkit-transition: .4s;
	transition: .4s;
	}

	.slider:before {
	position: absolute;
	content: "";
	height: 26px;
	width: 26px;
	left: 4px;
	bottom: 4px;
	background-color: white;
	-webkit-transition: .4s;
	transition: .4s;
	}

	input:checked + .slider {
	background-color: #47a447;
	}

	input:focus + .slider {
	box-shadow: 0 0 1px #2196F3;
	}

	input:checked + .slider:before {
	-webkit-transform: translateX(26px);
	-ms-transform: translateX(26px);
	transform: translateX(26px);
	}

	/* Rounded sliders */
	.slider.round {
	border-radius: 34px;
	}

	.slider.round:before {
	border-radius: 50%;
	}
	</style>

	<!-- Head Libs -->
	<script src="vendor/modernizr/modernizr.js"></script>
</head>

<body onload="Select(); SelectItemForPlayer(); AddSchulden();" class="loading-overlay-showing" data-loading-overlay>
	<div class="loading-overlay">
		<div class="bounce-loader">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>

	<section class="body">
		<!-- include Header-->
		<?php
		include 'includes/header.php';
		?>
		<!-- include Header-->
		<div class="inner-wrapper">
			<!-- include SideBar-->
			<?php
			include 'includes/sideBar.php';
			?>
			<!-- include SideBar-->
			<section role="main" class="content-body card-margin">
				<header class="page-header">
					<h2>Spieler Übersicht</h2>
				</header>
				<!-- start: page -->
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<section class="card">
							<header class="card-header">
								<div class="row">
									<div class="col-lg-6 col-xl-12">
										<div class="row">
											<div class="col-lg-4 col-xl-4">
												<h2 class="card-title"><?php echo $namenString; ?></h2>
											</div>
											<div class="col-lg-4 col-xl-4">
											</div>
											<div class="col-lg-4 col-xl-4">
												<?php if (isPlayerOnline($dbm, $pid)) {
													echo '<h2 class="card-title text-right text-success" >Online</h2>';
												} else {
													echo '<h2 class="card-title text-right text-danger" >Offline</h2>';
												} ?>
											</div>
										</div>
									</div>
								</div>
							</header>
						</section>
					</div>
				</div>
				<?php
				if (($bannedServer) || ($bannedRcon) || ($bannedRealRcon)) {
					echo
					'<div class="row">
                        <div class="col-lg-6 col-xl-12">
                            <section class="card">
                                <header class="card-header">
                                    <div class="card-actions">
                                        <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                                    </div>
                                    <h2 class="card-title">Wichtige Info</h2>
                                </header>
                                <div class="card-body">';
				}
				if (($bannedServer) || ($bannedRcon) || ($bannedRealRcon)) {
					echo
						'<div class="alert alert-danger">
							<div style="text-align:center; font-size: 24px">
								<strong>Dieser gottverdammte Spieler ist zur Zeit gebannt!</strong>
							</div>
                        </div>';
				}
				if (($bannedServer) || ($bannedRcon) || ($bannedRealRcon)) {
					echo
									'</div>
                            	</section>
                        	</div>
                    	</div>';
				}
				?>
				<div class="row">
					<div class="col-lg-6 col-xl-12">
						<div class="row">
							<div class="col-lg-12 col-xl-6">
								<div class="row">
									<div class="col-lg-12 col-xl-12">
										<section class="card card-featured-left card-featured-primary mb-4">
											<header class="card-header">
												<h3 class="card-title text-dark">Spielerinformationen</h3>
											</header>
											<div class="card-actions">
												<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
											</div>
											<div class="card-body">
												<div class="tabs tabs-vertical tabs-right tabs-primary">
													<ul class="nav nav-tabs">
														<li class="nav-item active">
															<a class="nav-link text-dark" href="#nameID" data-toggle="tab">Name & IDs</a>
														</li>
														<li class="nav-item">
															<a class="nav-link text-dark" href="#moneyEXP" data-toggle="tab">Money & EXP </a>
														</li>
														<li class="nav-item">
															<a class="nav-link text-dark" href="#TimeStats" data-toggle="tab">Playtime & Status</a>
														</li>
														<li class="nav-item">
															<a class="nav-link text-dark" href="#Level" data-toggle="tab">Level</a>
														</li>
														<!-- <li class="nav-item">
															<a class="nav-link text-dark" href="#locker" data-toggle="tab">Schließfächer</a>
														</li> -->
														<li class="nav-item">
															<a class="nav-link text-dark" href="#civStuff" data-toggle="tab">Civ Informationen</a>
														</li>
														<li class="nav-item">
															<a class="nav-link text-dark" href="#copStuff" data-toggle="tab">Cop Informationen</a>
														</li>
														<li class="nav-item">
															<a class="nav-link text-dark" href="#medStuff" data-toggle="tab">EMS Informationen</a>
														</li>
													</ul>
													<div class="tab-content">
														<div id="nameID" class="tab-pane active">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Name</h5><?php echo $name; ?>
																		<h5 class="text-dark">UID</h5><?php echo $id; ?>
																		<h5 class="text-dark">Player-ID</h5><?php echo $pid; ?>
																		<h5 class="text-dark">GUID</h5><?php echo $guid; ?>
																		<h5 class="text-dark">Admin Level</h5><?php echo $adminLevel; ?>
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Alias</h5><?php
																		foreach ($alias as $index => $value) {
																			echo $value . "<br>";
																		}?>
																		<h5 class="text-dark">Erlaubte Namen</h5><?php
																		foreach ($WhitelistNameArray as $index => $value) {
																			echo $value . "<br>";
																		}?>
																		<!-- <h5 class="text-dark">GangName</h5><?php echo "In Arbeit"; ?>
																		<h5 class="text-dark">GangTag</h5><?php echo "In Arbeit"; ?>
																		<h5 class="text-dark">Gang-ID</h5><?php echo "In Arbeit"; ?> -->
																	</div>
																</div>
															</div>
														</div>


														<div id="moneyEXP" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Cash</h5><?php echo number_format($cash, 0, ',', '.'); ?> $
																		<h5 class="text-dark">Bank</h5><?php echo number_format($bank, 0, ',', '.'); ?> $
																		<h5 class="text-dark">Garagen-Wert</h5><?php
																		$stmt = $dbm->prepare("SELECT *
																		FROM vehicles
																		JOIN acp_vehicles ON vehicles.classname = acp_vehicles.classname
																		WHERE vehicles.pid = ?");

																		$stmt->bind_param("s", $pid);
																		$stmt->execute();
																		$result = $stmt->get_result();
																		$priceVehValue = 0;
																		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
																			if ($row['price'] != -1) {
																				$priceVehValue = $priceVehValue + $row['price'];
																			}
																		};
																		echo number_format($priceVehValue, 0, ',', '.');
																		?> $

																		<!-- <h5 class="text-dark">Paycheck Insgesamt verdient</h5><?php //echo number_format($paycheckTotal, 0, ',', '.'); ?> $ -->
																		<!-- <h5 class="text-dark">Gesamt erwitschaftet</h5><?php //echo number_format($soldTotal, 0, ',', '.');  ?> $ -->
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Exp Level</h5><?php echo $expLevel; ?>
																		<h5 class="text-dark">Exp Total</h5><?php echo $expTotal; ?>
																		<h5 class="text-dark">Exp Perkpoints</h5><?php echo $expPerkPoints; ?>
																	</div>
																</div>
															</div>
														</div>
														<div id="TimeStats" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">First Seen</h5><?php echo $firstSeen; ?>
																		<h5 class="text-dark">Last Seen</h5><?php echo $lastSeen; ?>
																		<h5 class="text-dark">Gambling Blacklist</h5><?php echo translate3($gamblingBlacklist); ?>
																		<!-- <h5 class="text-dark">Anticheat</h5> <?php //echo $forceAnticheatString; ?> -->
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Gesamte Spielzeit</h5><?php echo round((($playTime[0] / 60) + ($playTime[1] / 60) + ($playTime[2] / 60)), 0); ?>
																		<h5 class="text-dark">Civ Spielzeit</h5><?php echo round($playTime[2] / 60, 0); ?>
																		<h5 class="text-dark">Cop Spielzeit</h5><?php echo round($playTime[0] / 60, 0); ?>
																		<h5 class="text-dark">EMS Spielzeit</h5><?php echo round($playTime[1] / 60, 0); ?>
																	</div>
																</div>
															</div>
														</div>
														<div id="Level" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Admin Level</h5><?php echo $adminLevel; ?>
																		<h5 class="text-dark">Cop Level</h5><?php echo $copLevel; ?>
																		<h5 class="text-dark">EMS Level</h5><?php echo $medLevel; ?>
																		<h5 class="text-dark">DoJ Level</h5><?php echo $dojLevel; ?>
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<!-- <h5 class="text-dark">Vol Level</h5><?php // echo $volunteersLevel; ?> -->
																		<h5 class="text-dark">tow Level</h5><?php echo $towlevel; ?>
																		<h5 class="text-dark">RM Level</h5><?php  echo $rmLevel; ?>
																		<h5 class="text-dark">Taxi Level</h5><?php echo $taxiLevel; ?>
																		<h5 class="text-dark">JCM Level</h5><?php echo $jcmLevel; ?>
																		<!-- <h5 class="text-dark">Red GWH Level</h5><?php // echo $redgwhLevel; ?> -->
																		<h5 class="text-dark">AutohausMM Level</h5><?php echo $AutohausMMLevel; ?>
																	</div>
																</div>
															</div>
														</div>
														<!-- <div id="locker" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Schließfach Level Civ</h5><?php //echo $civ_locker; ?>
																		<h5 class="text-dark">Schließfach Level Cop</h5><?php //echo $cop_locker; ?>
																		<h5 class="text-dark">Schließfach Level EMS</h5><?php //echo $med_locker; ?>
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Name Civ</h5><?php //echo $nameCiv; ?>
																		<h5 class="text-dark">Name Cop </h5><?php //echo $nameCop; ?>
																		<h5 class="text-dark">Name Med</h5><?php //echo $nameMed; ?>
																	</div>
																</div>
															</div>
														</div> -->
														<div id="civStuff" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">DoJ Level</h5><?php echo $dojLevel; ?>
																		<!-- <h5 class="text-dark">Vol Level</h5><?php // echo $volunteersLevel; ?> -->
																		<h5 class="text-dark">tow Level</h5><?php echo $towlevel; ?>
																		<!-- <h5 class="text-dark">RM Level</h5><?php // echo $rmLevel; ?> -->
																		<h5 class="text-dark">Taxi Level</h5><?php echo $taxiLevel; ?>
																		<h5 class="text-dark">JCM Level</h5><?php echo $jcmLevel; ?>
																		<!-- <h5 class="text-dark">Red GWH Level</h5><?php // echo $redgwhLevel; ?> -->
																		<h5 class="text-dark">AutohausMM Level</h5><?php echo $AutohausMMLevel; ?>
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Spielzeit</h5><?php echo round($playTime[2] / 60, 0); ?>
																		<h5 class="text-dark">Essen</h5><?php echo $civ_statsArr[0]; ?>
																		<h5 class="text-dark">Trinken</h5><?php echo $civ_statsArr[1]; ?>
																	</div>
																</div>
															</div>
														</div>
														<div id="copStuff" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Cop Level</h5><?php echo $copLevel; ?>
																		<h5 class="text-dark">SWAT Level</h5><?php echo $swatLevel; ?>
																		<h5 class="text-dark">FBI Level</h5><?php echo $fbiLevel; ?>
																		<h5 class="text-dark">DEA Level</h5><?php echo $deaLevel; ?>
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Spielzeit</h5><?php echo round($playTime[0] / 60, 0); ?>
																		<h5 class="text-dark">Essen</h5><?php echo $cop_statsArr[0]; ?>
																		<h5 class="text-dark">Trinken</h5><?php echo $cop_statsArr[1]; ?>
																	</div>
																</div>
															</div>
														</div>
														<div id="medStuff" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">EMS Level</h5><?php echo $medLevel; ?>
																		<h5 class="text-dark">FD Level</h5><?php echo $fdLevel; ?>
																	</div>
																	<div class="col-lg-6 col-xl-6">
																		<h5 class="text-dark">Spielzeit</h5><?php echo round($playTime[1] / 60, 0); ?>
																		<h5 class="text-dark">Essen</h5><?php echo $med_statsArr[0]; ?>
																		<h5 class="text-dark">Trinken</h5><?php echo $med_statsArr[1]; ?>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</section>
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-xl-6">
								<div class="row">
									<div class="col-lg-12 col-xl-12">
										<section class="card card-featured-left card-featured-primary mb-4">
											<header class="card-header">
												<h3 class="card-title text-dark">Support-Aktionen</h3>
											</header>
											<div class="card-actions">
												<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
											</div>
											<div class="card-body">
												<div class="tabs tabs-primary">
													<ul class="nav nav-tabs">
														<li class="nav-item active">
															<a class="nav-link text-dark" href="#somethingImportant" data-toggle="tab">Something Important</a>
														</li>
														<li class="nav-item">
															<a class="nav-link text-dark" href="#OfflineTools" data-toggle="tab">Offline-Tools</a>
														</li>
														<li class="nav-item">
															<a class="nav-link text-dark" href="#OnlineTools" data-toggle="tab">Online-Tools</a>
														</li>
													</ul>
													<div class="tab-content">
														<div id="somethingImportant" class="tab-pane active">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-4 col-xl-4">
																		<p class="m-0">
																			<?php if ($playersEditNotes) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#notizErstellen">Notiz erstellen</a>';
																			} ?>
																			<?php if ($playersLogsA3) {
																				echo '<a class="btn btn-default btn-lg btn-block" href="playerLogsA3.php?id=' . $pid . '" >Player - Logs</a>';
																			} ?>
																			<!-- <?php if ($_SESSION["rightsArr"]["Add_support_schulden"] == true) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#AddSupportSchulden">Supportschulden Verwalten</a>';
																			} ?> -->
																		</p>
																	</div>
																	<div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php /* if ($editBanRcon) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#editBanRcon">Kick Rcon</a>';
																			} ?>
																			<?php if ($editBanServer) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#editBanServer">Server Ban</a>';
																			} */ ?>
																			<?php
																			if ($createWhitelist) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#addWhitelistName">Whitelist bearbeiten</a>';
																			}
																			?>
																			<?php if ($_SESSION["rightsArr"]["Item_give"] == true) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#AddItemToPlayer">Item Geben</a>';
																			} ?>
																		</p>
																	</div>
																	<!-- <div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php /* if ($kickPlayerRcon) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#rconKick">Kick TheRealRcon</a>';
																			} ?>
																			<?php if ($banRealRcon) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#banRcon">TheRealRcon Ban</a>';
																			} ?>
																			<?php if ($unbanRealRcon) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#unbanRcon">TheRealRcon Unban</a>';
																			} */ ?>
																		</p>
																	</div> -->
																	<div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php if ($playerKick) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#playerKick">Kick Player</a>';
																			} ?>
																			<?php if ($playerBan) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#playerBan">Ban Player</a>';
																			} ?>
																			<?php if ($playerUnban) {
																				echo '<a class="modal-with-form btn btn-danger btn-lg btn-block" href="#playerUnban">Unban Player</a>';
																			} ?>
																		</p>
																	</div>
																</div>
															</div>
														</div>
														<div id="OfflineTools" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-4 col-xl-4">
																		<p class="m-0">
																			<?php if ($playersEditMoney) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#geldGeben">Geld edit</a>';
																			} ?>

																			<?php if (isset($changeLevelButton)) {
																				if ($changeLevelButton) {
																					echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#giveLevel">Level edit</a>';
																				}
																			} ?>
																		</p>
																	</div>
																	<!-- <div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php
                                                                            /*
                                                                            if ($playersEditName) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#changeName">Name ändern</a>';
																			}
                                                                            */
                                                                            ?>
																		</p>
																	</div> -->
																	<div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php if ($playersEditGambling) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#changeBlacklist">Gambling Blacklist</a>';
																			} ?>
																		</p>
																	</div>
																</div>
															</div>
														</div>
														<div id="OnlineTools" class="tab-pane">
															<div class="col-lg-6 col-xl-12">
																<div class="row">
																	<div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php if ($playersEditAdminMessage) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#playersEditAdminMessage">Admin Message</a>';
																			} ?>
																			<?php if ($playersEditTsWarn1) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#tsWarn1">TS-Warnung 1</a>';
																			} ?>
																			<?php if ($playersEditTsWarn2) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#tsWarn2">TS-Warnung 2</a>';
																			} ?>
																			<?php if ($playersEditTsWarn3) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#tsWarn3">TS-Warnung 3</a>';
																			} ?>
																		</p>
																	</div>
																	<div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php if ($playersEditMoney) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#changeMoneyOnline">Money erstatten</a>';
																			} ?>
																			<?php if ($unbugVehiclePlayer) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#changeKofferraumOnline">Kofferraum entbuggen</a>';
																			} ?>
																			<?php if ($playersEditRevive) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#revivePlayer">Revive Player</a>';
																			} ?>
																			<?php if ($playersEditPosition) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#editPos">Position setzen</a>';
																			} ?>
																			<?php if ($playersEdittfarDisable) {
																				echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#tfarDisable">TFAR überprüfung deaktivieren</a>';
																			} ?>
																		</p>
																	</div>
																	<div class="col-lg-6 col-xl-4">
																		<p class="m-0">
																			<?php if ($playersLogsA3) {
																				echo '<a class="btn btn-default btn-lg btn-block" href="logsA3Life_SupportPlayer.php?id=' . $pid . '" >Logs Life-Support</a>';
																			} ?>
																		</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
										</section>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<section class="card card-featured-left card-featured-primary mb-4">
							<header class="card-header">
								<h3 class="card-title text-dark">Support-Aktionen</h3>
							</header>
							<div class="card-actions">
								<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
							</div>
							<div class="tabs tabs-primary">
								<ul class="nav nav-tabs">
									<li class="nav-item active">
										<a class="nav-link text-dark" href="#Notes" data-toggle="tab">Notizen</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Kick-History" data-toggle="tab">Kick-History</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Ban-History" data-toggle="tab">Ban-History</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Perso" data-toggle="tab">Personalausweise</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Civ-Lizenzen" data-toggle="tab">Civ-Lizenzen</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Cop-Lizenzen" data-toggle="tab">Cop-Lizenzen</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Med-Lizenzen" data-toggle="tab">Med-Lizenzen</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Fahrzeuge" data-toggle="tab">Fahrzeuge</a>
									</li>
									<!-- <li class="nav-item">
										<a class="nav-link text-dark" href="#Farmed" data-toggle="tab">Farmed</a>
									</li> -->
									<!-- <li class="nav-item">
										<a class="nav-link text-dark" href="#Locker" data-toggle="tab">Schließfach</a>
									</li> -->
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Gearsafe_Civ" data-toggle="tab">Gearsafe - Civ</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Gearsafe_Cop" data-toggle="tab">Gearsafe - Cop</a>
									</li>
									<li class="nav-item">
										<a class="nav-link text-dark" href="#Gearsafe_Med" data-toggle="tab">Gearsafe - EMS</a>
									</li>
								</ul>
								<div class="tab-content">
									<div id="Notes" class="tab-pane active">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-default5">
												<thead>
													<tr>
														<th>Datum</th>
														<th>Notiz</th>
														<th>Spielzeit</th>
														<th>Supporter</th>
														<?php if ($playersEditNotesDelete) {
															echo '<th>Löschen</th>';
														} ?>
													</tr>
												</thead>
												<tbody>
													<?php
													$stmt = $dbl->prepare("SELECT * FROM acp_notes WHERE playerID=?");
													$stmt->bind_param("s", $pid);
													$stmt->execute();
													$result = $stmt->get_result();
													while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
														$date = date_create($zeile['insertDate']);
														$date = date_format($date, 'Y-m-d H:i:s');
														echo "<tr>";
														echo "<td>" . $date . "</td>";
														echo '<td><div class="note-container">' . $zeile['note'] . '</div></td>';
														echo "<td>" . $zeile['playerPlaytime'] . "</td>";
														echo "<td>" . $zeile['supName'] . "</td>";
														if ($playersEditNotesDelete) {
															echo '<td>
                                                                    <a class="modal-with-form mb-1 mt-1 mr-1 btn btn-xs btn-default" href="#deleteNote' . $zeile['id'] . '"><i class="fas fa-trash-alt"></i>  Löschen</a>

                                                                    <div id="deleteNote' . $zeile['id'] . '" class="modal-block modal-block-primary mfp-hide">
                                                                        <section class="card">
                                                                            <form method="post">
                                                                                <div class="card-body text-center">
                                                                                    <div class="modal-wrapper">
                                                                                        <div class="modal-icon center">
                                                                                            <i class="fas fa-question-circle"></i>
                                                                                        </div>
                                                                                        <div class="modal-text">
                                                                                            <h4>Bist Du Dir sicher?</h4>
                                                                                            <p>Mit bestätigen Löschst Du diese Notiz!?</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <footer class="card-footer">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 text-right">
                                                                                            <input type="hidden" id="deleteNoteSubmitIDHidden" name="noteDeleteSubmit" value=' . $zeile['id'] . '>
                                                                                            <button class="btn btn-primary" type="submit" id="deleteNoteSubmit' . $zeile['id'] . '" name="deleteNoteSubmit">Bestätigen</button>
                                                                                            <button class="btn btn-default modal-dismiss">Cancel</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </footer>
                                                                            </form>
                                                                        </section>
                                                                    </div>
                                                                    </td>';
														}
														echo "</tr>";
													}

													?>
												</tbody>
											</table>
										</div>
									</div>
									<div id="Kick-History" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-ajax4">
												<thead>
													<tr>
														<th>ID</th>
														<th>Datum</th>
														<th>Log</th>
														<th>Grund</th>
														<th>Supporter</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$stmt = $dbl->prepare("SELECT id, timestamp, text, supporter_pid, supporter_name, short_note FROM acp_logs WHERE category = 'kickPlayer' && user_pid = ?");
													$stmt->bind_param("s", $pid);
													$stmt->execute();
													$result = $stmt->get_result();
													while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
														$date = date_create($zeile['timestamp']);
														$date = date_format($date, 'Y-m-d H:i:s');
														echo "<tr>";
														echo "<td>" . $zeile['id'] . "</td>";
														echo "<td>" . $date . "</td>";
														echo "<td>" . $zeile['text'] . "</td>";
														echo "<td>" . $zeile['short_note'] . "</td>";
														echo '<td> <a class="btn btn-xs btn-default" href="playerShow.php?id=' . $zeile['supporter_pid'] . '" > ' . $zeile['supporter_name'] . '</a> </td>';
														echo "</tr>";
													}

													?>
												</tbody>
											</table>
										</div>
									</div>
									<div id="Ban-History" class="tab-pane">
									<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-ajax4">
												<thead>
													<tr>
														<th>ID</th>
														<th>Datum</th>
														<th>Log</th>
														<th>Grund</th>
														<th>Supporter</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$stmt = $dbl->prepare("SELECT id, timestamp, text, supporter_pid, supporter_name, short_note FROM acp_logs WHERE category = 'createBanPlayer' && user_pid = ?");
													$stmt->bind_param("s", $pid);
													$stmt->execute();
													$result = $stmt->get_result();
													while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
														$date = date_create($zeile['timestamp']);
														$date = date_format($date, 'Y-m-d H:i:s');
														echo "<tr>";
														echo "<td>" . $zeile['id'] . "</td>";
														echo "<td>" . $date . "</td>";
														echo "<td>" . $zeile['text'] . "</td>";
														echo "<td>" . $zeile['short_note'] . "</td>";
														echo '<td> <a class="btn btn-xs btn-default" href="playerShow.php?id=' . $zeile['supporter_pid'] . '" > ' . $zeile['supporter_name'] . '</a> </td>';
														echo "</tr>";
													}

													?>
												</tbody>
											</table>
										</div>
									</div>
									<div id="Civ-Lizenzen" class="tab-pane">
										<div class="col-lg-12">
											<form method="post">
												<h3 class="text-dark text-center">Civ-Lizenzen</h3>
												<?php
													foreach ($civlicenseArray as $key => $value) {
														// license_info_array
														$Displayname = $value[0];
														$Price = "N/A";
														foreach ($license_info_array as $key => $Infovalue) {
															if ($value[0] == $Infovalue["varname"]) {
																$Displayname = $Infovalue["displayname"];
																if ($Infovalue["price"] == -1) {
																	$PriceFormated = "N/A";
																} else {
																	$PriceFormated = number_format($Infovalue["price"], 0, ',', '.');
																}
																break;
															}
														};
														echo '<input type="hidden" name="license_' . $value[0] . '" value="' . $value[1] . '">';
														echo '<button type="button" class="mb-1 mt-1 mr-1 btn ' . ($value[1] == 1 ? 'btn-success' : 'btn-danger') . '" data-state="' . $value[1] . '" onclick="toggleButtonState(this)">' . $Displayname . " ($" . $PriceFormated . ")" . '</button>';
													}
												?>
												<script>
												function toggleButtonState(button) {
													var state = parseInt(button.getAttribute('data-state'));
													var hiddenInput = button.previousElementSibling;
													if (state == 0) {
														button.setAttribute('data-state', 1);
														button.classList.remove('btn-danger');
														button.classList.add('btn-success');
														hiddenInput.value = 1;
													} else {
														button.setAttribute('data-state', 0);
														button.classList.remove('btn-success');
														button.classList.add('btn-danger');
														hiddenInput.value = 0;
													}
												}
												</script>
												<?php if ($playersEditLicenseCiv) {
													echo '<button class="btn btn-primary btn-lg btn-block" type="submit" name="changeLicenseCivSubmit">Bestätigen</button>';
												} ?>

											</form>
										</div>
									</div>
									<div id="Cop-Lizenzen" class="tab-pane">
										<div class="col-lg-12">
											<form method="post">
												<h3 class="text-dark text-center">Cop-Lizenzen</h3>
												<?php
													foreach ($coplicenseArray as $key => $value) {
														// license_info_array
														$Displayname = $value[0];
														$Price = "N/A";
														foreach ($license_info_array as $key => $Infovalue) {
															if ($value[0] == $Infovalue["varname"]) {
																$Displayname = $Infovalue["displayname"];
																if ($Infovalue["price"] == -1) {
																	$PriceFormated = "N/A";
																} else {
																	$PriceFormated = number_format($Infovalue["price"], 0, ',', '.');
																}
																break;
															}
														};
														echo '<input type="hidden" name="license_' . $value[0] . '" value="' . $value[1] . '">';
														echo '<button type="button" class="mb-1 mt-1 mr-1 btn ' . ($value[1] == 1 ? 'btn-success' : 'btn-danger') . '" data-state="' . $value[1] . '" onclick="toggleButtonState(this)">' . $Displayname . " ($" . $PriceFormated . ")" . '</button>';
													}
												?>
												<script>
												function toggleButtonState(button) {
													var state = parseInt(button.getAttribute('data-state'));
													var hiddenInput = button.previousElementSibling;
													if (state == 0) {
														button.setAttribute('data-state', 1);
														button.classList.remove('btn-danger');
														button.classList.add('btn-success');
														hiddenInput.value = 1;
													} else {
														button.setAttribute('data-state', 0);
														button.classList.remove('btn-success');
														button.classList.add('btn-danger');
														hiddenInput.value = 0;
													}
												}
												</script>
												<?php if ($playersEditLicenseCop) {
													echo '<button class="btn btn-primary btn-lg btn-block" type="submit" name="changeLicenseCopSubmit">Bestätigen</button>';
												} ?>
											</form>
										</div>
									</div>
									<div id="Med-Lizenzen" class="tab-pane">
										<div class="col-lg-12">
											<form method="post">
												<h3 class="text-dark text-center">Med-Lizenzen</h3>
												<?php
													foreach ($medlicenseArray as $key => $value) {
														// license_info_array
														$Displayname = $value[0];
														$Price = "N/A";
														foreach ($license_info_array as $key => $Infovalue) {
															if ($value[0] == $Infovalue["varname"]) {
																$Displayname = $Infovalue["displayname"];
																if ($Infovalue["price"] == -1) {
																	$PriceFormated = "N/A";
																} else {
																	$PriceFormated = number_format($Infovalue["price"], 0, ',', '.');
																}
																break;
															}
														};
														echo '<input type="hidden" name="license_' . $value[0] . '" value="' . $value[1] . '">';
														echo '<button type="button" class="mb-1 mt-1 mr-1 btn ' . ($value[1] == 1 ? 'btn-success' : 'btn-danger') . '" data-state="' . $value[1] . '" onclick="toggleButtonState(this)">' . $Displayname . " ($" . $PriceFormated . ")" . '</button>';
													}
												?>
												<script>
												function toggleButtonState(button) {
													var state = parseInt(button.getAttribute('data-state'));
													var hiddenInput = button.previousElementSibling;
													if (state == 0) {
														button.setAttribute('data-state', 1);
														button.classList.remove('btn-danger');
														button.classList.add('btn-success');
														hiddenInput.value = 1;
													} else {
														button.setAttribute('data-state', 0);
														button.classList.remove('btn-success');
														button.classList.add('btn-danger');
														hiddenInput.value = 0;
													}
												}
												</script>
												<?php if ($playersEditLicenseMed) {
													echo '<button class="btn btn-primary btn-lg btn-block" type="submit" name="changeLicenseMedSubmit">Bestätigen</button>';
												} ?>
											</form>
										</div>
									</div>
									<div id="Fahrzeuge" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-default4">
												<thead>
													<tr>
														<th>ID</th>
														<th>Fahrzeug</th>
														<th>Typ</th>
														<th>Fahrzeugstatus</th>
														<th>Ausgeparkt</th>
														<th>Insurance</th>
														<th>Kaufpreis</th>
														<th>Zum Fahrzeug</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$stmt = $dbm->prepare("SELECT
													vehicles.id,
													vehicles.classname,
													vehicles.type,
													vehicles.alive,
													vehicles.active,
													vehicles.insurance,
													acp_vehicles.price,
													acp_vehicles.displayname
													FROM vehicles
													JOIN acp_vehicles ON vehicles.classname = acp_vehicles.classname
													WHERE vehicles.pid = ?");

													$stmt->bind_param("s", $pid);
													$stmt->execute();
													$result = $stmt->get_result();
													while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
														echo "<tr>";
														echo "<td>" . $row['id'] . "</td>";
														echo "<td>" . $row['classname'] . "</td>"; // translate($zeile['classname'])
														echo "<td>" . $row['type'] . "</td>";
														if ($row['alive'] === 1) {
															echo '<td><span class="text-success">OK</span></td>';
														} else {
															echo '<td><span class="text-danger">Zerstört</span></td>';
														}
														if ($row['active'] === 1) {
															echo '<td><span class="text-warning">Ausgeparkt</span></td>';
														} else {
															echo '<td><span class="text-success">In Garage</span></td>';
														}


														echo "<td>" . $row['insurance'] . "</td>";
														echo "<td>" . '$' . $row['price'] . "</td>";
														echo '<td>  <a class="btn btn-xs btn-default" href="vehicleShow.php?id=' . $row['id'] . '" ><i class="fas fa-pencil-alt"></i>    Zum Fahrzeug</a></td>';
														echo "</tr>";
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
									<div id="Perso" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-default4">
												<thead>
													<tr>
														<th>Fraktion</th>
														<th>Name</th>
														<th>Gang-ID</th>
														<th>Gang-Name</th>
														<th>Mobilfunk-nummer</th>
														<th>Anruf-Volumen</th>
														<th>SMS-Volumen</th>
														<th>Gebutsdatum</th>
														<th>Körper-größe</th>
														<th>Körper-gewicht</th>
														<th>Wohnort</th>
														<th>Straße</th>
														<th>Hausnummer</th>
														<th>Augenfarbe</th>
														<th>Geschlecht</th>
														<th>Staats-angehörigkeit</th>
														<th>Persobild</th>
													</tr>
												</thead>
												<tbody>
													<?php
													foreach ($PersoDatenArray as $row) {
														echo "<td>" .$row['Spielerfraktion'] . "</td>";
														echo "<td>" .$row['Spielername'] . "</td>";
														echo "<td>" .$row['gang_id'] . "</td>";
														echo "<td>" .$row['GangName'] . "</td>";
														echo "<td>" .$row['phone_number'] . "</td>";
														echo "<td>" .$row['call_value'] . "</td>";
														echo "<td>" .$row['sms_value'] . "</td>";
														echo "<td>" .$row['DateOfBirth'] . "</td>";
														echo "<td>" .$row['Size'] . "</td>";
														echo "<td>" .$row['Weight'] . "</td>";
														echo "<td>" .$row['Residence'] . "</td>";
														echo "<td>" .$row['Streetname'] . "</td>";
														echo "<td>" .$row['HouseNumber'] . "</td>";
														echo "<td>" .$row['Eyecolor'] . "</td>";
														echo "<td>" .$row['Gender'] . "</td>";
														echo "<td>" .$row['Nationality'] . "</td>";
														echo "<td>" .$row['Character'] . "</td>";
														echo "</tr>";
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
									<!-- <div id="Farmed" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<div class="row">
												<div class="col-lg-6 col-xl-6">

												</div>
												<div class="col-lg-6 col-xl-6">
													<table class="table table-bordered table-striped mb-0" id="datatable-default">
														<thead>
															<tr>
																<th>Item</th>
																<th>Anzahl</th>
															</tr>
														</thead>
														<tbody>
															<?php
                                                            /*
															$stmt = $dbm->prepare("SELECT * FROM farmed WHERE pID = ?");
															$stmt->bind_param("s", $pid);
															$stmt->execute();
															$result = $stmt->get_result();
															$row = mysqli_fetch_assoc($result);
															if ($row != "") {
																foreach ($row as $key => $value) {
																	if (($key == "uID") || ($key == "pID")) {
																	} else {
																		echo "<tr>";
																		echo "<td>" . $key . "</td>";
																		echo "<td>" . $value . "</td>";
																		echo "</tr>";
																	}
																}
															}
															*/
                                                            ?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div> -->

									<!-- <div id="Locker" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<div class="row">
												<?php /* if ($_SESSION["rightsArr"]["Item_give"] == true) {

													if (isPlayerOnline($dbm, $pid)) {
														echo '<a style="margin-bottom: 20px; color:black;" class=" modal-with-form btn btn-primary btn-lg btn-block" href="#" readonly disabled>+ Add Item to Locker / Nix da Player ist online</a>';
													} else {
														echo '<a style="margin-bottom: 20px; color:black;" class=" modal-with-form btn btn-primary btn-lg btn-block" href="#AddToLocker">+ Add Item to Locker</a>';
													}
												} */?> <br>
												<div class="col-lg-4 col-xl-4">
													<table class="table table-bordered table-striped mb-0" id="datatable-default6">
														<thead>
															<tr>
																<th>Item</th>
																<th>Anzahl</th>
															</tr>
														</thead>
														<tbody>
															<?php/*
															$stmt = $dbm->prepare("SELECT civ_locker FROM locker WHERE playerid = ?");
															$stmt->bind_param("s", $pid);
															$stmt->execute();
															$result = $stmt->get_result();
															$row = mysqli_fetch_assoc($result);

															if ($row["civ_locker"] ===  '"[]"') {
															} else {
																$row = str_replace(']"', "]", str_replace('"[', "[", (translate2($row["civ_locker"]))));
																$row = json_decode($row);


																foreach ($row as $key => $value) {
																	$translateItem = translate($value[1]);
																	echo "<tr>";
																	echo "<td>" . $translateItem . "</td>";
																	echo "<td>" . ($value[2]) . "</td>";
																	echo "</tr>";
																}
															}
															*/?>
														</tbody>
													</table>
												</div>
												<div class="col-lg-4 col-xl-4">
													<table class="table table-responsive-md table-striped mb-0" id="datatable-default7">
														<thead>
															<tr>
																<th>Item</th>
																<th>Anzahl</th>
															</tr>
														</thead>
														<tbody>
															<?php/*
															$stmt = $dbm->prepare("SELECT cop_locker FROM locker WHERE playerid = ?");
															$stmt->bind_param("s", $pid);
															$stmt->execute();
															$result = $stmt->get_result();
															$row = mysqli_fetch_assoc($result);

															if ($row["cop_locker"] ===  '"[]"') {
															} else {
																$row = str_replace(']"', "]", str_replace('"[', "[", (translate2($row["cop_locker"]))));
																$row = json_decode($row);

																foreach ($row as $key => $value) {
																	$translateItem = translate($value[1]);
																	echo "<tr>";
																	echo "<td>" . $translateItem . "</td>";
																	echo "<td>" . ($value[2]) . "</td>";
																	echo "</tr>";
																}
															}
															*/?>
														</tbody>
													</table>
												</div>
												<div class="col-lg-4 col-xl-4">
													<table class="table table-responsive-md table-striped mb-0" id="datatable-default8">
														<thead>
															<tr>
																<th>Item</th>
																<th>Anzahl</th>
															</tr>
														</thead>
														<tbody>
															<?php/*
															$stmt = $dbm->prepare("SELECT med_locker FROM locker WHERE playerid = ?");
															$stmt->bind_param("s", $pid);
															$stmt->execute();
															$result = $stmt->get_result();
															$row = mysqli_fetch_assoc($result);

															if ($row["med_locker"] ===  '"[]"') {
															} else {
																$row = str_replace(']"', "]", str_replace('"[', "[", (translate2($row["med_locker"]))));
																$row = json_decode($row);

																foreach ($row as $key => $value) {
																	$translateItem = translate($value[1]);
																	echo "<tr>";
																	echo "<td>" . $translateItem . "</td>";
																	echo "<td>" . ($value[2]) . "</td>";
																	echo "</tr>";
																}
															}
															*/?>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div> -->
									<div id="Gearsafe_Civ" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<a class="modal-with-form btn btn-default btn-lg" href="#civGearDerzeit">Derzeitiges Civ-Gear</a>
											<form method="post">
												<table class="table table-bordered table-striped" id="datatable-ajax2">
													<thead>
														<tr>
															<th width="15%">Insert Time</th>
															<th width="55%">Gear-Civ</th>
															<th width="15%">Gear setzen</th>
														</tr>
													</thead>
													<tbody>
														<?php
														$Civ_ArrGearSafe = array();
														$stmt = $dbm->prepare("SELECT * FROM log_acp_gear WHERE UID=? AND Playername='CIV'");
														$stmt->bind_param("s", $pid);
														$stmt->execute();
														$result = $stmt->get_result();
														while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
															if ($zeile["LOG"]  !== '"[]"') {
																echo "<tr>";
																echo "<td>" . $zeile["zeitstempel"] . "</td>";
																echo "<td>" . $zeile["LOG"] . "</td>";
																echo '<td>
                                                                    <a class="modal-with-form btn btn-xs btn-default" href="#civGear' . $zeile['UID'] . '">Gear setzen</a>
                                                                    <div id="civGear' . $zeile['UID'] . '" class="modal-block modal-block-primary mfp-hide">
                                                                        <section class="card">
                                                                            <form method="post">
                                                                                <div class="card-body text-center">
                                                                                    <div class="modal-wrapper">
                                                                                        <div class="modal-icon center">
                                                                                            <i class="fas fa-question-circle"></i>
                                                                                        </div>
                                                                                        <div class="modal-text">
                                                                                            <h4>Bist Du Dir sicher?</h4>
                                                                                            <p>Mit bestätigen ersetzt Du dem Spieler sein Gear.</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <footer class="card-footer">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 text-right">
                                                                                            <input type="hidden" id="gearCiv' . $zeile["LOG"] . '" name="gearCiv" value=' . $zeile['LOG'] . '>
                                                                                            <button class="btn btn-primary" type="submit" id="civGearID" name="civGearSubmit">Bestätigen</button>
                                                                                            <button class="btn btn-default modal-dismiss">Cancel</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </footer>
                                                                            </form>
                                                                        </section>
                                                                    </div>
                                                                    </td>';

																echo "</tr>";
															}
														}
														?>
													</tbody>
												</table>
											</form>
										</div>
									</div>
									<div id="Gearsafe_Cop" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<a class="modal-with-form btn btn-default btn-lg" href="#copGearDerzeit">Derzeitiges Cop-Gear</a>
											<form method="post">
												<table class="table table-bordered table-striped" id="datatable-ajax3">
													<thead>
														<tr>
															<th width="15%">Insert Time</th>
															<th width="55%">Gear-Cop</th>
															<th width="15%">Gear setzen</th>
														</tr>
													</thead>
													<tbody>
														<?php
														$cop_ArrGearsafe = array();
														$stmt = $dbm->prepare("SELECT * FROM log_acp_gear WHERE UID=? AND Playername='WEST'");
														$stmt->bind_param("s", $pid);
														$stmt->execute();
														$result = $stmt->get_result();
														while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
															if ($zeile["LOG"]  !== '"[]"') {
																echo "<tr>";
																echo "<td>" . $zeile["zeitstempel"] . "</td>";
																echo "<td>" . $zeile["LOG"] . "</td>";
																echo '<td>
                                                                    <a class="modal-with-form btn btn-xs btn-default" href="#copGear' . $zeile['UID'] . '">Gear setzen</a>

                                                                    <div id="copGear' . $zeile['UID'] . '" class="modal-block modal-block-primary mfp-hide">
                                                                        <section class="card">
                                                                            <form method="post">
                                                                                <div class="card-body text-center">
                                                                                    <div class="modal-wrapper">
                                                                                        <div class="modal-icon center">
                                                                                            <i class="fas fa-question-circle"></i>
                                                                                        </div>
                                                                                        <div class="modal-text">
                                                                                            <h4>Bist Du Dir sicher?</h4>
                                                                                            <p>Mit bestätigen ersetzt Du dem Spieler sein Gear.</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <footer class="card-footer">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 text-right">
                                                                                            <input type="hidden" id="gearCop' . $zeile["UID"] . '" name="gearCop" value=' . $zeile['LOG'] . '>
                                                                                            <button class="btn btn-primary" type="submit" id="copGearID" name="copGearSubmit">Bestätigen</button>
                                                                                            <button class="btn btn-default modal-dismiss">Cancel</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </footer>
                                                                            </form>
                                                                        </section>
                                                                    </div>
                                                                    </td>';
																echo "</tr>";
															}
														}
														?>
												</table>
											</form>
										</div>
									</div>
									<div id="Gearsafe_Med" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<a class="modal-with-form btn btn-default btn-lg" href="#medGearDerzeit">Derzeitiges EMS-Gear</a>
											<form method="post">
												<table class="table table-bordered table-striped" id="datatable-ajax2">
													<thead>
														<tr>
															<th width="15%">Insert Time</th>
															<th width="55%">Gear-Civ</th>
															<th width="15%">Gear setzen</th>
														</tr>
													</thead>
													<tbody>
														<?php
														$Civ_ArrGearSafe = array();
														$stmt = $dbm->prepare("SELECT * FROM log_acp_gear WHERE UID=? AND Playername='GUER'");
														$stmt->bind_param("s", $pid);
														$stmt->execute();
														$result = $stmt->get_result();
														while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
															if ($zeile["LOG"]  !== '"[]"') {
																echo "<tr>";
																echo "<td>" . $zeile["zeitstempel"] . "</td>";
																echo "<td>" . $zeile["LOG"] . "</td>";
																echo '<td>
                                                                    <a class="modal-with-form btn btn-xs btn-default" href="#medGear' . $zeile['UID'] . '">Gear setzen</a>
                                                                    <div id="medGear' . $zeile['UID'] . '" class="modal-block modal-block-primary mfp-hide">
                                                                        <section class="card">
                                                                            <form method="post">
                                                                                <div class="card-body text-center">
                                                                                    <div class="modal-wrapper">
                                                                                        <div class="modal-icon center">
                                                                                            <i class="fas fa-question-circle"></i>
                                                                                        </div>
                                                                                        <div class="modal-text">
                                                                                            <h4>Bist Du Dir sicher?</h4>
                                                                                            <p>Mit bestätigen ersetzt Du dem Spieler sein Gear.</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <footer class="card-footer">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 text-right">
                                                                                            <input type="hidden" id="gearMed' . $zeile["LOG"] . '" name="gearMed" value=' . $zeile['LOG'] . '>
                                                                                            <button class="btn btn-primary" type="submit" id="medGearID" name="medGearSubmit">Bestätigen</button>
                                                                                            <button class="btn btn-default modal-dismiss">Cancel</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </footer>
                                                                            </form>
                                                                        </section>
                                                                    </div>
                                                                    </td>';
																echo "</tr>";
															}
														}
														?>
													</tbody>
												</table>
											</form>
										</div>
									</div>
								</div>
							</div>
					</div>
			</section>
		</div>
		</div>
	</section>
	</section>

	<!-- Notiz erstellen -->
	<div id="notizErstellen" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Notiz erstellen</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="inputReadOnlynotiz">Spieler</label>
						<input type="text" value="<?php echo $namenString ?>" id="inputReadOnlynotiz" class="form-control" readonly="readonly">
						<label class="control-label text-lg-right pt-2" for="noteText">Notiz</label>
						<textarea class="form-control" name="noteTextName" id="noteTextID" placeholder="Was passiert? | Strafe/was ersetzt | Beweis" rows="3" data-plugin-maxlength maxlength="65000"></textarea>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="noteSubmit" name="noteSubmit" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>
	<!-- Geld Geben -->
	<div id="geldGeben" class="modal-block modal-block-sm mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Geld geben</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="giveCashID">Cash</label>
						<input class="form-control" name="giveCash" id="giveCashID" maxlength="10" type="text" value="<?php echo $cash ?>">
						<label class="control-label text-lg-right pt-2" for="giveBankID">Bank</label>
						<input class="form-control" name="giveBank" id="giveBankID" maxlength="10" type="text" value="<?php echo $bank ?>">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="giveMoney" name="giveMoneySubmit" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<!-- level gebne -->
	<div id="giveLevel" class="modal-block modal-block-sm mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Level bearbeiten</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveCopLevelID">Cop-Level<?php if (!$playersEditLevelCop) {echo "- Keine Berechtigung";} ?> </label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveCopLevelName" id="giveCopLevelID" value="<?php echo $copLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveSwatLevelID">SWAT-Level<?php if (!$playersEditLevelSwat) {echo "- Keine Berechtigung";} ?> </label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveSwatLevelName" id="giveSwatLevelID" value="<?php echo $swatLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveFbiLevelID">FBI-Level<?php if (!$playersEditLevelFbi) {echo "- Keine Berechtigung";} ?> </label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveFbiLevelName" id="giveFbiLevelID" value="<?php echo $fbiLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveDeaLevelID">DEA-Level<?php if (!$playersEditLevelDea) {echo "- Keine Berechtigung";} ?> </label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveDeaLevelName" id="giveDeaLevelID" value="<?php echo $deaLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveMedLevelID">Med-Level<?php if (!$playersEditLevelMed) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveMedLevelName" id="giveMedLevelID" value="<?php echo $medLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveFdLevelID">FD-Level<?php if (!$playersEditLevelFd) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveFdLevelName" id="giveFdLevelID" value="<?php echo $fdLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveDojLevelID">DoJ-Level<?php if (!$playersEditLevelDoj) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveDojLevelName" id="giveDojLevelID" value="<?php echo $dojLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveVolunteersLevelID">Vol-Level<?php if (!$playersEditLevelVolunteers) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveVolunteersLevelName" id="giveVolunteersLevelID" value="<?php echo $volunteersLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="givetowlevelID">tow-Level<?php if (!$playersEditLevelTow) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="givetowlevelName" id="givetowlevelID" value="<?php echo $towlevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveRmLevelID">RM-Level<?php if (!$playersEditLevelRm) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveRmLevelName" id="giveRmLevelID" value="<?php echo $rmLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 10 }'>
							<label class="control-label text-lg-right pt-2" for="giveTaxiLevelID">Taxi-Level<?php if (!$playersEditLevelTaxi) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveTaxiLevelName" id="giveTaxiLevelID" value="<?php echo $taxiLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 2 }'>
							<label class="control-label text-lg-right pt-2" for="giveJcmLevelID">JCM-Level<?php if (!$playersEditLevelJcm) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveJcmLevelName" id="giveJcmLevelID" value="<?php echo $jcmLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 2 }'>
							<label class="control-label text-lg-right pt-2" for="giveRedgwhLevelID">RedGWH-Level<?php if (!$playersEditLevelRedgwh) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveRedgwhLevelName" id="giveRedgwhLevelID" value="<?php echo $redgwhLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 2 }'>
							<label class="control-label text-lg-right pt-2" for="giveAutohausMMLevelID">AutohausMM-Level<?php if (!$playersEditLevelAutohausMM) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveAutohausMMLevelName" id="giveAutohausMMLevelID" value="<?php echo $AutohausMMLevel ?>" maxlength="2" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 5 }'>
							<label class="control-label text-lg-right pt-2" for="giveAdminLevelID">Admin-Level<?php if (!$playersEditLevelAdmin) {echo "- Keine Berechtigung";} ?></label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="giveAdminLevelName" id="giveAdminLevelID" value="<?php echo $adminLevel ?>" maxlength="1" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="giveLevelSubmitID" name="giveLevelSubmit" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<!-- Name Changen -->
	<!-- <div id="changeName" class="modal-block modal-block-sm mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Namen ändern</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="changeNameCivID">Name-Civ</label>
						<input class="form-control" name="changeNameCiv" id="changeNameCivID" type="text" value="<?php //echo $nameCiv ?>">
						<label class="control-label text-lg-right pt-2" for="changeNameCopID">Name-Cop</label>
						<input class="form-control" name="changeNameCop" id="changeNameCopID" type="text" value="<?php //echo $nameCop ?>">
						<label class="control-label text-lg-right pt-2" for="changeNameMedID">Name-Med</label>
						<input class="form-control" name="changeNameMed" id="changeNameMedID" type="text" value="<?php //echo $nameMed ?>">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="changeNameSubmitID" name="changeNameSubmit" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div> -->

	<!-- Gambling Blacklist -->
	<div id="changeBlacklist" class="modal-block modal-block-sm mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Spielsucht bekämpfen</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 1 }'>
							<label class="control-label text-lg-right pt-2" for="changeBlacklistID">Gambling Blacklist</label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control" name="changeBlacklistName" id="changeBlacklistID" value="<?php echo $gamblingBlacklist ?>" maxlength="1" readonly>
								<div class="input-group-append">
									<button type="button" class="btn btn-default spinner-up">
										<i class="fas fa-angle-up"></i>
									</button>
									<button type="button" class="btn btn-default spinner-down">
										<i class="fas fa-angle-down"></i>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="changeBlacklistSubmitID" name="changeBlacklistSubmitName" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>


	<!-- Whitelist Add -->
	<div id="addWhitelistName" class="modal-block modal-block-sm mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Namen Whitelisten</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="switch">
							<input type="checkbox" <?php echo ($isWhitelisted == 1) ? 'checked' : ''; ?> name="whitelistCheckbox" id="whitelistCheckbox">
							<span class="slider round"></span>
							<span class="label">Whitelist</span>
						</label>
					</div>
					<div id="whitelistNamesContainer">
						<!-- Generiere Zeilen basierend auf Werten im $WhitelistNameArray -->
						<?php
						foreach ($WhitelistNameArray as $index => $value) { ?>
							<div class="form-group">
								<div class="input-group">
									<input class="form-control" name="addWhitelistNameValue[]" id="addWhitelistNameID<?php echo $index; ?>" data-plugin-maxlength maxlength="24" type="text" value="<?php echo $value; ?>">
									<div class="input-group-append">
										<button type="button" class="btn btn-danger btn-sm deleteWhitelistNameButton">Löschen</button>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<div class="row mt-2">
						<div class="col-md-12 text-right">
							<button type="button" id="addWhitelistNameButton" class="btn btn-default">Zeile hinzufügen</button>
						</div>
					</div>
					<div class="form-group mt-3">
						<label for="lastEditByName">Letzter Bearbeiter</label>
						<input type="text" class="form-control" name="lastEditByName" id="lastEditByName" value="<?php echo $WhitelistLastEditByName; ?>" readonly>
					</div>
					<div class="form-group mt-3"> <!-- Füge hier mehr Leerfläche hinzu -->
						<label for="whitelistNote">Notiz</label>
						<textarea class="form-control" name="whitelistNote" id="whitelistNote" rows="1"><?php echo $WhitelistNote; ?></textarea>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="addWhitelistNameID" name="addWhitelistNameSubmit" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<script>
	document.getElementById('addWhitelistNameButton').addEventListener('click', function() {
		var container = document.getElementById('whitelistNamesContainer');
		var newFormGroup = document.createElement('div');
		newFormGroup.className = 'form-group';
		var newInputGroup = document.createElement('div');
		newInputGroup.className = 'input-group';
		var newInput = document.createElement('input');
		newInput.className = 'form-control';
		newInput.name = 'addWhitelistNameValue[]';
		newInput.id = 'addWhitelistNameID' + container.childElementCount;
		newInput.type = 'text';
		newInput.value = 'Max Mustermann';
		var newDeleteButton = document.createElement('button');
		newDeleteButton.className = 'btn btn-danger btn-sm deleteWhitelistNameButton';
		newDeleteButton.type = 'button';
		newDeleteButton.textContent = 'Löschen';
		newInputGroup.appendChild(newInput);
		newInputGroup.appendChild(newDeleteButton);
		newFormGroup.appendChild(newInputGroup);
		container.appendChild(newFormGroup);
	});

	document.addEventListener('click', function(event) {
		if (event.target.classList.contains('deleteWhitelistNameButton')) {
			var formGroup = event.target.parentNode.parentNode;
			formGroup.parentNode.removeChild(formGroup);
		}
	});

    // Function to auto resize textarea
    function autoResizeTextarea(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = (textarea.scrollHeight) + 'px';
    }

    // Auto resize textarea on input event
    document.getElementById('whitelistNote').addEventListener('input', function() {
        autoResizeTextarea(this);
    });

    // Initialize the textarea with the correct height
    autoResizeTextarea(document.getElementById('whitelistNote'));
</script>
	</script>






	<!-- Modal  civGearDerzeit -->
	<div id="civGearDerzeit" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Derzeitiges Civ-Gear</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<?php echo $civ_gearArr; ?>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<!-- Modal  copGearDerzeit -->
	<div id="copGearDerzeit" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Derzeitiges Cop-Gear</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<?php echo $cop_gearArr; ?>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

<!-- Modal  medGearDerzeit -->
<div id="medGearDerzeit" class="modal-block modal-block-primary mfp-hide">
	<section class="card">
		<form method="post">
			<header class="card-header">
				<h2 class="card-title">Derzeitiges EMS-Gear</h2>
			</header>
			<div class="card-body">
				<div class="form-group">
					<?php echo $med_gearArr; ?>
				</div>
			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button class="btn btn-default modal-dismiss">Cancel</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>

	<!-- Modal  rconKick -->
	<!-- <div id="rconKick" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Hackerfunction - Bist Du Dir sicher?</h4>
							<p>Du willst den Spieler per Rcon kicken. Mach Dies bitte nur, wenn es nicht anders Möglich ist.</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="rconKickTextID">Kick-Grund</label>
						<input class="form-control" name="rconKickText" id="rconKickTextID" type="text" value="Bitte im Support einfinden ts.fireside.gaming.de">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="rconKickSubmitID" name="rconKickSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div> -->

	<!-- Modal  rconUnbanTheRealOne -->
	<!-- <div id="unbanRcon" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du willst den Spieler per RealRcon entbannen. Mach Dies bitte nur, wenn es auch wirklich validiert ist, dass Er nix ist böse Mensch.</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="editUnbanBanRealRconShortNoteID">Short Note</label>
						<input class="form-control" name="editUnbanBanRealRconShortNote" id="editUnbanBanRealRconShortNoteID" type="text" value="Kurze Notiz">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="unbanRconSubmitID" name="unbanRconSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div> -->

	<!-- Modal  rconBanTheRealOne -->
	<!-- <div id="banRcon" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du willst den Spieler per RealRcon bannen. Mach Dies bitte nur, wenn es nicht anders Möglich ist..</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="banRconTextID">Ban-Grund</label>
						<input class="form-control" name="banRconText" id="banRconTextID" type="text" value="#Perm | ts.fireside-gaming.de">
						<label class="control-label text-lg-right pt-2" for="editBanRealRconShortNoteID">Short Note</label>
						<input class="form-control" name="editBanRealRconShortNote" id="editBanRealRconShortNoteID" type="text" value="Kurze Notiz">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="banRconSubmitID" name="banRconSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div> -->

	<!-- Modal  rconBan -->
	<!-- <div id="editBanRcon" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du willst den Spieler per Rcon bannen. Mach Dies bitte nur, wenn es sich tatsächlich um einen Perm handelt.</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="editBanRconTextID">Ban-Grund</label>
						<input class="form-control" name="editBanRconText" id="editBanRconTextID" type="text" value="#Perm | ts.fireside-gaming.de">
						<label class="control-label text-lg-right pt-2" for="editBanRconShortNoteID">Short Note</label>
						<input class="form-control" name="editBanRconShortNote" id="editBanRconShortNoteID" type="text" value="Kurze Notiz">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="editBanRconID" name="editBanRconSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div> -->


	<!-- Modal  serverBan -->
	<!-- <div id="editBanServer" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Server-Ban</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="serverBanGrundID">Ban-Grund</label>
						<input class="form-control" name="serverBanGrund" id="serverBanGrundID" type="text" value="RDM">
						<label class="control-label text-lg-right pt-2" for="serverBanTimeID">Ban-Zeit (Stunden)</label>
						<input class="form-control" name="serverBanTime" id="serverBanTimeID" type="text" value="24">
						<label class="control-label text-lg-right pt-2" for="serverBanPlakateID">Plakate Anzahl</label>
						<input class="form-control" name="serverBanPlakate" id="serverBanPlakateID" type="text" value="180">
						<label class="control-label text-lg-right pt-2" for="serverBanShortNoteID">Short Note</label>
						<input class="form-control" name="serverBanShortNote" id="serverBanShortNoteID" type="text" value="Kurze Notiz">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="editBanServerID" name="editBanServerSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div> -->

    <!-- Kick -->
	<div id="playerKick" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Kick Player</h4>
							<p>Du willst den Spieler kicken. Mach Dies bitte nur, wenn es nicht anders Möglich ist.</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="playerKickMessageID">Kick-Grund</label>
						<input class="form-control" name="playerKickMessage" id="playerKickMessageID" type="text" value="Bitte im Support einfinden ts.fireside.gaming.de">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="playerKickSubmitID" name="playerKickSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

    <div id="playerBan" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Server-Ban</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="playerBanMessageID">Ban-Grund</label>
						<input class="form-control" name="playerBanMessage" id="playerBanMessageID" type="text" value="Bitte im Support einfinden ts.fireside.gaming.de">
						<label class="control-label text-lg-right pt-2" for="playerBanTimeEndID">Ban-Ende</label>
						<div style="display: flex;">
							<input class="form-control" name="playerBanTimeEnd" id="playerBanTimeEndID" type="datetime-local">
							<button type="button" class="btn btn-danger" onclick="setDate()" style="margin-left: 10px;">Permanent</button>
						</div>
						<label class="control-label text-lg-right pt-2" for="playerBanNoteID">Notizen</label>
						<input class="form-control" name="playerBanNote" id="playerBanNoteID" type="text" value="Notiz">
					</div>
					<script>
					function setDate() {
						var date = new Date("2099-12-31T23:59:59");
						var dateString = date.toISOString().slice(0, -8);
						document.getElementById("playerBanTimeEndID").value = dateString;
					}
					</script>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="playerBanID" name="playerBanSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

    <div id="playerUnban" class="modal-block modal-block-primary mfp-hide">
        <section class="card">
            <form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Unban Player</h4>
							<p>Bist du dir sicher das due diesen Spieler entbannen möchtest?<br>Er ist bestimmt nicht ohne Grund gebannt!</p>
						</div>
					</div>
				</div>
                <footer class="card-footer">
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="btn btn-primary" type="submit" id="playerUnbanSubmitID" name="playerUnbanSubmit">Bestätigen</button>
                            <button class="btn btn-default modal-dismiss">Cancel</button>
                        </div>
                    </div>
                </footer>
            </form>
        </section>
    </div>

    <!-- TS Warnings -->
	<div id="tsWarn1" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du sprichst hiermit eine TS-Warung 1 aus.</p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="tsWarn1ID" name="tsWarn1Submit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<div id="tsWarn2" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du sprichst hiermit eine TS-Warung 1 aus.</p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="tsWarn2ID" name="tsWarn2Submit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<div id="tsWarn3" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du sprichst hiermit eine TS-Warung 1 aus.</p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="tsWarn3ID" name="tsWarn3Submit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>


	<div id="tfarDisable" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du Schaltest hiermit die TFAR überprüfung aus.</p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="tfarDisableID" name="tfarDisableSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<!-- Geld Geben Online -->
	<div id="changeMoneyOnline" class="modal-block modal-block-sm mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Geld geben Online</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="changeMoneyOnlineID">Bank</label>
						<input class="form-control" name="changeMoneyOnlineValue" id="changeMoneyOnlineID" maxlength="10" type="text" value="Betrag zum ersetzen">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="changeMoneyOnlineSubmitID" name="changeMoneyOnlineSubmit" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<!-- Kofferraum entbuggen -->
	<div id="changeKofferraumOnline" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Du enbuggst den Kofferraum des Spielers</p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="changeKofferraumOnlineID" name="changeKofferraumOnlineSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<!-- Adminmessage -->
	<div id="playersEditAdminMessage" class="modal-block modal-block-sm mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Adminmessage senden</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="changeMoneyOnlineID">Message</label>
						<input class="form-control" name="playersEditAdminMessageValue" id="playersEditAdminMessageValueID" maxlength="155" type="text" value="Bitte im Support einfinden">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button type="submit" id="playersEditAdminMessageSubmitID" name="playersEditAdminMessageSubmit" class="btn btn-primary">Speichern</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<!-- Revive Player-->
	<div id="revivePlayer" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Mit Bestätigen revivest Du den Spieler.</p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="revivePlayerSubmitId" name="revivePlayerSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<div id="editPos" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<div class="card-body text-center">
					<div class="modal-wrapper">
						<div class="modal-text">
							<h4>Bist Du Dir sicher?</h4>
							<p>Mit Bestätigen gibst Du dem Spieler das Spawnmenü erneut. </p>
						</div>
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="playersEditPositionSubmitID" name="playersEditPositionSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>

	<div id="AddToLocker" class="modal-block modal-block-lg mfp-hide">
		<section class="card">
			<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data">
				<header class="card-header">
					<h2 class="card-title">Add Item</h2>
				</header>
				<div class="card-body" id="hideTable">
					<table class="table table-bordered table-striped mb-0" id="datatable-3848348">
						<thead>
							<tr>
								<th>Item Name</th>
								<th>Item Variable</th>
								<th>Select</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($viteminfoarray as $key => $value) {
								if (isset($_SESSION["rightsArr"]["Item_give"])) {
									if ($_SESSION["rightsArr"]["Item_give"] == true) {
										echo "<tr>";
										echo "<td>" . $value["displayname"] . "</td>";
										echo "<td>" . $key . "</td>";
										echo '<td><input type="text" name="ItemGive_' . $key . '" value="0"></td>';
										echo "</tr>";
									}
								}
							}
							?>
						</tbody>
					</table>
				</div>

				<div style="display: none;" class="card-body" id="showTable">
					<div class="form-group row">
						<div class="col-sm-6 mb-6">
							<label>Item Name</label>
							<input name="ItemGiveName" id="ItemGiveName" type="text" readonly="readonly" class="form-control form-control-lg" />
							<input name="VarNameItem" id="VarNameItem" type="text" readonly="readonly" hidden class="form-control form-control-lg" />
						</div>
						<div class="col-sm-6 mb-6">

							<div data-plugin-spinner data-plugin-options=" <?php echo '{" min": 1, "max" : 99 }'; ?>">
								<label>Amount</label>
								<div class="input-group" style="width:345x;">
									<input type="text" class="spinner-input form-control form-control-lg" name="ItemAmount" id="ItemAmount" maxlength="1">
									<div class="input-group-append">
										<button type="button" class="btn btn-default spinner-up">
											<i class="fas fa-angle-up"></i>
										</button>
										<button type="button" class="btn btn-default spinner-down">
											<i class="fas fa-angle-down"></i>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-12">
						<label>Select Locker</label>
						<select data-plugin-selectTwo class="form-control populate" id="LockerSelect" name="LockerSelect">
							<optgroup label="Locker">
								<?php
								echo '<option value="civ_locker" selected>Civ Locker</option>';
								echo '<option value="cop_locker" >Cop Locker</option>';
								echo '<option value="med_locker" >Med Locker</option>';
								?>
							</optgroup>
						</select>
					</div>
				</div>

				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="SubmitGiveItem" name="SubmitGiveItem">Senden</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>


	<div id="AddItemToPlayer" class="modal-block modal-block-lg mfp-hide">
		<section class="card">
			<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data">
				<header class="card-header">
					<h2 class="card-title">Add Item</h2>
				</header>
				<div class="card-body" id="hideTable2">
					<table class="table table-bordered table-striped mb-0" id="datatable-3848343534534548">
						<thead>
							<tr>
								<th>Item Name</th>
								<th>Item Variable</th>
								<th>Select</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($viteminfoarray as $key => $value) {
								try {

									if (isset($_SESSION["rightsArr"]["Item_give"])) {
										if ($_SESSION["rightsArr"]["Item_give"] == true) {

											echo "<tr>";
											echo "<td>" . $value["displayname"] . "</td>";
											echo "<td>" . $key . "</td>";
											echo '<td>
												<div data-plugin-spinner data-plugin-options=\'{"min": 0, "max": 99}\'>
													<div class="input-group" style="width: 200px;">
														<input type="text" class="spinner-input form-control form-control-lg" name="ItemGive_' . $key . '" id="ItemGive_' . $key . '" maxlength="2" value="0">
														<div class="input-group-append">
															<button type="button" class="btn btn-default spinner-up">
																<i class="fas fa-angle-up"></i>
															</button>
															<button type="button" class="btn btn-default spinner-down">
																<i class="fas fa-angle-down"></i>
															</button>
														</div>
													</div>
												</div>
											</td>';
											// echo "<td>" . ' <a class="mb-1 mt-1 mr-1 btn btn-xs btn-default" onclick="SelectItemForPlayer(' . "'" . $value["displayname"] . "'" . "," . "'" . $key . "'" . ');" >Select</a>' . "</td>";
											echo "</tr>";
										}
									}
								} catch (ErrorException $e) {
									echo $e->getMessage();
								}
							}
							?>
						</tbody>
					</table>
				</div>

				<div style="display: none;" class="card-body" id="showTable2">
					<div class="form-group row">
						<div class="col-sm-6 mb-6">
							<label>Item Name</label>
							<input name="ItemGiveName2" id="ItemGiveName2" type="text" readonly="readonly" class="form-control form-control-lg" />
						</div>
						<div class="col-sm-6 mb-6">

							<div data-plugin-spinner data-plugin-options=" <?php echo '{" min": 1, "max" : 99 }'; ?>">
								<label>Amount</label>
								<div class="input-group" style="width:345x;">
									<input type="text" class="spinner-input form-control form-control-lg" name="ItemAmount2" id="ItemAmount2" maxlength="1">
									<div class="input-group-append">
										<button type="button" class="btn btn-default spinner-up">
											<i class="fas fa-angle-up"></i>
										</button>
										<button type="button" class="btn btn-default spinner-down">
											<i class="fas fa-angle-down"></i>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="SubmitGiveItemPLayer" name="SubmitGiveItemPLayer">Senden</button>
							<button class="btn btn-default modal-dismiss" onclick="SelectItemForPlayer();">Cancel</button>
						</div>
					</div>
				</footer>
			</form>
		</section>
	</div>


	<!-- <div id="AddSupportSchulden" class="modal-block modal-block-lg mfp-hide">
		<section class="card">
			<form class="form-horizontal form-bordered" method="post" enctype="multipart/form-data">
				<header class="card-header">
					<h2 class="card-title">Support Schulden</h2>
				</header>
				<div class="card-body" id="hidenewschulden">
					<div class="form-group">
						<a class="btn btn-default" id="AddSchulden" onclick="AddSchulden(1);">+ Neuen Eintrag Hinzufügen </a>
					</div>
					<table class="table table-bordered table-striped mb-0" id="datatable-supportschulden">
						<thead>
							<tr>
								<th>Amount</th>
								<th>Grund</th>
								<th>Share</th>
								<th>Till Date</th>
								<th>Erledigt</th>
								<?php if ($_SESSION["rightsArr"]["del_supportschulden"]) {
									echo 	'<th>Löschen</th>';
								} ?>
							</tr>
						</thead>
						<tbody>
							<?php
                            /*
							$stmt = $dbm->prepare("SELECT * FROM supportschulden WHERE pid=?");
							$stmt->bind_param("s", $pid);
							$stmt->execute();
							$result = $stmt->get_result();
							while ($row = mysqli_fetch_array($result)) {
								$date = date_create($row['till_date']);
								$date = date_format($date, 'd.m.Y H:i:s');

								echo "<tr>";
								echo "<td>" . $row["amount"] . "</td>";
								echo "<td>" . $row["reason"] . "</td>";
								echo "<td>" . $row["share"] . "</td>";
								echo "<td>" . $date . "</td>";
								echo "<td>" . translate3($row["handled"]) . "</td>";

								if ($_SESSION["rightsArr"]["del_supportschulden"]) {

									echo '
									<td>


											<a class="mb-1 mt-1 mr-1 btn btn-xs btn-default" data-toggle="modal" data-target="#deleteModal' . $row['id'] . '" href="#"><i class="fas fa-trash-alt"></i>   Löschen</a>

											<div class="modal" id="deleteModal' . $row['id'] . '" tabindex="-1" role="dialog">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<h5 class="modal-title">Bist Du Dir Sicher?</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
														<p>Mit bestätigen Löschst Du diese Schulden!?</p>
														</div>
														<div class="modal-footer">
															<form method="post">
															<input type="hidden" id="deleteSchuldenSubmitIDHidden" name="DelSchuldenSubmit" value=' . $row['id'] . '>
																<button type="submit" id="deleteSchuldeneSubmit' . $row['id'] . '" name="deleteSchuldeneSubmit" class="btn btn-primary">Confirm</button>
																<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
															</form>
														</div>
													</div>
												</div>
											</div>
									</td>';
								}
								echo "</tr>";
							}
                            */
                            ?>
						</tbody>
					</table>
				</div> -->

				<div style="display: none;" class="card-body" id="shownewschulden">
					<div class="form-group row">
						<div class="col-sm-6 mb-6">
							<label>Player ID</label>
							<input name="kkjfgh" id="kkjfgh" type="text" value="<?php echo $pid; ?>" readonly="readonly" class="form-control form-control-lg" />
						</div>
						<div class="col-sm-6 mb-6">

							<label>Schulden Höhe</label>
							<div class="input-group" style="width:345x;">
								<input type="text" class="spinner-input form-control form-control-lg" name="schuldenhoehe" id="schuldenhoehe">
								<span class="input-group-text">€</span>
							</div>
						</div>
						<br>
						<div class="col-lg-12">
							<label>Share in %</label>
							<select data-plugin-selectTwo class="form-control populate" id="shareProzent" name="shareProzent" required>
								<optgroup>
									<?php
									echo '<option value="0.1" selected>10%</option>';
									echo '<option value="0.25" >25%</option>';
									echo '<option value="0.5" >50%</option>';
									?>
								</optgroup>
							</select>
						</div>
						<br>
						<div class="col-lg-12">
							<label>Till Date:</label>
							<div class="input-group">
								<span class="input-group-text">
									<i class="fas fa-calendar-alt"></i>
								</span>
								<input type="text" data-plugin-datepicker="" class="form-control" name="SelectDate" id="SelectDate">
							</div>
						</div>

						<div class="col-lg-12">
							<label>Grund:</label>
							<div class="input-group">
								<span class="input-group-text">
									<i class="fas fa-pencil-alt"></i>
								</span>
								<input name="schuldReason" id="schuldReason" type="text" class="form-control form-control-lg" />
							</div>
						</div>

					</div>
				</div>


				<!-- <footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="createSchuldeneSubmit" name="createSchuldeneSubmit">Senden</button>
							<button class="btn btn-default modal-dismiss" onclick="AddSchulden();">Cancel</button>
						</div>
					</div>
				</footer> -->
			</form>
		</section>
	</div>







	<!-- Vendor -->
	<script src="vendor/jquery/jquery.js"></script>
	<script src="vendor/pnotify/pnotify.custom.js"></script>
	<script src="js/examples/examples.notifications.js"></script>
	<script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
	<script src="vendor/popper/umd/popper.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.js"></script>
	<script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="vendor/common/common.js"></script>
	<script src="vendor/nanoscroller/nanoscroller.js"></script>
	<script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
	<script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>

	<!-- Specific Page Vendor -->
	<script src="vendor/select2/js/select2.js"></script>
	<script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="vendor/datatables/media/js/dataTables.bootstrap4.min.js"></script>
	<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js"></script>
	<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.bootstrap4.min.js"></script>
	<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js"></script>
	<script src="vendor/datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js"></script>
	<script src="vendor/datatables/extras/TableTools/JSZip-2.5.0/jszip.min.js"></script>
	<script src="vendor/datatables/extras/TableTools/pdfmake-0.1.32/pdfmake.min.js"></script>
	<script src="vendor/datatables/extras/TableTools/pdfmake-0.1.32/vfs_fonts.js"></script>

	<!-- Specific Page Vendor -->
	<script src="vendor/jquery-ui/jquery-ui.js"></script>
	<script src="vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js"></script>
	<script src="vendor/select2/js/select2.js"></script>
	<script src="vendor/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
	<script src="vendor/jquery-maskedinput/jquery.maskedinput.js"></script>
	<script src="vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
	<script src="vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
	<script src="vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
	<script src="vendor/fuelux/js/spinner.js"></script>
	<script src="vendor/dropzone/dropzone.js"></script>
	<script src="vendor/bootstrap-markdown/js/markdown.js"></script>
	<script src="vendor/bootstrap-markdown/js/to-markdown.js"></script>
	<script src="vendor/bootstrap-markdown/js/bootstrap-markdown.js"></script>
	<script src="vendor/codemirror/lib/codemirror.js"></script>
	<script src="vendor/codemirror/addon/selection/active-line.js"></script>
	<script src="vendor/codemirror/addon/edit/matchbrackets.js"></script>
	<script src="vendor/codemirror/mode/javascript/javascript.js"></script>
	<script src="vendor/codemirror/mode/xml/xml.js"></script>
	<script src="vendor/codemirror/mode/htmlmixed/htmlmixed.js"></script>
	<script src="vendor/codemirror/mode/css/css.js"></script>
	<script src="vendor/summernote/summernote-bs4.js"></script>
	<script src="vendor/bootstrap-maxlength/bootstrap-maxlength.js"></script>
	<script src="vendor/ios7-switch/ios7-switch.js"></script>


	<!-- Theme Base, Components and Settings -->
	<script src="js/theme.js"></script>

	<!-- Theme Custom -->
	<script src="js/custom.js"></script>

	<!-- Theme Initialization Files -->
	<script src="js/theme.init.js"></script>

	<!-- Examples -->
	<script src="js/examples/examples.modals.js"></script>
	<script src="js/examples/examples.datatables.default.js"></script>
	<script src="js/examples/examples.datatables.defaultDevToDo.js"></script>
	<script src="js/examples/examples.datatables.defaultDevToDo2.js"></script>
	<script src="js/examples/examples.datatables.defaultDevToDo3.js"></script>
	<script src="js/examples/examples.datatables.defaultDevToDo4.js"></script>
	<!-- <script src="js/examples/examples.datatables.defaultDevToDo5.js"></script> -->
	<script src="js/examples/examples.datatables.row.with.details.js"></script>
	<script src="js/examples/examples.datatables.tabletools.js"></script>
	<script src="js/examples/examples.advanced.form.js"></script>

	<script>
		if (window.history.replaceState) {
			window.history.replaceState(null, null, window.location.href);
		}
	</script>

	<script>
		$(document).ready(function() {
			$('#datatable-ajax2').dataTable({
				"order": [
					[0, "desc"]
				],
				"pageLength": 25,
				"lengthMenu": [
					[25, 50, 75, 100, 250],
					[25, 50, 75, 100, 250]
				],
				'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'

			});
		});
	</script>

	<script>
		$(document).ready(function() {
			$('#datatable-ajax3').dataTable({
				"order": [
					[0, "desc"]
				],
				"pageLength": 10,
				"lengthMenu": [
					[10, 25, 50, 75, 100, 250],
					[10, 25, 50, 75, 100, 250]
				],
				'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'

			});
		});
	</script>

	<script>
		$(document).ready(function() {
			$('#datatable-ajax4').dataTable({
				"order": [
					[0, "desc"]
				],
				"pageLength": 10,
				"lengthMenu": [
					[10, 25, 50],
					[10, 25, 50]
				],
				'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'

			});
		});
	</script>


	<script>
		$(document).ready(function() {
			$('#datatable-3848348').dataTable({
				"order": [
					[1, "desc"]
				],
				"pageLength": 10,
				"lengthMenu": [
					[10, 25, 50],
					[10, 25, 50]
				],
				'dom': '<"row"<"col-lg-5"l><"col-lg-6"f>><"table-responsive"t>p'

			});
		});
	</script>


	<script>
		$(document).ready(function() {
			$('#datatable-supportschulden').dataTable({
				"order": [
					[1, "desc"]
				],
				"pageLength": 10,
				"lengthMenu": [
					[10, 25, 50],
					[10, 25, 50]
				],
				'dom': '<"row"<"col-lg-5"l><"col-lg-6"f>><"table-responsive"t>p'

			});
		});
	</script>

	<script>
		$(document).ready(function() {
			$('#datatable-3848343534534548').dataTable({
				"order": [
					[1, "desc"]
				],
				"pageLength": 10,
				"lengthMenu": [
					[10, 25, 50],
					[10, 25, 50]
				],
				'dom': '<"row"<"col-lg-5"l><"col-lg-6"f>><"table-responsive"t>p'

			});
		});
	</script>


	<script>
		function SelectItemForLocker(classname, displayname) {

			if (classname != null) {

				document.getElementById("hideTable").style.display = "none";
				document.getElementById("showTable").style.display = "block";
				document.getElementById("ItemGiveName").value = displayname;
				document.getElementById("VarNameItem").value = classname;
				document.getElementById("SubmitGiveItem").style.display = "";


			} else {


				document.getElementById("SubmitGiveItem").style.display = "none";
				document.getElementById("hideTable").style.display = "block";
				document.getElementById("showTable").style.display = "none";


			}
		}

		function SelectItemForPlayer(classname, displayname) {

			if (classname != null) {

				document.getElementById("hideTable2").style.display = "none";
				document.getElementById("showTable2").style.display = "block";
				document.getElementById("ItemGiveName2").value = classname;
				document.getElementById("SubmitGiveItemPLayer").style.display = "";


			} else {


				document.getElementById("SubmitGiveItemPLayer").style.display = "none";
				document.getElementById("hideTable2").style.display = "block";
				document.getElementById("showTable2").style.display = "none";


			}
		}
	</script>

	<script>
		function AddSchulden(item) {

			if (item != null) {

				document.getElementById("shownewschulden").style.display = "block";
				document.getElementById("hidenewschulden").style.display = "none";
				document.getElementById("createSchuldeneSubmit").style.display = "";

			} else {
				document.getElementById("shownewschulden").style.display = "none";
				document.getElementById("hidenewschulden").style.display = "block";
				document.getElementById("createSchuldeneSubmit").style.display = "none";
			}
		}
	</script>


</body>

</html>