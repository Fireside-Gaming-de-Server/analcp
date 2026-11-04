<?php 
	include 'includes/checkOnline.php';
	$LogUserPid = "-";
	$LogUserName = "-";

	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $_COOKIE["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	$groupID = $row[5];

	$permission_name = "visitRealRcon";
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

	$permission_name = "playerUnban";
	$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	$groupID_check = $row[2];
	$groupID_check = json_decode($groupID_check);
	if (in_array($groupID, $groupID_check)) {
		$playerUnban = TRUE;
	} else {
		$playerUnban = FALSE;
	}
       
	$LogCategory = "SiteActivity";
	$LogText = '['.$LogSupporterGroup.'] '.$LogsupporterName.' hat sich die Seite "Banliste - Server" angeschaut.';
	log_acp($LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);


	if (isset($_POST["playerUnbanSubmit"])) {
		if ($playerUnban) {
			$pid = $_POST["playerUnban"];
			$stmt = $dbm->prepare("SELECT * FROM acp_bans WHERE uid=?");
			$stmt->bind_param("s", $pid);
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = $result->fetch_assoc()) {
				$banVorher = 'UID: "'.$row['uid'].'" Name: "'.$row['name'].'" Ban Start: "'.$row['ban_start'].'" Ban Ende: "'.$row['ban_end'].'" S-UID: "'.$row['banned_by'].'" Suporrter: "'.$row['banned_by_name'].'" Grund: "'.$row['ban_reason'].'" Info: "'.$row['Info'];
			}
			
			$name = $row['name'];
			if ($result->num_rows > 0) {
				$sql = "DELETE FROM acp_bans WHERE uid = $pid";
				if (mysqli_query($dbm, $sql)) {
					$LogCategory = "unbanPlayer";
					$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Ban von: [' . $pid . '] "' . $name . '" aufehoben. Aufgehobener Ban: '.$banVorher.'';
					log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
					header("Refresh:0");
				} else {
					error_log("Fehler bei 'playerUnbanSubmit'! <> " . mysqli_error($dbm)); 
					$LogCategory = "unbanPlayerError";
					$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat beim versuch: [' . $pid . '] "' . $name . '" zu entbannen einen Error ausgelöst.';
					log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
					header("Refresh:0");
				}
			} elseif (!$result->num_rows > 0) {
				$LogCategory = "unbanPlayerTry";
				$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat versucht: [' . $pid . '] "' . $name . '" zu entbannen.';
				log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
				header("Refresh:0");
			}
		}
	}

//Ban Edit
if (isset($_POST["banEditSubmit"])) {
	if ($playerUnban) {
		$Reason = $_POST['BanEditReason'];
		$EndDate = $_POST['BanEditEnd'];
		$pid = $_POST['banEdituid'];
		$Info = $_POST['BanEditInfo'];
		
		$stmt = $dbm->prepare("SELECT * FROM acp_bans WHERE uid=?");
        $stmt->bind_param("s", $pid);
        $playerid = $pid;
        $stmt->execute();
        $result = $stmt->get_result();
		while ($row = $result->fetch_assoc()) {
            $banVorher = 'UID: "'.$row['uid'].'" Name: "'.$row['name'].'" Ban Start: "'.$row['ban_start'].'" Ban Ende: "'.$row['ban_end'].'" S-UID: "'.$row['banned_by'].'" Suporrter: "'.$row['banned_by_name'].'" Grund: "'.$row['ban_reason'].'" Info: "'.$row['info'];
            $banNachher = 'UID: "'.$row['uid'].'" Name: "'.$row['name'].'" Ban Start: "'.$row['ban_start'].'" Ban Ende: "'.$EndDate.'" S-UID: "'.$LogSupporterPid.'" Suporrter: "'.$LogSupporterName.'" Grund: "'.$Reason.'" Info: "'.$Info;
        }



		
		$stmt = $dbm->prepare("UPDATE acp_bans SET ban_end = ?, banned_by = ?, banned_by_name = ?, ban_reason = ?, info = ? WHERE uid = ?");
		$stmt->bind_param("ssssss", $EndDate, $LogSupporterPid, $LogSupporterName, $Reason, $Info, $pid);
				
		$stmt->execute();

		
		$LogCategory = "editbanPlayer";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogSupporterName . ' hat den Ban von: [' . $pid . '] "' . $name . '" editiert. Alte Werte: '.$banVorher.' Neue Werte: '.$banNachher.'';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogSupporterName, $LogSupporterGroup, $pid, $name);
		header("Refresh:0");
	}
}



