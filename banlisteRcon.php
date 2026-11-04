<?php 
	include 'includes/checkOnline.php';
	$LogSupporterPid = $row[7];
	$LogsupporterName = $row[1];
	$LogSupporterGroup = $userGroupname;
	$LogUserPid = "-";
	$LogUserName = "-";

	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $_COOKIE["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	$groupID = $row[5];

	$permission_name = "visitBanRcon";
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
       
	$LogCategory = "SiteActivity";
	$LogText = '['.$LogSupporterGroup.'] '.$LogsupporterName.' hat sich die Seite "Banliste - RCON" angeschaut.';
	log_acp($LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);

    $permission_name = "editBanRcon";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
    $stmt->bind_param("s", $permission_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    $groupID_check = $row[2];
    $groupID_check = json_decode($groupID_check);

    $editBanRcon = false;
    if (in_array($groupID, $groupID_check)) {
		$editBanRcon = true;
	}

    if (isset($_POST['unbanPlayerSubmit'])) {
        if ($editBanRcon) {
            $stmt = $dbs->prepare("DELETE FROM bans WHERE id = ?");
            $stmt->bind_param("s", $_POST['unbanPlayerBanID']);
            $stmt->execute();
            
            //log
            $LogUserPid = $_POST['unbanPlayerBanPID'];
            $LogCategory = "kickBan";
            $LogText = '['.$LogSupporterGroup.'] '.$LogsupporterName.' hat den RCON - Ban vom Spieler PID:'.$_POST['unbanPlayerBanPID'].' GUID:'.$_POST['unbanPlayerBanGUID'].' gelöscht. Regulär ausgelaufen in: '.$_POST['unbanPlayerRestZeit'].' Minuten';
           
			$editUnbanRconNote = $_POST["editUnbanRconNote"];
			$stmt = $dbl->prepare("INSERT INTO acp_logs (category, text, supporter_pid, supporter_name, supporter_group, user_pid, user_name, short_note) VALUES (?,?,?,?,?,?,?,?)");
			$stmt->bind_param("ssssssss", $LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName,$editUnbanRconNote);
			$stmt->execute();

            header("Refresh:0");
        }
	}
     


?>
<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Banliste - RCON</title>
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
						<h2>Banliste - RCON</h2>
					
					</header>

						<!-- start: page -->
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
									</div>
							
									<h2 class="card-title">Banliste - RCON</h2>
								</header>
								<div class="card-body">
                                    <table class="table table-bordered table-striped" id="datatable-noAjaxajax3">
                                        <thead>
                                            <tr>
                                            <th width="5%">ID</th>
                                            <th width="7%">pid</th>
                                            <th width="5%">GUID</th>
                                            <th width="45%">Reason</th>
                                            <th width="14%">Banned_From</th>
                                            <th width="14%">Bandauer</th>
                                            <th width="10%">Unban</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php   
                                            $stmt = $dbs->prepare("SELECT id, pid, guid, reason, creator_name, TIMESTAMPDIFF(Minute,current_timestamp(),`expiration_date`) AS ban_end FROM bans WHERE NOW() < expiration_date");
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            while ($zeile = mysqli_fetch_array( $result, MYSQLI_ASSOC))
                                            {
                                                $time = $zeile['ban_end'];
                                                if ($time > 60) {
                                                    $time = $time / 60;
                                                    $time = round($time,0);
                                                    $time = ''.$time.' hours';
                                                } else {
                                                    $time = ''.$time.' minutes';
                                                }
    
                                                echo "<tr>";
                                                echo "<td>" . $zeile["id"] . "</td>";
                                                echo '<td> <a class="btn btn-xs btn-default" href="playerShow.php?id='.$zeile['pid'].'" >'.$zeile['pid'].'</a> </td>';
                                                echo "<td>" . $zeile["guid"]. "</td>";
                                                echo "<td>" . $zeile["reason"]. "</td>";
                                                echo "<td>" . $zeile["creator_name"]. "</td>";
                                                echo "<td>" . $time . "</td>";

                                                if ($editBanRcon) {  echo '<td>

                                                    <a class="modal-with-form btn btn-xs btn-default" href="#unbanPlayer'.$zeile['id'].'"><i class="fas fa-trash-alt"></i>   Löschen</a>
                                                    
                                                    <div id="unbanPlayer'.$zeile['id'].'" class="modal-block modal-block-primary mfp-hide">
                                                        <section class="card">
															<form method="post"> 
                                                                <div class="card-body text-center">
                                                                    <div class="modal-wrapper">
                                                                        <div class="modal-icon center">
                                                                            <i class="fas fa-question-circle"></i>
                                                                        </div>
                                                                        <div class="modal-text">
                                                                            <h4>Bist Du Dir sicher?</h4>
                                                                            <p>Mit bestätigen Löscht Du den Ban von '.$zeile['pid'].' mit der GUID '.$zeile['guid'].' Restzeit: '.$time.'</p>
                                                                        </div>
																		<div class="form-group">
																			<label class="control-label text-lg-right pt-2" for="editUnbanRconNoteID">Short Note</label>
																			<input class="form-control" name="editUnbanRconNote" id="editUnbanRconNoteID" type="text" value="Kurze Notiz">
																		</div> 
                                                                    </div>
                                                                </div>
                                                                <footer class="card-footer">
																	<div class="row">
																		<div class="col-md-12 text-right">
																			<input type="hidden" id="unbanPlayer'.$zeile["id"].'" name="unbanPlayerBanPID" value='. $zeile['pid'] .'> 
																			<input type="hidden" id="unbanPlayerID'.$zeile["id"].'" name="unbanPlayerBanID" value='. $zeile['id'] .'> 
																			<input type="hidden" id="unbanPlayerGUID'.$zeile["id"].'" name="unbanPlayerBanGUID" value='. $zeile['guid'] .'> 
																			<input type="hidden" id="unbanPlayerRestZeit'.$zeile["id"].'" name="unbanPlayerRestZeit" value='. $zeile['ban_end'] .'> 
																			<button class="btn btn-primary" type="submit" id="unbanPlayer'.$zeile["id"].'" name="unbanPlayerSubmit">Bestätigen</button>
																			<button class="btn btn-default modal-dismiss">Cancel</button> 
																		</div>
																	</div>
                                                                </footer>
                                                            </form>
                                                        </section>
                                                    </div>    

                                                    
                                                    </td>'; }
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