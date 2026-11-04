<?php
require('includes/checkOnline.php');


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

// //permission edit check
// $permissionArr = [ 'vehicleEditRevive', 'vehicleEditImpound', 'vehicleEditGarage', 'vehicleEditInsurance', 'vehicleEditOwner', 'VehicleEditOnlineResett' ];
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

$vehicleID = $_GET['id'];

//Get the Company info
// SELECT * FROM entreprises WHERE members::text LIKE CONCAT('%', ?, '%')
$stmt = $dbm->prepare("SELECT * FROM entreprises WHERE id=?");
$stmt->bind_param("s", $vehicleID);
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
<html class="fixed has-top-menu dark">

<head>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Firmen - Info</title>
    <meta name="keywords" content="Userpanel" />
    <meta name="description" content="Trinity Life">
    <meta name="author" content="klausi♥">

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
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.theme.css" />
    <link rel="stylesheet" href="vendor/bootstrap-multiselect/css/bootstrap-multiselect.css" />
    <link rel="stylesheet" href="vendor/morris/morris.css" />
    <link rel="stylesheet" href="vendor/select2/css/select2.css" />
    <link rel="stylesheet" href="vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
    <link rel="stylesheet" href="vendor/datatables/media/css/dataTables.bootstrap4.css" />
    <link rel="stylesheet" href="vendor/pnotify/pnotify.custom.css" />
    <!-- Theme CSS -->
    <link rel="stylesheet" href="css/theme.css" />

    <!-- Skin CSS -->
    <link rel="stylesheet" href="css/skins/default.css" />

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">

    <!-- Head Libs -->
    <script src="vendor/modernizr/modernizr.js"></script>

</head>

<body>

    <body class="loading-overlay-showing" data-loading-overlay>
        <div class="loading-overlay">
            <div class="bounce-loader">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
        </div>
        <?php
        require('includes/header.php');
        ?>
        <div class="inner-wrapper">
            <section role="main" class="content-body">
                <header class="page-header">
                    <h2>Firmen - Info</h2>

                </header>

                <!-- start: page -->

                <div class="row">
                    <div class="col-lg-3 col-xl-3">
                        <section class="card card-featured-left card-featured-primary mb-4">
                            <div class="card-body">
                                <div class="widget-summary widget-summary-md">
                                    <div class="widget-summary-col widget-summary-col-icon">
                                        <div class="summary-icon bg-primary">
                                            <i class="fas fa-flag"></i>
                                        </div>
                                    </div>
                                    <div class="widget-summary-col">
                                        <div class="summary">
                                            <h5 class="title">NAME</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo $CompanyName; ?></strong>
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
                                            <h5 class="title">ID</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo $CompanyID; ?></strong>
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
                                            <h5 class="title">LEVEL</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo $CompanyTypeText; ?></strong>
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
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="widget-summary-col">
                                        <div class="summary">
                                            <h5 class="title">MITGLIEDER INSGESAMT</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo $CompanyMembersCount; ?></strong>
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
                                            <h5 class="title">KONTO</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo number_format($CompanyBank, 0, ',', '.'); ?></strong>
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
                                            <h5 class="title">OWNER</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo $CompanyOwnerPID; ?></strong>
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
                                            <h5 class="title">OWNER</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo $CompanyOwnerName; ?></strong>
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
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                    </div>
                                    <div class="widget-summary-col">
                                        <div class="summary">
                                            <h5 class="title">Gründungsdatum</h5>
                                            <div class="info">
                                                <strong class="amount"><?php echo $CompanyInsertTime; ?></strong>
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
														<th>Privat Lager <?php echo "[" . $PublicUsedSpace . "/" . $CompanyMaxStock . "]" ?></th>
														<th>Öffentliches Lager  <?php echo "[" . $PrivateUsedSpace . "/" . $CompanyMaxPrivateStock . "]" ?></th>
														<th>Ankaufspreis</th>
													</tr>
												</thead>
												<tbody>
													<?php
                                                    	foreach ($CompanyItemArray as $classname => $values) {
                                                            echo "<tr>";
                                                            echo "<td>" . $values["displayname"] . "</td>";
                                                            echo "<td>" . $values["privatecount"] . "</td>";
                                                            echo "<td>" . $values["publiccount"] . "</td>";
                                                            echo "<td>" . $values["price"] . "</td>";
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





            </section>

            <!-- Vendor -->
            <script src="vendor/jquery/jquery.js"></script>
            <script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
            <script src="vendor/pnotify/pnotify.custom.js"></script>
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
            <script src="vendor/jquery-ui/jquery-ui.js"></script>
            <script src="vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js"></script>
            <script src="vendor/jquery-appear/jquery.appear.js"></script>
            <script src="vendor/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
            <script src="vendor/jquery.easy-pie-chart/jquery.easypiechart.js"></script>
            <script src="vendor/flot/jquery.flot.js"></script>
            <script src="vendor/flot.tooltip/jquery.flot.tooltip.js"></script>
            <script src="vendor/flot/jquery.flot.pie.js"></script>
            <script src="vendor/flot/jquery.flot.categories.js"></script>
            <script src="vendor/flot/jquery.flot.resize.js"></script>
            <script src="vendor/jquery-sparkline/jquery.sparkline.js"></script>
            <script src="vendor/raphael/raphael.js"></script>
            <script src="vendor/morris/morris.js"></script>
            <script src="vendor/gauge/gauge.js"></script>
            <script src="vendor/snap.svg/snap.svg.js"></script>

            <!-- Theme Base, Components and Settings -->
            <script src="js/theme.js"></script>

            <!-- Theme Custom -->
            <script src="js/custom.js"></script>

            <!-- Theme Initialization Files -->
            <script src="js/theme.init.js"></script>

            <!-- Examples -->
            <script src="js/examples/examples.notifications.js"></script>
            <script src="js/examples/examples.header.menu.js"></script>
            <script src="js/examples/examples.dashboard.js"></script>
            <script src="js/examples/examples.modals.js"></script>


            

            <script type="text/javascript">
                Morris.Donut({
                    resize: true,
                    element: 'Donut1',
                    data: Donut1Var,
                    colors: ['#ffe400', '#ffae00', '#ff7800', '#ec261c']
                });



                Morris.Donut({
                    resize: true,
                    element: 'Donut2',
                    data: Donut2Var,
                    colors: ['#ffe400', '#ffae00', '#ff7800', '#ec261c']

                });
            </script>

            <script>
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.href);
                }
            </script>

            <?php require('includes/echoMessages.php');  ?>

    </body>

</html>

<?php
$dbm->close();
?>