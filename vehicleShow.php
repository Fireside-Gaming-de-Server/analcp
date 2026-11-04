<?php 
//logs und online check
include 'includes/checkOnline.php';
$LogSupporterPid = $row[7];
$LogSupporterName = $row[1];
$LogSupporterGroup = $userGroupname;

$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
$stmt->bind_param("s", $_COOKIE["username"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID = $row[5];
//permission check
$permission_name = "vehicleShowVisit";
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
// Get vehicle information
$stmt = $dbm->prepare("SELECT classname, displayname FROM acp_vehicles");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $vehicle_info_array[] = $row;
}


function resolve_displayname($name, $info_array) {
    $return = $name;
    foreach ($info_array as $info) {
        if (isset($info['varname'])) {
            $lookfor = $info['varname'];
        } elseif (isset($info['classname'])) {
            $lookfor = $info['classname'];
        } else {
            $lookfor = "false";
        }
        

        if ($lookfor == $name) {
            $return = $info['displayname'];
            break;
        }
    }
    return $return;
}

//permission edit check
$permissionArr = [ 'vehicleEditRevive', 'vehicleEditImpound', 'vehicleEditGarage', 'vehicleEditInsurance', 'vehicleEditOwner', 'VehicleEditOnlineResett' ];
foreach ( $permissionArr as $value ){											 
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

//
$vehicleID = $_GET['id'];

$stmt = $dbm->prepare("SELECT * FROM vehicles WHERE id=?");
$stmt->bind_param("s", $vehicleID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $vehicleOwnerPid=           $row["pid"];
    $vehicleSide =              $row["side"];
    $vehicleClassname =         $row["classname"];
    $vehicleType =              $row["type"];
    $vehicleAlive =             $row["alive"];
    $vehicleBlacklist =         $row["blacklist"];
    $vehicleTrunkupgrade =      $row["trunkupgrade"];
    $vehicleActive =            $row["active"];
    $vehiclePlate =             $row["plate"];
    $vehicleColor =             $row["color"];
    $vehicleInventory =         $row["inventory"];
    $vehicleGear =              $row["gear"];
    $vehicleFuel =              $row["fuel"];
    $vehicleDamage =            $row["damage"];
    $vehicleInsert =            $row["insert_time"];
    $vehicleControlTech =       $row["controlTech"];
    $vehicleImmatriculation =   trim($row["immatriculation"], "\"");
    $vehicleFourriere =         $row["fourriere"];
    $vehicleEntreprise_id =     $row["entreprise_id"];
    $vehicleMeters =            $row["meters"];
    $vehicleCustomColor =       $row["custom_color"];
    $vehicleCustom_rvmat =      $row["custom_rvmat"];
    $vehicleInsurance =         $row["insurance"];
    $vehicleDateInsurance =     $row["dateInsurance"];
    $vehicleContract_Number =   $row["contract_number"];
    $vehicleExpiration_Date =   $row["expiration_date"];
    $vehicleOld_Owner =         $row["old_owner"];
    $vehicleBooms =             $row["booms"];
}


$stmt = $dbm->prepare("SELECT * FROM entreprises WHERE id=?");
$stmt->bind_param("s", $vehicleEntreprise_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $vehicleEntreprise_name = $row["name"];
}

$stmt = $dbm->prepare("SELECT * FROM entreprises");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // ID und Name des Eintrags in das Array einfügen
    $entreprises[] = array(
        "id" => $row["id"], 
        "name" => $row["name"]
    );
}

$vehicleColor = explode(",", $vehicleColor, 2);
$vehicleColor = str_replace(array('"', '[', ']', '`'), '', $vehicleColor);
    
$stmt4 = $dbm->prepare("SELECT name, beguid FROM players WHERE pid=?");
$stmt4->bind_param("s", $vehicleOwnerPid);
$stmt4->execute();
$result4 = $stmt4->get_result();
$row4 = $result4->fetch_row();
$vehicleOwnerName = $row4[0];
$vehicleOwnerGUID = $row4[1];
if ($vehicleOwnerGUID === null) {
    $steamID = $pid;
    $temp = '';
    for ($i = 0; $i < 8; $i++) {
        $temp .= chr($steamID & 0xFF);
        $steamID >>= 8;
    }
    $vehicleOwnerGUID = md5('BE' . $temp);
};    
    
$LogUserPid = $vehicleOwnerPid;
$LogUserName = $vehicleOwnerName;
  
if (isset($_POST["VehReviveSubmit"])) {
    if ($vehicleEditRevive) {
        $update = $dbm->prepare("UPDATE vehicles SET alive=1 WHERE id=?");
        $update->bind_param("i", $vehicleID);
        $update->execute();

        $LogCategory = "changeVehicle";
        $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat das Fahrzeug ['.$vehicleID.']"'.$vehicleClassname.'" von ['.$vehicleOwnerPid.'] '.$vehicleOwnerName.' Wiederbelebt.';
        log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);
        header("Refresh:0");
    }
}
     
