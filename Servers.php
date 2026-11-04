<?php
	include 'includes/checkOnline.php';
	$LogSupporterPid = $row[7];
	$LogSupporterName = $row[1];
	$LogSupporterGroup = $userGroupname;
	$LogUserPid = "-";
	$LogUserName = "-";

	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $_COOKIE["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	$groupID = $row[5];

	// $NeededRights = array();
	// $Other = array();
	// array_push($NeededRights, "visitBanRcon","visitBanServer","visitRealRcon","visitPlayerOnline","createWhitelist");
	// array_push($Other, "visitBanRcon2","visitBanServer1","visitRealRcon1","visitPlayerOnline1","createWhitelist1");
	// if (count(array_intersect($NeededRights, $Other)) > 0) {
	// 	echo "JA";
	// } else {
	// 	echo "NEIN";
	// }

	// exit;


	$Servers = array (
		"ArmaMain" => array(
			"PermissionRestart" => "ServerRestartArmaHaupt",
			"PermissionEdit" => "ServerEditArmaHaupt",
			"DisplayName" => "Arma3 - Hauptserver",
			"Database" => "main"
		),
		"ArmaSync" => array(
			"PermissionRestart" => "ServerRestartArmaHauptSync",
			"PermissionEdit" => "ServerEditArmaHauptSync",
			"DisplayName" => "Arma3 - Syncserver",
			"Database" => "mainsync"
		),
		"ArmaDev" => array(
			"PermissionRestart" => "ServerRestartArmaDev",
			"PermissionEdit" => "ServerEditArmaDev",
			"DisplayName" => "Arma3 - Testserver",
			"Database" => "armatest"
		)
	);

	//Permissions Edit Check
	$kickFormSite = true;
	$permissions = array();
	foreach ($Servers as $serverType => $serverDetails) {
		$permission_name = $serverDetails["PermissionRestart"];
		$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
		$stmt->bind_param("s", $permission_name);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_row();
		$permission_row = $row[2];
		$permission_row = json_decode($permission_row);
		if (in_array($groupID, $permission_row)) {
			array_push($permissions, $serverDetails["PermissionRestart"]);
			$kickFormSite = false;
		}

		$permission_name2 = $serverDetails["PermissionEdit"];
		$stmt2 = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
		$stmt2->bind_param("s", $permission_name);
		$stmt2->execute();
		$result2 = $stmt2->get_result();
		$row2 = $result2->fetch_row();
		$permission_row2 = $row2[2];
		$permission_row2 = json_decode($permission_row2);
		if (in_array($groupID, $permission_row2)) {
			array_push($permissions, $serverDetails["PermissionEdit"]);
			$kickFormSite = false;
		}
	}


	if ($kickFormSite) {
		header("Location: dashboard.php");
		exit();
	}

	$LogCategory = "SiteActivity";
	$LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat sich die Seite "Servers" angeschaut.';
	log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);

    $stmt = $dbm->prepare("SELECT * FROM acp_restart");
	$stmt->execute();
	$result = $stmt->get_result();

	$rows = array();
	while ($row = $result->fetch_assoc()) {
		$type = $row['type'];
		unset($row['type']);
		$rows[$type] = $row;
	}




	  foreach ($Servers as $serverType => $serverDetails) {

		if (isset($_POST["Serveredit{$serverType}"])) {
		  if (in_array($serverDetails["PermissionEdit"], $permissions)) {
				// Konvertiere die RestartTimes in ein JSON-Array
				$Times = $_POST["{$serverType}RestartTimes"];
				$TimesJson = json_encode($Times);
				$Path = $_POST["{$serverType}ApplicationPath"];

				$stmt = $dbm->prepare("UPDATE acp_restart SET RestartTimes = ?, BatchFilePath = ? WHERE type = ?");
				$stmt->bind_param("sss", $TimesJson, $Path, $serverDetails["Database"]);
				$stmt->execute();

				// $LogCategory = "Serveredit{$serverType}GameParams";
				// $oldval = json_decode($rows[$serverType]["RestartTimes"]);
				// $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . " hat die {$serverType}-Gameserver Parameter Editiert. Alt: Zeiten:{$oldval}Pfad:{$rows[$serverType]['BatchFilePath']} | Neu: Zeiten:{$MainGameRestartTimesJSON}Pfad:{$_POST["{$serverType}GameApplicationPath"]}";
				// log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, 0, "{$serverType}-Game Server");

			}
			header("Refresh:0");
		}
		if (isset($_POST["ServerRestart{$serverType}"])) {
			if (in_array($serverDetails["PermissionRestart"], $permissions)) {
				$stmt = $dbm->prepare("UPDATE acp_restart SET value = '1' WHERE type = ?");
				$stmt->bind_param("s", $serverDetails["Database"]);
				$stmt->execute();

				// $LogCategory = "ServerRestart{$serverType}Game";
				// $LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . " hat denn {$serverType}-Gameserver neugestartet.";
				// log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, 0, "{$serverType}-Game Server");
			}
			header("Refresh:0");
		}
	}