?>
<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Banliste - Server</title>
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
						<h2>Banliste - Server</h2>
					
					</header>

						<!-- start: page -->
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
									</div>
							
									<h2 class="card-title">Banliste - Server</h2>
								</header>
								<div class="card-body">
                                    <table class="table table-bordered table-striped" id="datatable-noAjaxajax3">
                                        <thead>
										<tr>
											<th>Steam64-ID</th>
											<th>Name</th>
											<th>Reason</th>
											<th>Ban Start</th>
											<th>Ban End</th>
											<th>Banned By</th>
											<th>Info</th>
											<th>Zum Profil</th>
											<th>Edit</th>
											<th></th>
											<th></th>
										</tr>
                                        </thead>
                                        <tbody>
                                            <?php   
                                           $stmt = $dbm->prepare("SELECT * FROM acp_bans");
										   $stmt->execute();
										   $result = $stmt->get_result();
										   while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC))
										   {
											   echo "<tr>";
											   echo "<td>" . $zeile["uid"] . "</td>";
											   echo "<td>" . $zeile["name"] . "</td>";
											   echo "<td>" . $zeile["ban_reason"] . "</td>";
											   echo "<td>" . date("d.m.Y H:i", strtotime($zeile["ban_start"])) . "</td>";
											   // Überprüfen, ob das Jahr 2099 ist
											   if (intval(substr($zeile["ban_end"], 0, 4)) >= 2099) {
													echo '<td><span class="text-danger">PERMANENT</span></td>';
												} elseif (intval(substr($zeile["ban_end"], 0, 4)) >= 2088) {
													echo '<td><span class="text-warning">FALLKLÄRUNG</span></td>';
												} else {
													echo "<td>" . date("d.m.Y H:i", strtotime($zeile["ban_end"])) . "</td>";
												}
											   echo "<td>" . $zeile["banned_by_name"] . "</td>";
											   
											   echo "<td>" . $zeile["info"] . "</td>";
											   echo '<td><a class="btn btn-xs btn-default" href="playerShow.php?id='.$zeile['uid'].'"><i class="fas fa-pencil-alt"></i> Zum Profil</a></td>';
											   if ($playerUnban) {
												echo '<td><a class="btn btn-xs btn-default" href="#" data-toggle="modal" data-target="#editModal'.$zeile["uid"].'"><i class="fas fa-pencil-alt"></i> Edit</a></td>
														<div class="modal fade" id="editModal'.$zeile["uid"].'" tabindex="-1" role="dialog" aria-labelledby="editModalLabel'.$zeile["uid"].'">
														<div class="modal-dialog" role="document">
															<div class="modal-content">
																<div class="modal-header">
																<h4 class="modal-title" id="editModalLabel'.$zeile["uid"].'">Edit Entry (ID: '.$zeile["uid"].')</h4>
																<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
															</div>
																<div class="modal-body">
																	<form method="post">
																		<div class="form-group mt-3">
																			<label for="reason'.$zeile["uid"].'">Reason</label>
																			<textarea class="form-control" id="BanEditReason'.$zeile["uid"].'" name="BanEditReason">'.$zeile["ban_reason"].'</textarea>
																		</div>
																		<div class="form-group mt-3">
																			<label for="info'.$zeile["uid"].'">Info</label>
																			<textarea class="form-control" id="BanEditInfo'.$zeile["uid"].'" name="BanEditInfo">'.$zeile["info"].'</textarea>
																		</div>
																		<div class="form-group mt-3">
																			<label for="ban_end'.$zeile["uid"].'">Ban End</label>
																			<input type="datetime-local" class="form-control" id="BanEditEnd'.$zeile["uid"].'" name="BanEditEnd" value="'.$zeile["ban_end"].'">
																		</div>
																		<input type="hidden" name="banEdituid" value="'.$zeile["uid"].'">
																		<button type="submit" class="btn btn-primary" id="banEditSubmitID" name="banEditSubmit">Save Changes</button>
																		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
																	</form>
																</div>
															</div>
														</div>
													</div>
												
													';
													echo '<td>
													<a class="modal-with-form btn btn-xs btn-danger" href="#playerUnban' . $zeile['uid'] . '">Entbannen</a>
													<div id="playerUnban' . $zeile['uid'] . '" class="modal-block modal-block-primary mfp-hide">
														<section class="card">
															<form method="post">
																<div class="card-body text-center">
																	<div class="modal-wrapper">
																		<div class="modal-icon center">
																			<i class="fas fa-question-circle"></i>
																		</div>
																		<div class="modal-text">
																			<h4>Bist Du Dir sicher?</h4>
																			<p>Mit bestätigen löscht du den Eintrag und der Spieler kann wieder Connecten.</p>
																		</div>
																	</div>
																</div>
																<footer class="card-footer">
																	<div class="row">
																		<div class="col-md-12 text-right">
																			<input type="hidden" id="playerUnban' . $zeile["uid"] . '" name="playerUnban" value=' . $zeile['uid'] . '> 
																			<button class="btn btn-primary" type="submit" id="playerUnbanSubmitID" name="playerUnbanSubmit">Bestätigen</button>
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
							</section>
					<!-- end: page -->
				</section>
			</div>
						
		</section>

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
        
		
		<!-- Theme Base, Components and Settings -->
		<script src="js/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="js/custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="js/theme.init.js"></script>

		<!-- Examples -->
        <script src="js/examples/examples.modals.js"></script>	

		<script>
			$(document).ready(function(){
				$('#datatable-noAjaxajax3').dataTable({
            	"pageLength": 100,
				"lengthMenu": [[25, 50, 75, 100, 250, 500], [25, 50, 75, 100, 250, 500]],
				'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'
				});
			});
		</script>


	</body>
</html>