if (isset($_POST["VehPackSubmit"])) {
    if ($vehicleEditGarage) {
        // Der Gameserver verarbeitet Queue-Einträge mit der Ziel-PID 1234.
        // Die echte Besitzer-PID darf dabei nicht überschrieben werden.
        $queueTargetPid = '1234';
        $typeAcp = '"FahrzeugEinparken"';
        $valueAcp = (int)$vehicleID;

        $dbm->begin_transaction();

        try {
            // Fahrzeug in der Datenbank als eingeparkt markieren.
            // Ohne active=0 verschwindet es zwar aus der Welt, wird aber nicht
            // in der Garage des Besitzers angezeigt.
            $update = $dbm->prepare(
                "UPDATE vehicles SET active=0, fourriere=0 WHERE id=?"
            );
            $update->bind_param("i", $valueAcp);
            $update->execute();

            if ($update->affected_rows < 0) {
                throw new Exception("Fahrzeugstatus konnte nicht aktualisiert werden.");
            }

            // Auftrag an den Gameserver senden, damit das Fahrzeug aus der Welt entfernt wird.
            $stmt = $dbm->prepare(
                "INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)"
            );
            $stmt->bind_param(
                "ssis",
                $queueTargetPid,
                $typeAcp,
                $valueAcp,
                $LogSupporterName
            );
            $stmt->execute();

            $dbm->commit();

            $LogCategory = "changeVehicle";
            $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.
                ' hat das Fahrzeug ['.$vehicleID.']"'.$vehicleClassname.'" von ['.
                $vehicleOwnerPid.'] '.$vehicleOwnerName.' zurück in die Garage gestellt.';
            log_acp(
                $LogCategory,
                $LogText,
                $LogSupporterPid,
                $LogSupporterName,
                $LogSupporterGroup,
                $LogUserPid,
                $LogUserName
            );

            header("Location: vehicleShow.php?id=" . urlencode($vehicleID));
            exit();
        } catch (Throwable $e) {
            $dbm->rollback();
            error_log("Fahrzeug konnte nicht eingeparkt werden: " . $e->getMessage());
        }
    }
}
if (isset($_POST["VehImpoundSubmit"])) {
    if ($vehicleEditImpound) {
        $updateImpoundStatus = $_POST['setVehImpoundName'];
        $update = $dbm->prepare("UPDATE vehicles SET fourriere=? WHERE id=?");
        $update->bind_param("ss", $updateImpoundStatus, $vehicleID);
        $update->execute();
        
        $LogCategory = "changeVehicle";
        $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat das Fahrzeug ['.$vehicleID.']"'.$vehicleClassname.'" von ['.$vehicleOwnerPid.'] '.$vehicleOwnerName.' auf den Abstellhof gestellt';
        log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);

        header("Refresh:0");
    }
}

if (isset($_POST["VehChangeOwnerSubmit"])) {
    if ($vehicleEditOwner) {
        $LogCategory = "changeVehicle";
        $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat den Besitzer des Fahrzeuges ['.$vehicleID.']"'.$vehicleClassname.'" geändert. Vorher: ['.$vehicleOwnerPid.'] '.$vehicleOwnerName.' Nacher:'.$_POST["VehChangeOwnerName"].' ';
        log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);
        
        $update = $dbm->prepare("UPDATE vehicles SET pid=? WHERE id=?");
        $update->bind_param("ss", $_POST["VehChangeOwnerName"], $vehicleID); 
        $update->execute();
        header("Refresh:0");
    }
}

if (isset($_POST["CompanyChangeSubmit"])) {
    if ($vehicleEditInsurance) {
        $update = $dbm->prepare("UPDATE vehicles SET entreprise_id=? WHERE id=?");
        $update->bind_param("ss", $_POST['setCompanyChangeName'], $vehicleID);
        $update->execute();

        $LogCategory = "changeVehicle";
        $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat das Fahrzeug ['.$vehicleID.']"'.$vehicleClassname.'" von ['.$vehicleOwnerPid.'] '.$vehicleOwnerName.' der Firma' . $_POST['setCompanyChangeName'] . " zugewiesen.";
        log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);

        header("Refresh:0");
    }
}