?>
<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Server - Manager</title>
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

			<?php
			include 'includes/header.php';
			?>

			<div class="inner-wrapper">

				<?php
				include 'includes/sideBar.php';
				?>

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Server - Manager</h2>

					</header>

						<!-- Start: Seite -->
						<div class="row">
							<?php foreach ($Servers as $serverType => $serverDetails) : ?>
								<div class="col-lg-4 col-xl-5">
									<section class="card card-featured-left card-featured-primary mb-4">
										<header class="card-header">
											<h3 class="card-title text-dark"><?php echo $serverDetails['DisplayName']; ?></h3>
										</header>
										<div class="card-actions">
											<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										</div>
										<div class="card-body">
											<form method="post">
												<div class="form-group">
													<section class="card card-featured-left card-featured-secondary mb-4">
														<header class="card-header">
															<h3 class="card-title text-dark">Restart Zeiten</h3>
														</header>
														<div class="card-body mb-3" id="<?php echo $serverType; ?>RestartTimesContainer">
															<div class="input-group mb-3">
																<button type="button" class="btn btn-success btn-block <?php echo $serverType; ?>AddButton" data-container="<?php echo $serverType; ?>RestartTimesContainer">Zeile hinzufügen</button>
															</div>
															<?php
															$restartTimes = json_decode($rows[$serverDetails["Database"]]["RestartTimes"]);
															if (!empty($restartTimes)) {
																foreach ($restartTimes as $index => $time) {
															?>
																	<div class="input-group mb-3">
																		<input type="time" class="form-control" name="<?php echo $serverType; ?>RestartTimes[]" id="<?php echo $serverType; ?>RestartTimes<?php echo $index; ?>" value="<?php echo $time; ?>" />
																		<div class="input-group-append">
																			<button type="button" class="btn btn-danger btn-sm deleteChangeButton">Löschen</button>
																		</div>
																	</div>
															<?php
																}
															} else {
															?>
																<div class="input-group mb-3">
																	<input type="time" class="form-control" name="<?php echo $serverType; ?>RestartTimes[]" id="<?php echo $serverType; ?>RestartTimes0" value="" />
																	<div class="input-group-append">
																		<button type="button" class="btn btn-danger btn-sm deleteChangeButton">Löschen</button>
																	</div>
																</div>
															<?php
															}
															?>
														</div>
													</section>
												</div>

												<section class="card card-featured-left card-featured-secondary mb-4">
													<header class="card-header">
														<h3 class="card-title text-dark">Application Path</h3>
													</header>
													<div class="card-body mb-3" id="<?php echo $serverType; ?>PathContainer">
														<input type="text" class="form-control" name="<?php echo $serverType; ?>ApplicationPath" id="<?php echo $serverType; ?>ApplicationPathID" value="<?php echo $rows[$serverDetails["Database"]]["BatchFilePath"]; ?>" />
													</div>
												</section>

												<button type="submit" name="Serveredit<?php echo $serverType; ?>" id="Serveredit<?php echo $serverType; ?>ID" class="mb-1 mt-4 mr-1 btn btn-success btn-block">Save</button>
												<button type="submit" name="ServerRestart<?php echo $serverType; ?>" id="ServerRestart<?php echo $serverType; ?>ID" class="mb-1 mt-3 mr-1 btn btn-primary btn-block">Ausführen</button>
											</form>
										</div>
									</section>
								</div>
							<?php endforeach; ?>
						</div>
						<!-- Ende: Seite -->


						<script>
							var addButtons = document.getElementsByClassName('MainAddButton');

							function addChange(event) {
								var containerId = event.target.getAttribute('data-container');
								var container = document.getElementById(containerId);
								var newFormGroup = document.createElement('div');
								newFormGroup.className = 'form-group';
								var newInputGroup = document.createElement('div');
								newInputGroup.className = 'input-group';
								var newInput = document.createElement('input');
								newInput.className = 'form-control';
								newInput.name = 'MainGameRestartTimes[]';
								newInput.id = 'restartTime' + container.childElementCount;
								newInput.type = 'time';
								newInput.placeholder = '';
								var newDeleteButton = document.createElement('button');
								newDeleteButton.className = 'btn btn-danger btn-sm deleteChangeButton';
								newDeleteButton.type = 'button';
								newDeleteButton.textContent = 'Löschen';
								newInputGroup.appendChild(newInput);
								newInputGroup.appendChild(newDeleteButton);
								newFormGroup.appendChild(newInputGroup);
								container.appendChild(newFormGroup);
							}

							for (var i = 0; i < addButtons.length; i++) {
								addButtons[i].addEventListener('click', addChange);
							}

							var addButtons = document.getElementsByClassName('MainSyncAddButton');

							function addChange(event) {
								var containerId = event.target.getAttribute('data-container');
								var container = document.getElementById(containerId);
								var newFormGroup = document.createElement('div');
								newFormGroup.className = 'form-group';
								var newInputGroup = document.createElement('div');
								newInputGroup.className = 'input-group';
								var newInput = document.createElement('input');
								newInput.className = 'form-control';
								newInput.name = 'MainSyncRestartTimes[]';
								newInput.id = 'restartTime' + container.childElementCount;
								newInput.type = 'time';
								newInput.placeholder = '';
								var newDeleteButton = document.createElement('button');
								newDeleteButton.className = 'btn btn-danger btn-sm deleteChangeButton';
								newDeleteButton.type = 'button';
								newDeleteButton.textContent = 'Löschen';
								newInputGroup.appendChild(newInput);
								newInputGroup.appendChild(newDeleteButton);
								newFormGroup.appendChild(newInputGroup);
								container.appendChild(newFormGroup);
							}

							for (var i = 0; i < addButtons.length; i++) {
								addButtons[i].addEventListener('click', addChange);
							}




							function deleteChange(event) {
								var formGroup = event.target.parentNode.parentNode;
								formGroup.parentNode.removeChild(formGroup);
							}

							document.addEventListener('click', function (event) {
								if (event.target.classList.contains('deleteChangeButton')) {
									deleteChange(event);
								}
							});
						</script>
						<!-- end: page -->




				</section>
			</div>

		</section>



		<script src="vendor/jquery/jquery.js"></script>
	<script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
	<script src="vendor/popper/umd/popper.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.js"></script>
	<script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="vendor/nanoscroller/nanoscroller.js"></script>
	<script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
	<script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>

	<!-- Specific Page Vendor -->
	<script src="vendor/jquery-ui/jquery-ui.js"></script>
	<script src="vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js"></script>
	<script src="vendor/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
	<script src="vendor/jquery-maskedinput/jquery.maskedinput.js"></script>
	<script src="vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
	<script src="vendor/fuelux/js/spinner.js"></script>
	<script src="vendor/dropzone/dropzone.js"></script>
	<script src="vendor/select2/js/select2.js"></script>
	<script src="vendor/pnotify/pnotify.custom.js"></script>
	<script src="js/examples/examples.notifications.js"></script>
	<!-- Theme Base, Components and Settings -->
	<script src="js/theme.js"></script>

	<!-- Theme Custom -->
	<script src="js/custom.js?t=2"></script>

	<!-- Theme Initialization Files -->
	<script src="js/theme.init.js"></script>

	<!-- Vendor -->
	<script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
	<script src="vendor/popper/umd/popper.min.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.js"></script>
	<script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
	<script src="vendor/nanoscroller/nanoscroller.js"></script>
	<script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
	<script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>

	<!-- Specific Page Vendor -->
	<script src="vendor/jquery-appear/jquery.appear.js"></script>
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
	<script src="vendor/liquid-meter/liquid.meter.js"></script>
	<script src="vendor/chartist/chartist.js"></script>

		<script>
            $(document).ready(function(){
                $("#clearID").click(function(){
                    $("#adminMessage").val("");
                });
            });
        </script>


	</body>
</html>