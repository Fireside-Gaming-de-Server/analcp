<?php
//Logs And CheckOnline
include 'includes/checkOnline.php';
$LogSupporterPid = $row[7];
$LogsupporterName = $row[1];
$LogSupporterGroup = $userGroupname;
$LogUserPid = "-";
$LogUserName = "-";

//PermissionCheck
$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
$stmt->bind_param("s", $_COOKIE["username"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID = $row[5];

$permission_name = "gangShowVisit";
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
	exit();
}

$permission_name = "EnterpriseEdit";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$groupID_check = json_decode($groupID_check);
$EnterpriseEdit = false;
if (in_array($groupID, $groupID_check)) {
	$EnterpriseEdit = true;
}

//permission Ende

$entreprisesID = $_GET['id'];

$stmt3 = $dbm->prepare("SELECT * FROM entreprises WHERE id=?");
$stmt3->bind_param("i", $entreprisesID);
$stmt3->execute();
$result3 = $stmt3->get_result();
while ($row3 = $result3->fetch_assoc()) {
	$CompanyID = $row3["id"];
    $entreprisesOwnerPID = $row3["pdg_pid"];
	$entreprisespersoPID = $row3["pdg_persoid"];
	$entreprisesOwnerName = $row3["pdg_name"];
	$entreprisesName = $row3["name"];
	$entreprisesBank = $row3["bankacc"];
	$entreprisesLevel = $row3["type"];
	$entreprisesMembers = $row3["members"];
		// Einige DB-Schemata haben diese Spalten nicht (oder liefern sie nicht mit).
		// isset(...) verhindert in PHP 8 sicher "Undefined array key" Warnings.
		$entreprisesItemstock  = isset($row3["item_stock"]) ? $row3["item_stock"] : 0;
		$entreprisesItemstock2 = isset($row3["item_stock_2"]) ? $row3["item_stock_2"] : 0;
	$entreprisesversigelt = $row3["sealed"];
	$entreprisesinsert = $row3["insert_time"];

	$entreprisesDate = $row3["type"];
	$entreprisesTag = $row3["pdg_name"];

	$CompanyType = $row3["type"];
	// Typ/Level Mapping (in DB werden Keys gespeichert, im ACP sollen Namen angezeigt werden)
	$CompanyTypeOptions = [
		'size_1_entreprises' => 'Klein-Gewerbe',
		'size_2_entreprises' => 'Mittleres-Gewerbe',
		'size_3_entreprises' => 'Großes-Gewerbe',
		'size_4_entreprises' => 'Firma',
	];
	$CompanyTypeText = $CompanyTypeOptions[$CompanyType] ?? 'N/A';
	$CompanyActive          = $row3["active"];
	$CompanySealed          = $row3["sealed"];
	$CompanyInsertTime      = $row3["insert_time"];
	$CompanyPosition        = $row3["position"];

	$string                 = $row3["members"];
    $json                   = str_replace('`', '"', $string);
    $CompanyMembers      = json_decode(trim($json, '"'), true);
	
	$string                 = $row3["prices"];
    $json                   = str_replace('`', '"', $string);
    $CompanyPrice        = json_decode(trim($json, '"'), true);
	
	$string                 = $row3["items_stock"];
    $json                   = str_replace('`', '"', $string);
    $CompanyStock        = json_decode(trim($json, '"'), true); //Format [["Item1",Anzahl1],["Item2",Anzahl2]]
	$CompanyMaxStock        = $row3["max_stock"]; 
	
	$string                 = $row3["items_stock_2"];
    $json                   = str_replace('`', '"', $string);
    $CompanyStockPrivate = json_decode(trim($json, '"'), true); //Format [["Item1",Anzahl1],["Item2",Anzahl2]]
	$CompanyMaxPrivateStock = $row3["max_stock_2"];
}




	
	
    // weitere Spalten können hier ausgegeben werden


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


// $CompanyPrice = [["woodenlog",0],["woodenboard",0],["wheat",0],["apple",0],["oil",0],["oilbarrel",0],["redwine",0],["applecorn",0],["grape",0],["salt",0],["flour",10]];
// $CompanyStockPrivate = [["fish",1],["Praline",3],["chocolate",3],["Rose",2],["pretzel",4],["cherrywater",6],["flour",100],["teddy",1],["tire",5],["vtoolkit",1],["Plan_TAC_PBDFG2_RG_1",1],["waterBottle",7],["sandwich",7]];
// $CompanyStock = [["apple",666],["applecorn",1448],["grape",2],["flour",1911]];

// Merge the arrays and create a list of unique class names
$classList = [];
foreach ([$CompanyPrice, $CompanyStockPrivate, $CompanyStock] as $arr) {
    foreach ($arr as $subarr) {
        $className = $subarr[0];
        if (!in_array($className, $classList)) {
            $classList[] = $className;
        }
    }
}

$Array = [];

foreach ($classList as $className) {
    $publicCount = 0;
    $privateCount = 0;
    $price = "N/A";

    // Find the item in the ArrayPublic array
    foreach ($CompanyStock as $publicItem) {
        if ($publicItem[0] === $className) {
            $publicCount = $publicItem[1];
            break;
        }
    }

    // Find the item in the ArrayPrivate array
    foreach ($CompanyStockPrivate as $privateItem) {
        if ($privateItem[0] === $className) {
            $privateCount = $privateItem[1];
            break;
        }
    }

    // Find the item in the ArrayPrices array
    foreach ($CompanyPrice as $priceItem) {
        if ($priceItem[0] === $className) {
            $price = $priceItem[1];
            break;
        }
    }

	    // Item-Infos können je nach DB/Config fehlen -> Defaults setzen (verhindert PHP 8 "Undefined array key")
	    $itemInfo = (isset($viteminfoarray) && is_array($viteminfoarray) && isset($viteminfoarray[$className]) && is_array($viteminfoarray[$className])) ? $viteminfoarray[$className] : [];
	    $displayName = $itemInfo["displayname"] ?? $className;
	    $weight = (float)($itemInfo["weight"] ?? 0);

	    // Add the item to the new array using class name as key
	    $CompanyItemArray[$className] = [
	        "displayname" => $displayName,
        "publiccount" => $publicCount,
        "privatecount" => $privateCount,
        "price" => $price,
	        "weight" => $weight,
    ];
}

$PublicUsedSpace = 0;
$PrivateUsedSpace = 0;
foreach ($CompanyItemArray as $classname => $values) {
    $PublicUsedSpace = $PublicUsedSpace + ($values["weight"] * $values["publiccount"]);
    $PrivateUsedSpace = $PrivateUsedSpace + ($values["weight"] * $values["privatecount"]);
}	

// echo $PublicUsedSpace . "/" . $CompanyMaxStock;
// echo "<br>";
// echo $PrivateUsedSpace . "/" . $CompanyMaxPrivateStock ;
// exit;

//Members Array neu bauen um mit ["pid"] zugreifen zu können
$CompanyMembersArray = array();
foreach ($CompanyMembers as $value) {
    
	$member = array(
        'pid' => $value[0],
        'name' => $value[1],
        'group' => $value[2],
        'grouptext' => $value[2] === 0 ? 'Mitarbeiter' : ($value[2] === 1 ? 'COO' : ($value[2] === 2 ? 'CEO' : 'Unbekannt'))
    );
    $CompanyMembersArray[] = $member;
}
$CompanyMembersCount = count($CompanyMembersArray);

// Hinweis: Firmen-Level werden über Keys (z.B. size_1_entreprises) gespeichert.
// Anzeigen/Ändern erfolgt über $CompanyTypeOptions (Key => Anzeigename).




$row3 = $result3->fetch_row();
$playersArr = array();
$stmt4 = $dbm->prepare("SELECT count(*) FROM entreprises WHERE id=?");
$stmt4->bind_param("i", $CompanyID);
$stmt4->execute();
$result4 = $stmt4->get_result();
$row4 = $result4->fetch_row();
$gangMemberCount = $row4[0];

if (isset($_POST["gangChangeSubmitHidden"])) {
	if ($EnterpriseEdit) {
		// Eingaben säubern/validieren
		$changeName   = trim($_POST['changeName'] ?? '');
		$changeLeader = trim($_POST['changeLeader'] ?? '');
		$changeMoney  = trim($_POST['changeMoney'] ?? '');
		$changeType   = trim($_POST['gangLevelChange'] ?? '');
		$changePerso   = trim($_POST['changePerso'] ?? '');
		$CompanyID    = (int)$CompanyID;

		// Nur erlaubte Level-Keys übernehmen, sonst alten Wert behalten
		if (!isset($CompanyTypeOptions[$changeType])) {
			$changeType = $entreprisesLevel;
		}

		$beforeTypeText = $CompanyTypeOptions[$entreprisesLevel] ?? $entreprisesLevel;
		$afterTypeText  = $CompanyTypeOptions[$changeType] ?? $changeType;

		$update = $dbm->prepare("UPDATE entreprises SET name=?, pdg_pid=?, bankacc=?, type=? WHERE id=?");
		// pdg_pid/bankacc/type sind in vielen DBs VARCHAR/DECIMAL -> als String binden
		$update->bind_param("ssssi", $changeName, $changeLeader,$changePerso, $changeMoney, $changeType, $CompanyID);
		$update->execute();

		$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat die Firma mit der ID: "' . $CompanyID . '" bearbeitet. Vorher: "Name:' . $entreprisesName . ',Bank:' . $entreprisesBank . ', Level:' . $beforeTypeText . '" Nacher: "Name:' . $changeName . ', Bank:' . $changeMoney . ', Level:' . $afterTypeText . '"';
		$LogCategory = "ChangeGang";
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
		header("Location: " . $_SERVER['REQUEST_URI']);
		exit;
	}
}

require_once 'scripts/arc/arc.php';

use \Nizarii\ARC;
/*
if (isset($_POST["gangBanSubmit"])) {
	if ($EnterpriseEdit) {

		$gangBanModalReason = $_POST["gangBanMessage"];
		$gangBanModalReasonIntern = $_POST["gangBanNote"];

		$query = "SELECT SpielerUID, Spielername FROM perso WHERE gang_id=$CompanyID";

			$db_erg = mysqli_query($dbm, $query);
			if (!$db_erg) {
				die('Ungültige Abfrage: ' . mysqli_error());
			}
			while ($zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
			$pid = $zeile['SpielerUID'];
			$name = $zeile['Spielername'];
			$typeAcp = '"Ban"';
			$valueAcp = '["' . $_POST['gangBanMessage'] . '"]';
			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
			$stmt->execute();

			$stmt = $dbm->prepare("SELECT * FROM acp_bans WHERE uid=?");
			$stmt->bind_param("s", $pid);
			$playerid = $pid;
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows > 0) {
				$stmt = $dbm->prepare("UPDATE acp_bans SET name = ?, ban_end = ?, banned_by = ?, banned_by_name = ?, ban_reason = ? WHERE uid = ?");
				$stmt->bind_param("ssssss", $name, $_POST['gangBanTimeEnd'], $LogSupporterPid, $LogSupporterName, $_POST['gangBanMessage'], $pid);
				$stmt->execute();
			} elseif (!$result->num_rows > 0) {
				$stmt = $dbm->prepare("INSERT INTO acp_bans (uid, name, ban_end, banned_by, banned_by_name, ban_reason) VALUES (?, ?, ?, ?, ?, ?)");
				$stmt->bind_param("ssssss", $pid, $name, $_POST['gangBanTimeEnd'], $LogSupporterPid, $LogSupporterName, $_POST['gangBanMessage']);
				$stmt->execute();
			}

			$LogCategory = "createBanPlayer";
            $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat [' . $pid . '] "' . $name . '" bis "'. '' .'" mit der Nachricht: "'.$gangBanModalReason.'" gebant.';
            log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
		}

		$LogCategory = "ChangeGang";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat die Gang  ' . $gangName . '  mit der ID: "' . $CompanyID . '" komplett weggebannt." Interner vermerk: "' . $gangBanModalReasonIntern . '" ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
		header("Refresh:0");
	}
}
*/
//Log visitGang
$LogCategory = "SiteActivity";
$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat sich die die Seite von der Firma ' . $entreprisesName . ' mit der ID ' . $CompanyID . ' angeschaut';
log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);


