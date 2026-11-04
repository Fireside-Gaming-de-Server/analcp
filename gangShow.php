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

$permission_name = "gangEdit";
$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
$stmt->bind_param("s", $permission_name);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$groupID_check = $row[2];
$groupID_check = json_decode($groupID_check);
$gangEdit = false;
if (in_array($groupID, $groupID_check)) {
	$gangEdit = true;
}

//permission Ende

$gangID = $_GET['id'];

$stmt3 = $dbm->prepare("SELECT * FROM gangs WHERE id=?");
$stmt3->bind_param("i", $gangID);
$stmt3->execute();
$result3 = $stmt3->get_result();
while ($row3 = $result3->fetch_assoc()) {
    $gangOwnerPID = $row3["owner"];
	$gangName = $row3["name"];
	$gangBank = $row3["bank"];
	$gangDate = $row3["insert_time"];
	$gangLevel = $row3["level"];
	$gangTag = $row3["tag"];
    // weitere Spalten können hier ausgegeben werden
}
$row3 = $result3->fetch_row();
$playersArr = array();
$stmt4 = $dbm->prepare("SELECT count(*) FROM perso WHERE gang_id=?");
$stmt4->bind_param("i", $gangID);
$stmt4->execute();
$result4 = $stmt4->get_result();
$row4 = $result4->fetch_row();
$gangMemberCount = $row4[0];

if (isset($_POST["gangChangeSubmitHidden"])) {
	if ($gangEdit) {
		$update = $dbm->prepare("UPDATE gangs SET name=?, tag=?, owner=?, bank=?, level=? WHERE id=?");
		$update->bind_param("sssiii", $_POST['changeName'], $_POST['changeTag'], $_POST['changeLeader'], $_POST['changeMoney'], $_POST['gangLevelChange'], $gangID);
		$update->execute();

		$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat die Gang mit der ID: "' . $gangID . '" bearbeitet. Vorher: "Name:' . $gangName . ', Tag:' . $gangTag . ', Leader:' . $gangOwnerPID . ', Bank:' . $gangBank . ', Level:' . $gangLevel . '"Nacher: "Name:' . $_POST['changeName'] . ', Tag:' . $_POST['changeTag'] . ', Leader:' . $_POST['changeLeader'] . ', Bank:' . $_POST['changeMoney'] . ', Level:' . $_POST['gangLevelChange'] . '"';
		$LogCategory = "ChangeGang";
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
		header("Refresh:0");
	}
}

require_once 'scripts/arc/arc.php';

use \Nizarii\ARC;

if (isset($_POST["gangBanSubmit"])) {
	if ($gangEdit) {

		$gangBanModalReason = $_POST["gangBanMessage"];
		$gangBanModalReasonIntern = $_POST["gangBanNote"];

		$query = "SELECT SpielerUID, Spielername FROM perso WHERE gang_id=$gangID";

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
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat die Gang  ' . $gangName . '  mit der ID: "' . $gangID . '" komplett weggebannt." Interner vermerk: "' . $gangBanModalReasonIntern . '" ';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
		header("Refresh:0");
	}
}