if (isset($_POST["VehInsuranceSubmit"])) {
    if ($vehicleEditInsurance) {
        $updateInsuranceStatus = $_POST['setVehInsuranceName'];
        $update = $dbm->prepare("UPDATE vehicles SET Insurance=? WHERE id=?");
        $update->bind_param("ss", $updateInsuranceStatus, $vehicleID);
        $update->execute();

        $LogCategory = "changeVehicle";
        $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat das Fahrzeug ['.$vehicleID.']"'.$vehicleClassname.'" von ['.$vehicleOwnerPid.'] '.$vehicleOwnerName.' wieder versichert';
        log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);

        header("Refresh:0");
    }
}

if (isset($_POST["VehicleEditOnlineResettSubmit"])) {
    if ($VehicleEditOnlineResett) {
        $typeAcp = '"FahrzeugErsetzen"';
        $valueAcp = $vehicleID;
        $stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
        $stmt->bind_param("ssis",$vehicleOwnerPid,$typeAcp,$valueAcp,$LogSupporterName);
        $stmt->execute(); 

        $LogCategory = "changeVehicle";
        $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat das Fahrzeug ['.$vehicleID.']"'.$vehicleClassname.'" von ['.$vehicleOwnerPid.'] '.$vehicleOwnerName.' mittels dem Online Tool samt Kofferaum erstattet.';
        log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);

        header("Refresh:0");
    }
}

    
//Log visitVehicle
$LogCategory = "SiteActivity";
$LogUserPid = $vehicleOwnerPid;
$LogUserName =$vehicleOwnerName;
$LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat sich die Seite von dem Fahrzeug ['.$vehicleID.']"'.$vehicleClassname.'" von ['.$vehicleOwnerPid.'] '.$vehicleOwnerName.' angeschaut';
log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);
    
?>