?>


<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
<html>

<head>
	<!-- Basic -->
	<meta charset="UTF-8">

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

	<!-- Head Libs -->
	<script src="vendor/modernizr/modernizr.js"></script>

</head>

<body class="loading-overlay-showing" data-loading-overlay>
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
					<h2>Firmen-Übersicht</h2>
				</header>
				<!-- start: page -->
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<section class="card">
							<header class="card-header">
								<div class="row">
									<div class="col-lg-4 col-xl-12">
										<h2 class="card-title"><?php echo "$entreprisesName"; ?></h2>
									</div>
								</div>
							</header>
							<div class="col-lg-4 col-xl-12">
								<?php if ($EnterpriseEdit) {
									echo '<a class="modal-with-form mb-1 mt-1 mr-1 btn btn-default btn-block" href="#groupChangeModal"> Firma verfuschen  <i class="fas fa-hammer"></i></a>';
								}	?>
							</div>
							<div class="row">
								<div class="col-lg-3 col-xl-2">
									<section class="card card-featured-left card-featured-primary mb-4">
										<div class="card-body">
											<div class="widget-summary widget-summary-md">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-user-tag"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h5 class="title">Firmen-Besitzer</h5>
														<div class="info">
															<strong class="amount"><?php echo $entreprisesOwnerName; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
								<div class="col-lg-3 col-xl-2">
									<section class="card card-featured-left card-featured-primary mb-4">
										<div class="card-body">
											<div class="widget-summary widget-summary-md">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-key"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h5 class="title">Firmen-ID</h5>
														<div class="info">
															<strong class="amount"><?php echo $entreprisesID; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
								<div class="col-lg-3 col-xl-2"> 
									<section class="card card-featured-left card-featured-primary mb-4">
										<div class="card-body">
											<div class="widget-summary widget-summary-md">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-fist-raised"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h5 class="title">Firmen-LEVEL</h5>
														<div class="info">
															<strong class="amount"><?php echo $CompanyTypeText; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
								<div class="col-lg-3 col-xl-3">
									<section class="card card-featured-left card-featured-primary mb-4">
										<div class="card-body">
											<div class="widget-summary widget-summary-md">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-dollar-sign"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h5 class="title">Firmen-KONTO</h5>
														<div class="info">
															<strong class="amount"><?php echo number_format($entreprisesBank, 0, ',', '.'); ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
								<div class="col-lg-3 col-xl-3">
									<section class="card card-featured-left card-featured-primary mb-4">
										<div class="card-body">
											<div class="widget-summary widget-summary-md">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-user-cog"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h5 class="title">Firmen-OWNER-PID</h5>
														<div class="info">
															<strong class="amount"><?php echo $entreprisesOwnerPID; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
							</div>

								<div class="col-lg-3 col-xl-2">
									<section class="card card-featured-left card-featured-primary mb-4">
										<div class="card-body">
											<div class="widget-summary widget-summary-md">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-users"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h5 class="title">Firmen-MITGLIEDER INSGESAMT</h5>
														<div class="info">
															<strong class="amount"><?php echo $CompanyMembersCount; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
								<div class="col-lg-3 col-xl-2">
									<section class="card card-featured-left card-featured-primary mb-4">
										<div class="card-body">
											<div class="widget-summary widget-summary-md">
												<div class="widget-summary-col widget-summary-col-icon">
													<div class="summary-icon bg-primary">
														<i class="fas fa-calendar-alt"></i>
													</div>
												</div>
												<div class="widget-summary-col">
													<div class="summary">
														<h5 class="title">Firmen-INSERT DATE</h5>
														<div class="info">
															<strong class="amount"><?php echo $entreprisesinsert; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
							</div>



							<div class="col-lg-12">
                    <section class="card card-featured-left card-featured-primary mb-4">
                        <header class="card-header">
                            <h3 class="card-title text-dark center">Firmeninformation</h3>
                        </header>
                        <div class="card-actions">
                            <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                        </div>
                        <div class="card-body">
                            <div class="tabs tabs-primary">
                                <ul class="nav nav-tabs">
                                    <!-- <li class="nav-item active">
                                        <a class="nav-link text-dark" href="#Ts3" data-toggle="tab">TeamSpeak</a>
                                    </li> -->
                                    <li class="nav-item active">
                                        <a class="nav-link text-dark" href="#Mitarbeiter" data-toggle="tab">Mitarbeiter</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="#Fahrzeuge" data-toggle="tab">Vehicles</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="#PreiseLagerbestand" data-toggle="tab">Preise & Lagerbestand</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-dark" href="#Logs" data-toggle="tab">Logs</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div id="Mitarbeiter" class="tab-pane active">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-default4">
												<thead>
													<tr>
														<th>Steam-ID</th>
														<th>Name</th>
														<th>Job</th>
													</tr>
												</thead>
												<tbody>
													<?php
													foreach ($CompanyMembersArray as $player_row) {
                                                        echo "<tr>";
														echo "<td>" . $player_row['pid'] . "</td>";
														echo "<td>" . $player_row['name'] . "</td>";
                                                        echo "<td>" . "[" . $player_row['group'] . "]" . " " . $player_row['grouptext'] . "</td>";
                                                        echo "</tr>";
                                                    }
                                                        
													?>
												</tbody>
											</table>
										</div>
									</div>  
                                   
                                    <div id="Fahrzeuge" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-default4">
												<thead>
													<tr>
														<th>Eigentümer - Steam ID</th>
														<th>Eigentümer</th>
														<th>Fahrzeug</th>
														<th>Typ</th>
														<th>Fahrzeugstatus</th>
														<th>Ausgeparkt</th>
														<th>Insurance</th>
													</tr>
												</thead>
												<tbody>
													<?php
													$stmt = $dbm->prepare("SELECT 
                                                    vehicles.pid, vehicles.alive, vehicles.active, vehicles.trunkupgrade, vehicles.fuel, vehicles.immatriculation, vehicles.insurance, vehicles.type,
                                                    acp_vehicles.displayname
                                                    FROM vehicles
                                                    JOIN acp_vehicles ON vehicles.classname = acp_vehicles.classname
                                                    WHERE vehicles.entreprise_id = ?");
                                                    $stmt->bind_param("s", $CompanyID);
													
													$stmt->execute();
													$result = $stmt->get_result();
													while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                                                        $player_name = 'N/A';
                                                        foreach ($CompanyMembersArray as $player_row) {
                                                            if ($player_row['pid'] === $row['pid']) {
                                                                $player_name = $player_row['name'];
                                                                break;
                                                            }
                                                        }
                                                        switch ($row['type']) {
                                                            case 'Car':
                                                              $type_string = 'Auto';
                                                              break;
                                                            case 'Air':
                                                              $type_string = 'Luftfahrzeug';
                                                              break;
                                                            case 'Ship':
                                                              $type_string = 'Boot';
                                                              break;
                                                            default:
                                                              $type_string = 'Unbekannt';
                                                          }


														echo "<tr>";
														echo "<td>" . $row['pid'] . "</td>";
														echo "<td>" . $player_name . "</td>";
														echo "<td>" . $row['displayname'] . "</td>"; // translate($zeile['classname'])
														echo "<td>" . $type_string . "</td>";
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
														echo "</tr>";
													}
													?>
												</tbody>
											</table>
										</div>
									</div>   
                                    <div id="PreiseLagerbestand" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-default4">
												<thead>
													<tr>
														<th>Item</th>
														<th>Öffentliches Lager <?php echo "[" . $PublicUsedSpace . "/" . $CompanyMaxStock . "]" ?></th>
														<th>Privates Lager  <?php echo "[" . $PrivateUsedSpace . "/" . $CompanyMaxPrivateStock . "]" ?></th>
														<th>Ankaufspreis</th>
													</tr>
												</thead>
												<tbody>
													<?php
                                                    	foreach ($CompanyItemArray as $classname => $values) {
                                                            echo "<tr>";
                                                            echo "<td>" . $values["displayname"] . "</td>";
                                                            echo "<td>" . $values["publiccount"] . "</td>";
                                                            echo "<td>" . $values["privatecount"] . "</td>";
                                                            echo "<td>" . $values["price"] . "</td>";
                                                            echo "</tr>";                                                            
                                                        }													
													?>
												</tbody>
											</table>
										</div>
									</div>          
									
									<div id="Logs" class="tab-pane">
										<div class="col-lg-6 col-xl-12">
											<table class="table table-bordered table-striped mb-0" id="datatable-default4">
												<thead>
													<tr>
														<th>Log</th>
														<th>Zeitstempel</th>
													</tr>
												</thead>
												<tbody>
													<?php

													$stmt4 = $dbm->prepare("SELECT * FROM entreprises_logs WHERE entreprise_id=? ORDER BY zeitstempel DESC");
													$stmt4->bind_param("i", $entreprisesID);
													$stmt4->execute();
													$result4 = $stmt4->get_result();
													while ($row4 = $result4->fetch_assoc()) {
														echo "<tr>";
														echo "<td>" . str_replace('"', ' ', $row4['text']) . "</td>";
														echo "<td>" . $row4['zeitstempel'] . "</td>";
                                                        echo "</tr>";
													}                                                        
													?>
												</tbody>
											</table>
										</div>
									</div>  
                                </div>
                            </div>
                        </div>
                    </section>
                </div>




							<div id="groupChangeModal" class="modal-block modal-block-primary mfp-hide">
								<section class="card">
									<form class="form-horizontal form-bordered" method="post">
										<header class="card-header">
											<h2 class="card-title"><?php echo $entreprisesName; ?> - Bearbeiten</h2>
										</header>
										<div class="card-body">
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Firmen-Namen</label>
													<input name="changeName" id="changeName" type="text" value="<?php echo $entreprisesName; ?>" class="form-control form-control-lg" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Firemen-Besitzer</label>
													<input name="changeTag" id="changeTag" type="text" value="<?php echo $entreprisesTag; ?>" class="form-control form-control-lg" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Owner-PID</label>
													<input name="changeLeader" id="changeLeader" type="text" value="<?php echo $entreprisesOwnerPID; ?>" class="form-control form-control-lg" data-plugin-maxlength maxlength="17" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Perso-ID</label>
													<input name="changePerso" id="changePerso" type="text" value="<?php echo $entreprisespersoPID; ?>" class="form-control form-control-lg" data-plugin-maxlength maxlength="17" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Firmen-Money</label>
													<input name="changeMoney" id="changeMoney" type="text" value="<?php echo $entreprisesBank; ?>" class="form-control form-control-lg" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Firemen-Level</label>
											<select name="gangLevelChange" id="gangLevelChange" class="form-control form-control-lg">
												<?php foreach (($CompanyTypeOptions ?? []) as $typeKey => $typeLabel) { ?>
													<option value="<?php echo htmlspecialchars($typeKey); ?>" <?php echo ($entreprisesLevel === $typeKey) ? 'selected' : ''; ?>>
														<?php echo htmlspecialchars($typeLabel); ?>
													</option>
												<?php } ?>
											</select>
												</div>
											</div>
										</div>
										
</body>
<footer class="card-footer">
	<div class="form-group">
		<div class="row">
			<div class="col-md-12 text-right">
				<input type="hidden" id="gangChangeSubmitHidden" name="gangChangeSubmitHidden" value="gangChangeSubmitHidden">
				<button type="submit" id="gangChangeSubmit" name="userChangeSubmit" class="btn btn-default">Senden</button>
				<button class="btn btn-default modal-dismiss">Cancel</button>
			</div>
		</div>
	</div>
</footer>
</form>
</section>
</div>

<!-- end: page -->
</section>
</div>

</section>



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



<!-- Theme Base, Components and Settings -->
<script src="js/theme.js"></script>

<!-- Theme Custom -->
<script src="js/custom.js"></script>

<!-- Theme Initialization Files -->
<script src="js/theme.init.js"></script>

<!-- Examples -->
<script src="js/examples/examples.modals.js"></script>
<script src="js/examples/examples.datatables.row.with.details.js"></script>
<script src="js/examples/examples.datatables.tabletools.js"></script>
<script src="js/examples/examples.advanced.form.js"></script>
<script src="js/examples/examples.widgets.js"></script>


</body>

</html>