//Log visitGang
$LogCategory = "SiteActivity";
$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat sich die die Seite von der Gang [' . $gangTag . '] ' . $gangName . ' mit der ID ' . $gangID . ' angeschaut';
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
					<h2>Gang-Übersicht</h2>
				</header>
				<!-- start: page -->
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<section class="card">
							<header class="card-header">
								<div class="row">
									<div class="col-lg-4 col-xl-12">
										<h2 class="card-title"><?php echo "$gangName"; ?></h2>
									</div>
								</div>
							</header>
							<div class="col-lg-4 col-xl-12">
								<?php if ($gangEdit) {
									echo '<a class="modal-with-form mb-1 mt-1 mr-1 btn btn-default btn-block" href="#groupChangeModal"> Gang verfuschen  <i class="fas fa-hammer"></i></a>';
								}	?>
							</div>
							<div class="col-lg-4 col-xl-12">
								<?php if ($gangEdit) {
									echo '<a class="modal-with-form mb-1 mt-1 mr-1 btn btn-danger btn-block" href="#gangBan">Komplette Gang wegbannen  <i class="fas fa-hammer"></i></a>';
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
														<h5 class="title">GANG-TAG</h5>
														<div class="info">
															<strong class="amount"><?php echo $gangTag; ?></strong>
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
														<h5 class="title">GANG-ID</h5>
														<div class="info">
															<strong class="amount"><?php echo $gangID; ?></strong>
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
														<h5 class="title">GANG-LEVEL</h5>
														<div class="info">
															<strong class="amount"><?php echo $gangLevel; ?></strong>
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
														<h5 class="title">GANG-KONTO</h5>
														<div class="info">
															<strong class="amount"><?php echo number_format($gangBank, 0, ',', '.'); ?></strong>
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
														<h5 class="title">GANG-OWNER</h5>
														<div class="info">
															<strong class="amount"><?php echo $gangOwnerPID; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-3 col-xl-6">
									<section class="card">
										<header class="card-header">
											<h2 class="card-title"><?php echo "$gangName"; ?> - Mitgliederliste</h2>
										</header>
										<div class="card-body">
											<table class="table table-responsive-md table-bordered mb-0 datatable-default" id="datatable-default2">
												<thead>
													<tr>
														<th>Username</th>
														<th>PID</th>
														<th>Online</th>
														<!-- <th>Stunden Last 7 Days</th> -->
														<th>Button</th>
													</tr>
												</thead>
												<tbody>
													<?php


													$gangTime = 0;

													$playersOnline = '';
													$stmt = $dbm->prepare("SELECT onlinePlayers FROM rcon WHERE id=1");
													$stmt->execute();
													$result = $stmt->get_result();
													while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
														$playersOnline = json_decode($row['onlinePlayers']);
													}

													$query = "SELECT SpielerUID, Spielername FROM perso WHERE gang_id=$gangID";

													$db_erg = mysqli_query($dbm, $query);
													if (!$db_erg) {
														die('Ungültige Abfrage: ' . mysqli_error());
													}
													while ($zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
														$playersArr[] = array(
															"pid" => $zeile['SpielerUID'],
															"name" => $zeile['Spielername']
														);
														echo "<tr>";
														echo "<td>" . $zeile['Spielername'] . "</td>";
														echo "<td>" . $zeile['SpielerUID'] . "</td>";
														if (in_array($zeile['SpielerUID'], $playersOnline) === true) {
															echo '<td><font color="#00FF00">ONLINE</font></td>';
														} else {
															echo '<td><font color="#FF0000">OFFLINE</font></td>';
														}

														// Fusch START

														// $timeStats_playtime = array();
														// $stmt = $dbl->prepare("SELECT time_stamp , playtime_cop , playtime_med , playtime_civ FROM stats_player_daylie WHERE pid = ? && `time_stamp` > NOW() - INTERVAL 7 DAY ORDER BY time_stamp asc");
														// $stmt->bind_param("s", $zeile['SpielerUID']);
														// $stmt->execute();
														// $result = $stmt->get_result();
														// while ($row2 = mysqli_fetch_array($result)) {

														// 	$timeStats_playtime[] = array(
														// 		'playtime_civ' => $row2['playtime_civ'],
														// 		'playtime_cop' => $row2['playtime_cop'],
														// 		'playtime_med' => $row2['playtime_med'],
														// 		'playtime_gesamt' => $row2['playtime_civ'] + $row2['playtime_cop'] + $row2['playtime_med'],
														// 		'timeStamp' => $row2['time_stamp']

														// 	);
														// }

														// $last30Civ =  ($timeStats_playtime[count($timeStats_playtime) - 1]["playtime_civ"]) - ($timeStats_playtime[0]["playtime_civ"]);
														// $last30Cop =  ($timeStats_playtime[count($timeStats_playtime) - 1]["playtime_cop"]) - ($timeStats_playtime[0]["playtime_cop"]);
														// $last30med =  ($timeStats_playtime[count($timeStats_playtime) - 1]["playtime_med"]) - ($timeStats_playtime[0]["playtime_med"]);


														// $playtime_gesamt = $last30Civ + $last30Cop + $last30med;
														// $gangTime = $gangTime + $playtime_gesamt;


														// echo "<td>" . $playtime_gesamt . "</td>";
														// Fusch END 

														echo '<td><a class="btn btn-xs btn-default" href="playerShow.php?id=' . $zeile['SpielerUID'] . '" ><i class="fas fa-pencil-alt"></i>   Zum Spieler</a></td>';
														echo "</tr>";
														$playtime_gesamt = 0;
													}

													mysqli_free_result($db_erg);
													
													?>

												</tbody>
											</table>
										</div>
									</section>
								</div>

								<!-- <div class="col-lg-3 col-xl-2">
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
														<h5 class="title">Aktive Gang</h5>
														<div class="info">
															<strong class="amount"><?php if ($gangTime / $gangMemberCount > 20) {
																						$gangAktiv = "JA";
																					} else {
																						$gangAktiv = "NEIN";
																					}
																					echo $gangAktiv; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div> -->

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
														<h5 class="title">GANG-MITGLIEDER INSGESAMT</h5>
														<div class="info">
															<strong class="amount"><?php echo $gangMemberCount; ?></strong>
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
														<h5 class="title">GANG-INSERT DATE</h5>
														<div class="info">
															<strong class="amount"><?php echo $gangDate; ?></strong>
														</div>
													</div>
												</div>
											</div>
										</div>
									</section>
								</div>
							</div>


							<div id="groupChangeModal" class="modal-block modal-block-primary mfp-hide">
								<section class="card">
									<form class="form-horizontal form-bordered" method="post">
										<header class="card-header">
											<h2 class="card-title"><?php echo $gangName; ?> - Bearbeiten</h2>
										</header>
										<div class="card-body">
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Gang-Namen</label>
													<input name="changeName" id="changeName" type="text" value="<?php echo $gangName; ?>" class="form-control form-control-lg" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Gang-Tag</label>
													<input name="changeTag" id="changeTag" type="text" value="<?php echo $gangTag; ?>" class="form-control form-control-lg" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Gang-Owner</label>
													<input name="changeLeader" id="changeLeader" type="text" value="<?php echo $gangOwnerPID; ?>" class="form-control form-control-lg" data-plugin-maxlength maxlength="17" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Gang-Money</label>
													<input name="changeMoney" id="changeMoney" type="text" value="<?php echo $gangBank; ?>" class="form-control form-control-lg" />
												</div>
											</div>
											<div class="form-group row">
												<div class="col-sm-12 mb-6">
													<label>Gang-Level</label>
													<div data-plugin-spinner data-plugin-options='{ "value":0, "min": 0, "max": 5 }'>
														<div class="input-group form-control-small">
															<input name="gangLevelChange" id="gangLevelChange" type="text" class="spinner-input form-control" readonly="readonly" value="<?php echo $gangLevel; ?>">
															<div class="spinner-buttons input-group-btn btn-group-vertical">
																<button type="button" class="btn spinner-up btn-xs btn-default">
																	<i class="fas fa-angle-up"></i>
																</button>
																<button type="button" class="btn spinner-down btn-xs btn-default">
																	<i class="fas fa-angle-down"></i>
																</button>
															</div>
														</div>
													</div>
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