<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
<html >
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
	<body class="loading-overlay-showing" data-loading-overlay >
		<div class="loading-overlay">
			<div class="bounce-loader">
				<div class="bounce1"></div>
				<div class="bounce2"></div>
				<div class="bounce3"></div>
			</div>
		</div>

		<section class="body">
			<!-- include Header-->
			<?php include 'includes/header.php'; ?>
			<!-- include Header-->
			<div class="inner-wrapper">
				<!-- include SideBar-->
				<?php include 'includes/sideBar.php';?>
				<!-- include SideBar-->
				<section role="main" class="content-body card-margin">
					<header class="page-header">
						<h2>Fahrzeug Übersicht</h2>
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
                                                    <h2 class="card-title"><?php echo resolve_displayname($vehicleClassname, $vehicle_info_array); ?></h2>
                                                </div>
                                                <div class="col-lg-4 col-xl-4">
                                                </div>
                                                <div class="col-lg-4 col-xl-4">
                                                    <?php if ($vehicleActive) {
                                                        echo '<h2 class="card-title text-right text-success" >Aktiv</h2>';
                                                    } else {
                                                        echo '<h2 class="card-title text-right text-danger" >Inaktiv</h2>';
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </header>
                            </section>
                        </div>
                    </div>
					<div class="row">
                        <div class="col-lg-6 col-xl-12">
                            <div class="row">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row">
                                        <div class="col-lg-12 col-xl-12">
                                            <section class="card card-featured-left card-featured-primary mb-4">
                                                <header class="card-header">
                                                    <h3 class="card-title text-dark">Fahrzeuginformationen</h3>
                                                </header>
                                                <div class="card-body">
                                                    <div class="tabs tabs-vertical tabs-right tabs-primary">
                                                        <ul class="nav nav-tabs">
                                                            <li class="nav-item active">
                                                                <a class="nav-link text-dark" href="#vehicleOwner" data-toggle="tab">Fahrzeug & Besitzer</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link text-dark" href="#vehicleInformation" data-toggle="tab">Informationen</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link text-dark" href="#vehicleInventory" data-toggle="tab">Inventory</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link text-dark" href="#vehicleColor" data-toggle="tab">Lackierung</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div id="vehicleOwner" class="tab-pane active">
                                                                <div class="col-lg-6 col-xl-12">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">Vehicle-ID</h5><?php echo $vehicleID; ?>
                                                                            <h5 class="text-dark">Vehicle-Typ</h5><?php echo $vehicleType; ?>
                                                                            <h5 class="text-dark">Side</h5><?php 
                                                                            switch ($vehicleSide) {
                                                                                case 'cop':
                                                                                    echo "cop - Police";
                                                                                    break;
                                                                                case 'med':
                                                                                    echo "med - Medical";
                                                                                    break;
                                                                                case 'civ':
                                                                                    echo "civ - Civilian";
                                                                                    break;
                                                                                default:
                                                                                    echo "kek - Seite Unbk";
                                                                                    break;
                                                                            } ?>
                                                                            <h5 class="text-dark">Name</h5><?php echo resolve_displayname($vehicleClassname, $vehicle_info_array); ?>
                                                                            <h5 class="text-dark">Classname</h5><?php echo $vehicleClassname; ?>
                                                                            <h5 class="text-dark">Kennzeichen</h5><?php echo $vehicleImmatriculation; ?>
                                                                            <h5 class="text-dark">Kaufdatum</h5><?php echo $vehicleInsert; ?>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">Besitzer</h5><?php echo $vehicleOwnerName; ?>
                                                                            <h5 class="text-dark">PlayerID</h5><?php echo $vehicleOwnerPid; ?>
                                                                            <h5 class="text-dark">GUID</h5><?php echo $vehicleOwnerGUID; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <div id="vehicleInformation" class="tab-pane">
                                                                <div class="col-lg-6 col-xl-12">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">Am Leben</h5><?php 
                                                                            switch ($vehicleAlive) {
                                                                                case 1:
                                                                                    echo '<td><span class="text-success">OK</span></td>';
                                                                                    break;
                                                                                case 0:
                                                                                    echo '<td><span class="text-danger">Zerstört</span></td>';
                                                                                    break;
                                                                                case 2:
                                                                                    echo '<td><span class="text-warning">Verkauft</span></td>';
                                                                                break;
                                                                                default:
                                                                                    echo '<td><span class="text-warning">UNBEKANNT</span></td>';
                                                                                break;
                                                                            }
                                                                            ?>
                                                                            <h5 class="text-dark">Aktiv</h5><?php 
                                                                                switch ($vehicleActive) {
                                                                                    case 1:
                                                                                        echo '<td><span class="text-warning">Ausgeparkt</span></td>';
                                                                                        break;
                                                                                    default:
                                                                                        echo '<td><span class="text-success">Eingeparkt</span></td>';
                                                                                        break;
                                                                                }
                                                                            ?>
                                                                            <h5 class="text-dark">Versichert</h5><?php 
                                                                                if ($vehicleInsurance == 0) {
                                                                                    echo '<td><span class="text-warning">Nicht Versichert</span></td>';
                                                                                } else {
                                                                                    echo '<td><span class="text-success">Versichert - Level '. $vehicleInsurance .'</span></td>';
                                                                                }
                                                                            ?>
                                                                            <h5 class="text-dark">Impound</h5><?php 
                                                                                switch ($vehicleFourriere) {
                                                                                    case 1:
                                                                                        echo '<td><span class="text-warning">Abgeschleppt</span></td>';
                                                                                        break;
                                                                                    default:
                                                                                        echo '<td><span class="text-success">Nein</span></td>';
                                                                                        break;
                                                                                }
                                                                            ?>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">Trunk Upgrade</h5><?php echo $vehicleTrunkupgrade; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <div id="vehicleColor" class="tab-pane">
                                                                <div class="col-lg-6 col-xl-12">
                                                                    <h5 class="text-dark">Original Skin</h5><?php echo $vehicleColor[0] . "<br>" . $vehicleColor[1]; ?>
                                                                    <h5 class="text-dark">Custom Color</h5><?php echo $vehicleCustomColor; ?>
                                                                    <h5 class="text-dark">Custom RVMAT</h5><?php echo $vehicleCustom_rvmat; ?>
                                                                </div>
                                                            </div> 
                                                            <div id="vehicleInventory" class="tab-pane">
                                                                <div class="col-lg-6 col-xl-12">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">Vehicle - Inventory</h5><?php echo $vehicleInventory;  ?>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">Vehicle - Gear</h5><?php echo $vehicleGear;  ?>    
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
                                        <div class="col-lg-4 col-xl-6">
                                            <section class="card card-featured-left card-featured-primary mb-4">
                                                <div class="card-body">
                                                    <div class="widget-summary widget-summary-md">
                                                        <div class="widget-summary-col widget-summary-col-icon">
                                                            <div class="summary-icon bg-basic">
                                                                <i class="fa-solid fa-car-burst"></i>
                                                            </div>
                                                        </div>
                                                        <div class="widget-summary-col">
                                                            <div class="summary">
                                                                <h5 class="title">Am Leben</h5>
                                                                <div class="info">
                                                                    <strong class="amount"><?php echo translate3($vehicleAlive); ?></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="col-lg-4 col-xl-6">
                                            <section class="card card-featured-left card-featured-primary mb-4">
                                                <div class="card-body">
                                                    <div class="widget-summary widget-summary-md">
                                                        <div class="widget-summary-col widget-summary-col-icon">
                                                            <div class="summary-icon bg-basic">
                                                                <i class="fa-solid fa-industry"></i>
                                                            </div>
                                                        </div>
                                                        <div class="widget-summary-col">
                                                            <div class="summary">
                                                                <h5 class="title">Firmenfahrzeug:</h5>
                                                                <div class="info">
                                                                    <strong class="amount"><?php 
                                                                        if ($vehicleEntreprise_id != 0) {
                                                                            echo $vehicleEntreprise_name;
                                                                        } else {
                                                                            echo "Nein";
                                                                        }

                                                                    ?></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="col-lg-4 col-xl-6">
                                            <section class="card card-featured-left card-featured-primary mb-4">
                                                <div class="card-body">
                                                    <div class="widget-summary widget-summary-md">
                                                        <div class="widget-summary-col widget-summary-col-icon">
                                                            <div class="summary-icon bg-basic">
                                                                <i class="fas fa-piggy-bank"></i>
                                                            </div>
                                                        </div>
                                                        <div class="widget-summary-col">
                                                            <div class="summary">
                                                                <h5 class="title">Impond</h5>
                                                                <div class="info">
                                                                    <strong class="amount"><?php echo translate3($vehicleFourriere); ?></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                        <div class="col-lg-4 col-xl-6">
                                            <section class="card card-featured-left card-featured-primary mb-4">
                                                <div class="card-body">
                                                    <div class="widget-summary widget-summary-md">
                                                        <div class="widget-summary-col widget-summary-col-icon">
                                                            <div class="summary-icon bg-basic">
                                                                <i class="fas fa-car"></i>
                                                            </div>
                                                        </div>
                                                        <div class="widget-summary-col">
                                                            <div class="summary">
                                                                <h5 class="title">Kennzeichen</h5>
                                                                <div class="info">
                                                                    <strong class="amount"><?php echo $vehicleImmatriculation; ?></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
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
                                                <div class="card-body">
                                                    <div class="tabs tabs-primary">
                                                        <ul class="nav nav-tabs">
                                                            <li class="nav-item active">
                                                                <a class="nav-link text-dark" href="#OfflineTools" data-toggle="tab">Offline-Tools</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link text-dark" href="#OnlineTools" data-toggle="tab">Online-Tools</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div id="OfflineTools" class="tab-pane active">
                                                                <div class="col-lg-6 col-xl-12">
                                                                    <div class="row">
                                                                        <div class="col-lg-4 col-xl-4">
                                                                            <p class="m-0">
                                                                                <?php if ($vehicleEditRevive) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehRevive">Wiederbeleben</a>'; } ?>
                                                                                <?php if ($vehicleEditImpound) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehImpound">Impound Status</a>'; } ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-4">
                                                                            <p class="m-0">
                                                                                <?php if ($vehicleEditGarage) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehPack">In Garage stellen</a>'; } ?>
                                                                                <?php if ($vehicleEditInsurance) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehInsurance">Versicherung ändern</a>'; } ?> 
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-4">
                                                                            <p class="m-0">
                                                                                <?php if ($vehicleEditOwner) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehChangeOwner">Besitzer ändern</a>'; } ?>
                                                                                <?php if ($vehicleEditOwner) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#CompanyChange">Firma ändern</a>'; } ?> 
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
                                                                                <?php if ($VehicleEditOnlineResett) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehicleEditOnlineResett">Fahrzeug erstatten Online</a>'; } ?> 
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-4">
                                                                            <p class="m-0">
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-4">
                                                                            <p class="m-0">
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
					<!-- end: page -->
				</section>
			</div>

        </section>
        
        <!-- Wiederbeleben-->
        <div id="VehRevive" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post">
                    <div class="card-body text-center">
                        <div class="modal-wrapper">
                            <div class="modal-icon center">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <h4>Bist Du Dir sicher?</h4>
                                <p>Mit bestätigen Machst Du das Fahrzeug wieder Heile.</p>
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit" id="VehReviveID" name="VehReviveSubmit">Bestätigen</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <!-- In Garage Stellen-->
        <div id="VehPack" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post">
                    <div class="card-body text-center">
                        <div class="modal-wrapper">
                            <div class="modal-icon center">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <h4>Bist Du Dir sicher?</h4>
                                <p>Mit bestätigen stellst Du das Fahrzeug wieder in die Garage.</p>
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer"> 
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button class="btn btn-primary" type="submit" id="VehPackID" name="VehPackSubmit">Bestätigen</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <div id="VehImpound" class="modal-block modal-block-sm mfp-hide">
            <section class="card">
                <form method="post">
                    <header class="card-header">
                        <h2 class="card-title">Impound Status bearbeiten</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 1 }'>
                                <div class="input-group" style="width:345x;">
                                    <input type="text" class="spinner-input form-control" name="setVehImpoundName" id="setVehImpoundID" value="<?php echo $vehicleFourriere ?>" maxlength="1" readonly>
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
                                <button type="submit" id="VehImpoundSubmitID" name="VehImpoundSubmit" class="btn btn-primary">Speichern</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

         <!--Besitzer ändern -->                                                      
         <div id="VehChangeOwner" class="modal-block modal-block-sm mfp-hide">
            <section class="card">
                <form method="post">
                    <header class="card-header">
                        <h2 class="card-title">Besitzer ändern</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label text-lg-right pt-2" for="VehChangeOwnerID">Besitzer ändern</label>
                            <input class="form-control" name="VehChangeOwnerName" id="VehChangeOwnerID" maxlength="17" type="text" value="<?php echo $vehicleOwnerPid ?>">
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" id="VehChangeOwnerSubmitID" name="VehChangeOwnerSubmit" class="btn btn-primary">Speichern</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button> 
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        <!--Versicherung widerherstellen -->   
        <div id="VehInsurance" class="modal-block modal-block-sm mfp-hide">
            <section class="card">
                <form method="post">
                    <header class="card-header">
                        <h2 class="card-title">Versicherung bearbeiten</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 4 }'>
                                <div class="input-group" style="width:345x;">
                                    <input type="text" class="spinner-input form-control" name="setVehInsuranceName" id="setVehInsuranceID" value="<?php echo $vehicleInsurance ?>" maxlength="1" readonly>
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
                                <button type="submit" id="VehInsuranceSubmitID" name="VehInsuranceSubmit" class="btn btn-primary">Speichern</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>

        
        <!--Firma Ändern $entreprises -->   
        <div id="CompanyChange" class="modal-block modal-block-sm mfp-hide">
            <section class="card">
                <form method="post">
                    <header class="card-header">
                        <h2 class="card-title">Firma bearbeiten</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <select class="form-control" name="setCompanyChangeName" id="setCompanyChangeID">
                                <?php
                                    echo '<option value="0" selected="selected">'."Keine Firma".'</option>';
                                    foreach ($entreprises as $key => $value) {
                                        echo '<option value="'.$value["id"].'" '.($vehicleEntreprise_id == $value["id"] ? 'selected="selected"' : '').'>'.$value["name"].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" id="CompanyChangeSubmitID" name="CompanyChangeSubmit" class="btn btn-primary">Speichern</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                </form>
            </section>
        </div>
        
        <!--  -->
        <div id="VehicleEditOnlineResett" class="modal-block modal-block-primary mfp-hide">
            <section class="card">
                <form method="post">
                    <div class="card-body text-center">
                        <div class="modal-wrapper">
                            <div class="modal-icon center">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="modal-text">
                                <h4>Bist Du Dir sicher?</h4>
                                <p>Mit bestätigen stellst Du das Fahrzeug samt Inhalt wieder her. Der Spieler muss online sein.</p>
                            </div>
                        </div>
                    </div>
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">  
                                <button class="btn btn-primary" type="submit" id="VehicleEditOnlineResettSubmitID" name="VehicleEditOnlineResettSubmit">Bestätigen</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
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