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

   $permission_name = "adminUserlist";
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

   	//LOg
	$LogCategory = "SiteActivity";
	$LogText = '['.$LogSupporterGroup.'] '.$LogsupporterName.' hat sich die Seite: Userliste angesehen';
	log_acp($LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);
	//LOg

	if (isset($_POST['deleteConfirm'])) {
		//Log DeleteUser////
		$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDdelete']);
		$stmt->execute();
		$result = $stmt->get_result();
		$rowDelete = $result->fetch_row();
		$LogUserPid = $rowDelete[7];
		$LogUserName = $rowDelete[1];

		$LogCategory = "changeAcpUser";
		$LogText = '['.$LogSupporterGroup.'] '.$LogsupporterName.' hat den User mit dem Namen "'.$LogUserName.'" und der PID "'.$LogUserPid.'" aus dem ACP gelöscht.';
		log_acp($LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);
		//////////////
		//delteDiese
		$stmt = $dbl->prepare("DELETE FROM acp_users WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDdelete']);
		$stmt->execute();
	}

	if (isset($_POST['userChangeSubmit'])) {
		$stmt2 = $dbl->prepare("SELECT * FROM acp_usergroups WHERE group_name=?");
		$stmt2->bind_param("s", $_POST['userChangeRank']);
		$stmt2->execute();
		$result2 = $stmt2->get_result();
		$row2 = $result2->fetch_row();
		$userChangeSortID = $row2[2];
		$userGroupID = $row2[0];
		////////////Log
		$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDUserChange']);
		$stmt->execute();
		$result = $stmt->get_result();
		$rowDelete = $result->fetch_row();
		$LogUserPid = $rowDelete[7];
		$LogUserName = $rowDelete[1];

		$LogCategory = "changeAcpUser";
		$LogText = '['.$LogSupporterGroup.'] '.$LogsupporterName.' hat den User mit dem Namen "'.$LogUserName.'" und der PID "'.$LogUserPid.'" bearbeitet: Neuer Name:"'.$_POST['changeUserName'].'" Neuer Rang: "'.$_POST['userChangeRank'].'"';
		log_acp($LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);
		///////
		$update = $dbl->prepare("UPDATE acp_users SET username=?, userGroupID=?, sort_id=? WHERE id=?");
		$update->bind_param("siii", $_POST['changeUserName'], $userGroupID, $userChangeSortID, $_POST['zeilenIDUserChange'] );
		$update->execute();
	}

	

?>

<script>
	function copyToClipboard(text) {
		navigator.clipboard.writeText(text)
		alert('Link wurde kopiert: ' + text)
	}
</script>

<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Userliste</title>
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
						<h2>User-Liste</h2>
					
					</header>

						<!-- start: page -->
						<div class="row">
							<div class="col">
								<section class="card">
									<header class="card-header">
										<div class="card-actions">
											<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										</div> 
						
										<h2 class="card-title">Registrierungs-Link</h2>
									</header>
									<div class="card-body">
									<div class="form-group d-flex justify-content-between">
										<input type="text" value="https://acp.fireside-gaming.de/registerSteam.php" id="inputReadOnlynotiz" class="form-control" readonly="readonly">
										<button type="button" onclick="copyToClipboard('https://acp.fireside-gaming.de/registerSteam.php')" class="btn btn-primary">Kopieren</button>
									</div>
									</div>
								</section>
							</div>							
						</div>	
						<div class="row">
							<div class="col">
								<section class="card">
									<header class="card-header">
										<div class="card-actions">
											<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										</div> 
						
										<h2 class="card-title">User - Liste</h2>
									</header>
										<div class="card-body">
										
											<table class="table table-responsive-md table-bordered mb-0" id="datatable-default">
												<thead>
													<tr>
														<th>Sort ID</th>
														<th>ID</th>
                                                        <th>User-Rank</th>
                                                        <th>User-Name</th>
														<th>PID</th>
														<th>Bearbeiten</th>
														<th>Löschen</th>
													</tr>
												</thead>
												<tbody>
                                                    <?php
                                                        $ranks = "";
                                                        $queryRanks = "SELECT * FROM acp_usergroups";
                                                        $db_ergRanks = mysqli_query( $dbl, $queryRanks );
                                                        if ( ! $db_ergRanks )
                                                        {
                                                        die('Ungültige Abfrage: ' . mysqli_error());
                                                        }								
                                                        while ($row = mysqli_fetch_array( $db_ergRanks, MYSQLI_ASSOC))
                                                        {   
                                                            $newGroupName = $row['group_name'];
                                                            $ranks = ''.$ranks.'<option value="'. $newGroupName.'">'.$newGroupName.'</option>';
                                                        }
                                                        mysqli_free_result( $db_ergRanks );

														$query = "SELECT * FROM acp_users";
														
														$db_erg = mysqli_query( $dbl, $query );
														if ( ! $db_erg )
														{
														die('Ungültige Abfrage: ' . mysqli_error());
														}								
														while ($zeile = mysqli_fetch_array( $db_erg, MYSQLI_ASSOC))
													    {
	
														$stmt = $dbl->prepare("SELECT group_name FROM acp_usergroups WHERE id=?");
														$stmt->bind_param("i", $zeile['userGroupID']);
														$stmt->execute();
														$result = $stmt->get_result();
														$groupName = $result->fetch_row();
														$groupName = $groupName[0];		

														echo "<tr>";
														echo "<td>" . $zeile['sort_id'] . "</td>";
														echo "<td>" . $zeile['id'] . "</td>";
                                                        echo "<td>" . $groupName . "</td>";
														echo "<td>" . $zeile['username'] . "</td>";

														
														echo '<td> <a class="btn btn-xs btn-default" href="playerShow.php?id='.$zeile['steam_id'].'" ></i>    '.$zeile['steam_id'].'</a> </td>';
														echo '
														<td>

															<a class="modal-with-form mb-1 mt-1 mr-1 btn btn-xs btn-default" href="#userChangeModal'.$zeile['id'].'"><i class="fas fa-pencil-alt"></i>   Bearbeiten</a>	

															<div id="userChangeModal'.$zeile['id'].'" class="modal-block modal-block-primary mfp-hide">
																<section class="card">
																	<form class="form-horizontal form-bordered" method="post">
																		<header class="card-header">
																			<h2 class="card-title">User bearbeiten</h2>
																		</header>
																			<div class="card-body">
																				<div class="form-group row">
																					<div class="col-sm-12 mb-6">
																						<label>User-Name</label>
																						<input name="changeUserName" id="changeUserName'.$zeile['id'].'" type="text" value="'.$zeile['username'].'" class="form-control form-control-lg" />
																					</div>
                                                                                </div>
                                                                                    <div class="form-group row">
                                                                                       <div class="col-sm-12 mb-6">
                                                                                       <select data-plugin-selectTwo class="form-control populate placeholder" id="userChangeRank'.$groupName.'" name="userChangeRank">
                                                                                                <optgroup label="Rang Derzeit">
                                                                                                    <option value="'.$groupName.'"selected>'.$groupName.'</option>
                                                                                                </optgroup>
                                                                                                <optgroup label="Alle Ränge">
                                                                                                   '.$ranks.'
                                                                                                </optgroup>  
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
																			</body>
																		<footer class="card-footer">
																			<div class="form-group">
																				<div class="row">
																					<div class="col-md-12 text-right">
																						<input type="hidden" id="zeilenIDUserChange" name="zeilenIDUserChange" value='. $zeile['id'] .'> 
																						<button type="submit" id="groupChangeSubmit" name="userChangeSubmit" class="btn btn-default">Senden</button>
																						<button class="btn btn-default modal-dismiss">Cancel</button>
																					</div>
																				</div>
																			</div> 
																		</footer>
																	</form>	
																</section>
															</div>			
														</td>';
														echo '
														<td>
															
														
																<a class="mb-1 mt-1 mr-1 btn btn-xs btn-default" data-toggle="modal" data-target="#deleteModal'.$zeile['id'].'" href="#"><i class="fas fa-trash-alt"></i>   Löschen</a>
														
																<div class="modal" id="deleteModal'.$zeile['id'].'" tabindex="-1" role="dialog">
																	<div class="modal-dialog" role="document">
																		<div class="modal-content">
																			<div class="modal-header">
																				<h5 class="modal-title">Bist Du Dir Sicher?</h5>
																				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																					<span aria-hidden="true">&times;</span>
																				</button>
																			</div>
																			<div class="modal-body">
																				<p>Mövhtest Du den User "'. $zeile['username'] .'" wirklkiich löschen?!</p>
																			</div>
																			<div class="modal-footer">
																				<form method="post">
																					<input type="hidden" id="zeilenIDdelete" name="zeilenIDdelete" value='. $zeile['id'] .'> 
																					<button type="submit" id="deleteConfirm" name="deleteConfirm" class="btn btn-primary">Confirm</button>
																					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																				</form>
																			</div>
																		</div>
																	</div>
																</div>
														</td>';		
														echo "</tr>";
													    }
							
														mysqli_free_result( $db_erg );
														
													?>
												</tbody>
											</table>
									</div>
								</section>
							</div>							
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
		<script src="js/examples/examples.datatables.row.with.details.js"></script>
		<script src="js/examples/examples.datatables.tabletools.js"></script>
		<script src="js/examples/examples.advanced.form.js"></script>


	</body>
</html>