<div id="gangBan" class="modal-block modal-block-primary mfp-hide">
		<section class="card">
			<form method="post">
				<header class="card-header">
					<h2 class="card-title">Komplette Gang Wegbannen</h2>
				</header>
				<div class="card-body">
					<div class="form-group">
						<label class="control-label text-lg-right pt-2" for="gangBanMessageID">Ban-Grund</label>
						<input class="form-control" name="gangBanMessage" id="gangBanMessageID" type="text" value="Bitte im Support einfinden ts.fireside.gaming.de">
						<label class="control-label text-lg-right pt-2" for="gangBanTimeEndID">Ban-Ende</label>
						<input class="form-control" name="gangBanTimeEnd" id="gangBanTimeEndID" type="datetime-local" value="2099-12-31 23:59:00">
						<label class="control-label text-lg-right pt-2" for="gangBanNoteID">Notizen</label>
						<input class="form-control" name="gangBanNote" id="gangBanNoteID" type="text" value="Notiz #Gangban">
					</div>
				</div>
				<footer class="card-footer">
					<div class="row">
						<div class="col-md-12 text-right">
							<button class="btn btn-primary" type="submit" id="gangBanID" name="gangBanSubmit">Bestätigen</button>
							<button class="btn btn-default modal-dismiss">Cancel</button>
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