<?php
require('includes/checkOnline.php');


$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
$stmt->bind_param("s", $_COOKIE["username"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID = $row[5];
//permission check
$permission_name = "companyShowVisit";
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

// //permission edit check
// $permissionArr = [ 'companyEditRevive', 'companyEditImpound', 'companyEditGarage', 'companyEditInsurance', 'companyEditOwner', 'companyEditOnlineResett' ];
// foreach ( $permissionArr as $value ){											 
//     $permission_name = $value;
//     $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
//     $stmt->bind_param("s", $permission_name);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $row = $result->fetch_row();
//     $permission_row = $row[2];
//     $permission_row = json_decode($permission_row);
//     ${$permission_name} = false;
//     if (in_array($groupID, $permission_row)) {
//       ${$permission_name} = true;
//     } 
// }

$companyID = $_GET['id'];

//Get the Company info
// SELECT * FROM entreprises WHERE members::text LIKE CONCAT('%', ?, '%')
$stmt = $dbm->prepare("SELECT * FROM entreprises WHERE id=?");
$stmt->bind_param("s", $companyID);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
	$CompanyID              = $row["id"];
	$CompanyName            = $row["name"];
	$CompanyOwnerPID        = $row["pdg_pid"];
	$CompanyOwnerName       = $row["pdg_name"];
	$CompanyBank            = $row["bankacc"];
	$CompanyType            = $row["type"];
    switch ($CompanyType) {
        case 'autoentreprise':
            $CompanyTypeText    = "Klein-Gewerbe";
            break;
        case 'sarl':
            $CompanyTypeText    = "Mittleres-Gewerbe";
            break;
        case 'sas':
            $CompanyTypeText    = "Großes-Gewerbe";
            break;
        case 'sa':
            $CompanyTypeText    = "Firma";
            break;
        default:
            $CompanyTypeText    = "N/A";
            break;
    }
	$CompanyActive          = $row["active"];
	$CompanySealed          = $row["sealed"];
	$CompanyInsertTime      = $row["insert_time"];
	$CompanyPosition        = $row["position"];

	$string                 = $row["members"];
    $json                   = str_replace('`', '"', $string);
    $CompanyMembers      = json_decode(trim($json, '"'), true);
	
	$string                 = $row["prices"];
    $json                   = str_replace('`', '"', $string);
    $CompanyPrice        = json_decode(trim($json, '"'), true);
	
	$string                 = $row["items_stock"];
    $json                   = str_replace('`', '"', $string);
    $CompanyStock        = json_decode(trim($json, '"'), true); //Format [["Item1",Anzahl1],["Item2",Anzahl2]]
	$CompanyMaxStock        = $row["max_stock"]; 
	
	$string                 = $row["items_stock_2"];
    $json                   = str_replace('`', '"', $string);
    $CompanyStockPrivate = json_decode(trim($json, '"'), true); //Format [["Item1",Anzahl1],["Item2",Anzahl2]]
	$CompanyMaxPrivateStock = $row["max_stock_2"];
}



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

    // Add the item to the new array using class name as key
    $CompanyItemArray[$className] = [
        "displayname" => $viteminfoarray[$className]["displayname"],
        "publiccount" => $publicCount,
        "privatecount" => $privateCount,
        "price" => $price,
        "weight" => $viteminfoarray[$className]["weight"],
    ];
}

// echo "<pre>";
// print_r($CompanyItemArray);
// echo "</pre>";

// exit;






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
                                                                <a class="nav-link text-dark" href="#companyOwner" data-toggle="tab">Fahrzeug & Besitzer</a>
                                                            </li>
                                                            <li class="nav-item">
                                                                <a class="nav-link text-dark" href="#companyInformation" data-toggle="tab">Informationen</a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div id="companyOwner" class="tab-pane active">
                                                                <div class="col-lg-6 col-xl-12">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">company-ID</h5><?php echo $CompanyID; ?>
                                                                            <h5 class="text-dark">company-Typ</h5><?php echo $CompanyTypeText; ?>
                                                                            <h5 class="text-dark">Kaufdatum</h5><?php echo $CompanyInsertTime; ?>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-6">
                                                                            <h5 class="text-dark">Besitzer</h5><?php echo $CompanyOwnerName; ?>
                                                                            <h5 class="text-dark">PlayerID</h5><?php echo $CompanyOwnerPID; ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                            <div id="companyInformation" class="tab-pane">
                                                                <div class="col-lg-6 col-xl-12">
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-xl-6">
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
                                        <!-- <div class="col-lg-4 col-xl-6">
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
                                                                    <strong class="amount"><?php echo $companyImmatriculation; ?></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>
                                        </div> -->
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
                                                                                <!-- <?php if ($companyEditRevive) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehRevive">Wiederbeleben</a>'; } ?>
                                                                                <?php if ($companyEditImpound) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehImpound">Impound Status</a>'; } ?> -->
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-4">
                                                                            <p class="m-0">
                                                                                <!-- <?php if ($companyEditGarage) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehPack">In Garage stellen</a>'; } ?>
                                                                                <?php if ($companyEditInsurance) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehInsurance">Versicherung ändern</a>'; } ?>  -->
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-lg-6 col-xl-4">
                                                                            <p class="m-0">
                                                                                <!-- <?php if ($companyEditOwner) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#VehChangeOwner">Besitzer ändern</a>'; } ?>
                                                                                <?php if ($companyEditOwner) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#CompanyChange">Firma ändern</a>'; } ?>  -->
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
                                                                                <!-- <?php if ($companyEditOnlineResett) {echo '<a class="modal-with-form btn btn-default btn-lg btn-block" href="#companyEditOnlineResett">Fahrzeug erstatten Online</a>'; } ?>  -->
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
                                    <input type="text" class="spinner-input form-control" name="setVehImpoundName" id="setVehImpoundID" value="<?php echo $companyFourriere ?>" maxlength="1" readonly>
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
                        <h2 class="card-title">Geld geben</h2>
                    </header>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label text-lg-right pt-2" for="VehChangeOwnerID">Besitzer ändern</label>
                            <input class="form-control" name="VehChangeOwnerName" id="VehChangeOwnerID" maxlength="17" type="text" value="<?php echo $companyOwnerPid ?>">
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
                                    <input type="text" class="spinner-input form-control" name="setVehInsuranceName" id="setVehInsuranceID" value="<?php echo $companyInsurance ?>" maxlength="1" readonly>
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
                                        echo '<option value="'.$value["id"].'" '.($companyEntreprise_id == $value["id"] ? 'selected="selected"' : '').'>'.$value["name"].'</option>';
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
        <div id="companyEditOnlineResett" class="modal-block modal-block-primary mfp-hide">
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
                                <button class="btn btn-primary" type="submit" id="companyEditOnlineResettSubmitID" name="companyEditOnlineResettSubmit">Bestätigen</button>
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

<?php
$dbm->close();
?>
