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

	$kickFormSite = true;
	foreach ($permissionArr as $value) {
		if (${$value}) {
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

	$Servers = array (
		"Main" => "MainDB",
		"Sync" => "SyncDB"
		);
		print "<pre>";
		print_r($Servers);
		print "</pre>";
	exit;

	// GAMESERVER *********************************************************
	if (isset($_POST["ServereditMainGameParams"])) {
		if ($ServereditMainGameParams) {
			// Hole die Eingaben aus dem Formular
			$MainGameRestartTimes = $_POST["MainGameRestartTimes"];

			// Konvertiere die MainGameRestartTimes in ein JSON-Array
			$MainGameRestartTimesJSON = json_encode($MainGameRestartTimes);

			$stmt = $dbm->prepare("UPDATE acp_restart SET RestartTimes = ?, BatchFilePath = ? WHERE type = 'main'");
            $stmt->bind_param("ss", $MainGameRestartTimesJSON, $_POST['MainGameApplicationPath']);
            $stmt->execute();
		
			$LogCategory = "ServereditMainGameParams";
			$oldval = json_decode($rows["main"]["RestartTimes"]);
			$LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat die Haupt-Gameserver Parameter Editiert. Alt: Zeiten:'. $oldval . 'Pfad: '. $rows["main"]["BatchFilePath"]. ' | Neu: Zeiten:'. $MainGameRestartTimesJSON . 'Pfad: '. $_POST['MainGameApplicationPath'];
			log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, 0, "Main-Game Server");
			
		}
		header("Refresh:0");
	}
	if (isset($_POST["ServerRestartMainGame"])) {
		if ($ServerRestartMainGame) {
			$stmt = $dbm->prepare("UPDATE acp_restart SET value = '1' WHERE type = 'main'");
            $stmt->execute();
			
			$LogCategory = "ServerRestartMainGame";
			$LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat denn Haupt-Gameserver neugestartet.';
			log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, 0, "Main-Game Server");
		}
		header("Refresh:0");
	}
	
	// GAMESERVER *********************************************************
	if (isset($_POST["ServereditMainSyncParams"])) {
		if ($ServereditMainSyncParams) {
			// Hole die Eingaben aus dem Formular
			$MainSyncRestartTimes = $_POST["MainSyncRestartTimes"];
			// Konvertiere die MainSyncRestartTimes in ein JSON-Array
			$MainSyncRestartTimesJSON = json_encode($MainSyncRestartTimes);

			$stmt = $dbm->prepare("UPDATE acp_restart SET RestartTimes = ?, BatchFilePath = ? WHERE type = 'mainsync'");
            $stmt->bind_param("ss", $MainSyncRestartTimesJSON, $_POST['MainSyncApplicationPath']);
            $stmt->execute();
		
			$LogCategory = "ServereditMainSyncParams";
			$oldval = json_decode($rows["mainsync"]["RestartTimes"]);
			$LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat die Haupt-Syncserver Parameter Editiert. Alt: Zeiten:'. $oldval . 'Pfad: '. $rows["mainsync"]["BatchFilePath"]. ' | Neu: Zeiten:'. $MainSyncRestartTimesJSON . 'Pfad: '. $_POST['MainSyncApplicationPath'];
			log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, 0, "Main-Sync Server");

		}
		header("Refresh:0");
	}
	if (isset($_POST["ServerRestartMainSync"])) {
		if ($ServerRestartMainSync) {
			$stmt = $dbm->prepare("UPDATE acp_restart SET value = '1' WHERE type = 'mainsync'");
            $stmt->execute();
			
			$LogCategory = "ServerRestartMainSync";
			$LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat denn Haupt-Syncserver neugestartet.';
			log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, 0, "Main-Sync Server");
		}
		header("Refresh:0");
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

						<!-- start: page -->
							<div class="row">
								<div class="col-lg-4 col-xl-5">
									<section class="card card-featured-left card-featured-primary mb-4">
										<header class="card-header">
											<h3 class="card-title text-dark">Haupt-Server</h3>
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
														<div class="card-body mb-3" id="MainGameRestartTimesContainer">
															<div class="input-group mb-3">
																<button type="button" class="btn btn-success btn-block MainAddButton" data-container="MainGameRestartTimesContainer">Zeile hinzufügen</button>
															</div>
															<?php
															$MainGameRestartTimes = json_decode($rows["main"]["RestartTimes"]);
															if (!empty($MainGameRestartTimes)) {
																foreach ($MainGameRestartTimes as $index => $time) {
																	?>
																		<div class="input-group mb-3">
																			<input type="time" class="form-control" name="MainGameRestartTimes[]" id="MainGameRestartTimes<?php echo $index; ?>" value="<?php echo $time; ?>" />
																			<div class="input-group-append">
																				<button type="button" class="btn btn-danger btn-sm deleteChangeButton">Löschen</button>
																			</div>
																		</div>
																	<?php
																}
															} else {
																?>
																	<div class="input-group mb-3">
																		<input type="time" class="form-control" name="MainGameRestartTimes[]" id="MainGameRestartTimes0" value="" />
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
													<div class="card-body mb-3" id="PathContainer">
														<input type="text" class="form-control" name="MainGameApplicationPath" id="MainGameApplicationPathID" value="<?php echo $rows["main"]["BatchFilePath"]; ?>" />
													</div>
												</section>

												<button type="submit" name="ServereditMainGameParams" id="ServereditMainGameParamsID" class="mb-1 mt-4 mr-1 btn btn-success btn-block">Save</button>
												<button type="submit" name="ServerRestartMainGame" id="ServerRestartMainGameID" class="mb-1 mt-3 mr-1 btn btn-primary btn-block">Restart</button>
											</form>
										</div>
									</section>
								</div>

								<div class="col-lg-4 col-xl-5">
									<section class="card card-featured-left card-featured-primary mb-4">
										<header class="card-header">
											<h3 class="card-title text-dark">Sync-Server</h3>
										</header>
										<div class="card-actions">
											<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										</div>
										<div class="card-body">
											<form method="post">
												<div class="form-group">
													<section class="card card-featured-left card-featured-secondary mb-4">
														<header class="card-header">
															<h3 class="card-title text-dark">Build Zeiten</h3>
														</header>
														<div class="card-body mb-3" id="MainSyncRestartTimesContainer">
															<div class="input-group mb-3">
																<button type="button" class="btn btn-success btn-block MainSyncAddButton" data-container="MainSyncRestartTimesContainer">Zeile hinzufügen</button>
															</div>
															<?php
															$MainSyncRestartTimes = json_decode($rows["mainsync"]["RestartTimes"]);
															if (!empty($MainSyncRestartTimes)) {
																foreach ($MainSyncRestartTimes as $index => $time) {
															?>
																	<div class="input-group mb-3">
																		<input type="time" class="form-control" name="MainSyncRestartTimes[]" id="MainSyncRestartTimes<?php echo $index; ?>" value="<?php echo $time; ?>" />
																		<div class="input-group-append">
																			<button type="button" class="btn btn-danger btn-sm deleteChangeButton">Löschen</button>
																		</div>
																	</div>
															<?php
																}
															} else {
															?>
																<div class="input-group mb-3">
																	<input type="time" class="form-control" name="MainSyncRestartTimes[]" id="MainSyncRestartTimes0" value="" />
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
													<div class="card-body mb-3" id="PathContainer">
														<input type="text" class="form-control" name="MainSyncApplicationPath" id="MainSyncApplicationPathID" value="<?php echo $rows["mainsync"]["BatchFilePath"]; ?>" />
													</div>
												</section>

												<button type="submit" name="ServereditMainSyncParams" id="ServereditMainSyncParamsID" class="mb-1 mt-4 mr-1 btn btn-success btn-block">Save</button>
												<button type="submit" name="ServerRestartMainSync" id="ServerRestartMainSyncID" class="mb-1 mt-3 mr-1 btn btn-primary btn-block">Build</button>
											</form>
										</div>
									</section>
								</div>
							</div>
						</div>


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