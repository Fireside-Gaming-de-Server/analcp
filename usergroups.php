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

$permission_name = "adminUserGroups";
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
//groupCheckEnd
//LOg
$LogCategory = "SiteActivity";
$LogText = '[ ' . $LogSupporterGroup . ' ] ' . $LogsupporterName . ' hat sich die Seite: Usergrupppen angesehen';
log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
//LOg

if (isset($_POST['groupAddSubmit'])) {

	$stmt = $dbl->prepare("INSERT INTO acp_usergroups (group_name, sort_id) VALUES (?,?)");
	$stmt->bind_param("si", $_POST['addusergroupName'], $_POST['addusergroupSortID']);
	$stmt->execute();
	//log
	$LogCategory = "changeAcpGroup";
	$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat eine neue Gruppe mit dem Namen: "' . $_POST['addusergroupName'] . '" und der SortID: "' . $_POST['addusergroupSortID'] . '" erstellt.';
	log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
	header("Refresh:0");
}

if (isset($_POST['deleteConfirm'])) {
	if ($_POST['zeilenIDdelete'] === 65) {
	} else {
		$stmt = $dbl->prepare("SELECT * FROM acp_usergroups WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDdelete']);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_row();
		$nameGroup = $row[1];

		$LogCategory = "changeAcpGroup";
		$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat die Gruppe mit dem Namen: "' . $nameGroup . '" und der ID: "' . $_POST['zeilenIDdelete'] . '" gelöscht.';
		log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
		//logEnd
		$stmt = $dbl->prepare("DELETE FROM acp_usergroups WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDdelete']);
		$stmt->execute();

		$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE userGroupID=?");
		$stmt->bind_param("i", $_POST['zeilenIDdelete']);
		$stmt->execute();
		$result = $stmt->get_result();
		while ($zeile = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

			$stmt = $dbl->prepare("UPDATE acp_users SET userGroupID=65 WHERE id=?");
			$stmt->bind_param("i", $zeile['id']);
			$stmt->execute();
		}
	}

	header("Refresh:0");

	//delete aus allen permission_arrays muss noch hinzugefügt werden
}


//rights arrays machen
$headerArr = array();
$rechteArray = array();

$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE header != 'Items'");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	if ((in_array($row['header'], $headerArr)) === false) {
		$headerArr[] = $row['header'];
	}

	$array = array(
		$row['permission_name'] => array(
			"rights" => (json_decode($row['group_ids'])),
			"description" => $row['description']
		)
	);
	$rechteArray[$row['header']][] = $array;
}

if (isset($_POST['rightsEditSubmit'])) {
	//rankCheck


	$LogCategory = "changeAcpGroup";
	$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat die Gruppe mit der ID: "' . $groupID . '" bearbeitet.';
	log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
	//log///

	//toDo
	$group_id = $_POST['rightsEditSubmit'];
	foreach ($rechteArray as $key => $value) {
		foreach ($value as $key2 => $value2) {
			foreach ($value2 as $key3 => $value3) {

				if (isset($_POST['checkbox_' . $key3])) {
					$arr_rights = $value3['rights'];
					if ((in_array($group_id, $arr_rights)) === false) {
						$arr_rights[] = intval($group_id);

						$rights_str = json_encode($arr_rights);

						$stmt = $dbl->prepare("UPDATE acp_permissions SET `group_ids` = ? WHERE `permission_name` = ?");
						$stmt->bind_param("ss", $rights_str, $key3);
						$stmt->execute();
					}
				} else {

					$arr_rights = $value3['rights'];
					if ((in_array($group_id, $arr_rights)) === true) {

						$index = array_search($group_id, $arr_rights);
						unset($arr_rights[$index]);
						array_unshift($arr_rights);
						$rights_str = json_encode($arr_rights);

						$stmt = $dbl->prepare("UPDATE acp_permissions SET `group_ids` = ? WHERE `permission_name` = ?");
						$stmt->bind_param("ss", $rights_str, $key3);
						$stmt->execute();
					}
				}
			}
		}
	}

	header("Refresh:0");
}


if (isset($_POST["GroupChangeSubmit"])) {


	$id = $_POST["GroupChangeSubmitID"];
	$sort_id =  $_POST['changeusergroupSortID'];
	$group_name = $_POST['changeusergroupName'];

	$stmt = $dbl->prepare("UPDATE acp_usergroups SET group_name=?, sort_id=? WHERE id=?");
	$stmt->bind_param("sss", $group_name, $sort_id, $id);
	$stmt->execute();

	$LogCategory = "changeAcpGroup";
	$LogText = '[' . $LogSupporterGroup . '] ' . $LogsupporterName . ' hat die Gruppe mit der ID: "' . $groupID . '" bearbeitet.';
	log_acp($LogCategory, $LogText, $LogSupporterPid, $LogsupporterName, $LogSupporterGroup, $LogUserPid, $LogUserName);
	//log///
	header("Refresh:0");
}

?>


<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">

<head>

	<!-- Basic -->
	<meta charset="UTF-8">

	<title>UserGroups</title>
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

		<?php
		include 'includes/header.php';
		?>

		<div class="inner-wrapper">

			<?php
			include 'includes/sideBar.php';
			?>

			<section role="main" class="content-body">
				<header class="page-header">
					<h2>User-Gruppen</h2>

				</header>

				<!-- start: page -->

				<div class="row">
					<div class="col">
						<section class="card">
							<header class="card-header">
								<div class="card-actions">
									<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
								</div>

								<h2 class="card-title">User - Gruppen</h2>
							</header>
							<div class="card-body">

								<div class="form-group">
									<a class="modal-with-form btn btn-default" href="#groupAdd">+ Neue Gruppe</a>
								</div>

								<div id="groupAdd" class="modal-block modal-block-primary mfp-hide">
									<section class="card">
										<form class="form-horizontal form-bordered" action="usergroups.php" method="post">
											<header class="card-header">
												<h2 class="card-title">Neue Gruppe</h2>
											</header>
											<div class="card-body">
												<form class="form-horizontal form-bordered" action="#">
													<div class="form-group row">
														<div class="col-sm-8 mb-4">
															<label>Gruppen-Name</label>
															<input name="addusergroupName" type="text" class="form-control form-control-lg" />
														</div>
														<div class="col-sm-4 mb-2">
															<div data-plugin-spinner data-plugin-options='{ "value":1, "min": 1, "max": 99 }'>
																<label>Sort-ID</label>
																<div class="input-group">
																	<input name="addusergroupSortID" type="text" class="spinner-input form-control form-control-lg" maxlength="3" readonly>
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
</body>
<footer class="card-footer">
	<div class="form-group">
		<div class="row">
			<div class="col-md-12 text-right">
				<button type="submit" id="groupAddSubmit" name="groupAddSubmit" class="btn btn-default">Senden</button>
				<button class="btn btn-default modal-dismiss">Cancel</button>
			</div>
		</div>
	</div>
</footer>
</form>
</section>
</div>

<table class="table table-responsive-md table-bordered mb-0" id="datatable-default">
	<thead>
		<tr>
			<th>Sort ID</th>
			<th>Gruppen-Name</th>
			<th>ID</th>
			<th>Rang Editieren</th>
			<th>Rechte Editieren</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$query = "SELECT * FROM acp_usergroups";

		$db_erg = mysqli_query($dbl, $query);
		if (!$db_erg) {
			die('Ungültige Abfrage: ' . mysqli_error());
		}
		while ($zeile = mysqli_fetch_array($db_erg, MYSQLI_ASSOC)) {
			$groupID = $zeile['id'];

			foreach ($permissionArr as $value) {

				$permission_name = $value;
				$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
				$stmt->bind_param("s", $permission_name);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_row();
				$groupID_check = $row[2];
				$groupID_check = json_decode($groupID_check);

				if (in_array($groupID, $groupID_check)) {
					${'checkbox_' . $value} = "checked";
				} else {
					${'checkbox_' . $value} = "";
				}
			}

			echo "<tr>";
			echo "<td>" . $zeile['sort_id'] . "</td>";
			echo "<td>" . $zeile['group_name'] . "</td>";
			echo "<td>" . $zeile['id'] . "</td>";
			echo '
                                                        <td>

                                                            <a class="modal-with-form mb-1 mt-1 mr-1 btn btn-xs btn-default" href="#groupChangeModal' . $zeile['id'] . '"><i class="fas fa-pencil-alt"></i>   Bearbeiten</a>

                                                            <div id="groupChangeModal' . $zeile['id'] . '" class="modal-block modal-block-primary mfp-hide">
															<section class="card">
															<form class="form-horizontal form-bordered" action="usergroups.php" method="post">
																<header class="card-header">
																	<h2 class="card-title">Gruppe Bearbeiten</h2>
																</header>
																<div class="card-body">
																	<form class="form-horizontal form-bordered" action="#">
																		<div class="form-group row">
																			<div class="col-sm-8 mb-4">
																				<label>Gruppen-Name</label>
																				<input type="hidden" id="GroupChangeSubmitID' . $zeile['id'] . '" name="GroupChangeSubmitID" value=' . $zeile['id'] . '>
																				<input name="changeusergroupName" id="changeusergroupName' . $zeile['id'] . '" value="' . $zeile["group_name"] . '" type="text" class="form-control form-control-lg" />
																			</div>
																			<div class="col-sm-4 mb-2">
																				<div data-plugin-spinner data-plugin-options=' . '{ "value":1, "min": 1, "max": 99 }' . '>
																					<label>Sort-ID</label>
																					<div class="input-group">
																						<input name="changeusergroupSortID" value="' . $zeile["sort_id"] . '" type="text" class="spinner-input form-control form-control-lg" maxlength="3" readonly>
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
					</body>
					<footer class="card-footer">
						<div class="form-group">
							<div class="row">
								<div class="col-md-12 text-right">
									<button type="submit" id="GroupChangeSubmit" value="' . $zeile["id"] . '" name="GroupChangeSubmit" class="btn btn-default">Senden</button>
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

                                                            <a class="modal-with-form mb-1 mt-1 mr-1 btn btn-xs btn-default" href="#rightsEditModal' . $zeile['id'] . '"><i class="fas fa-pencil-alt"></i>   Rechte einstellen</a>

                                                            <div id="rightsEditModal' . $zeile['id'] . '" class="modal-block modal-block-full mfp-hide">
                                                                <section class="card">
                                                                    <form class="form-horizontal form-bordered" method="post">
                                                                        <header class="card-header">
                                                                            <h2 class="card-title">Rechte einstellen [' . $zeile['group_name'] . ']</h2>
                                                                        </header>
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-sm-12 mb-12">
																						<div class="col">
																							<div class="tabs tabs-dark">
																								<ul class="nav nav-tabs">
																									';
			$counter1 = 0;
			foreach ($headerArr as $key => $value) {
				if ($counter1 === 0) {
					$counter1 = $counter1 + 1;
					echo '
																												<li class="nav-item active">
																													<a class="nav-link" href="#' . $value . '' . $zeile['id'] . '" data-toggle="tab">' . $value . '</a>
																												</li>
																												';
				} else {
					echo '
																												<li class="nav-item">
																													<a class="nav-link" href="#' . $value . '' . $zeile['id'] . '" data-toggle="tab">' . $value . '</a>
																												</li>
																												';
				}
			}

			echo '
																								</ul>
																								<div class="tab-content">
																							';
			$counter2 = 0;
			foreach ($rechteArray as $key => $value) {
				if ($counter2 === 0) {
					echo '
																									<div id="' . $key . '' . $zeile['id'] . '" class="tab-pane active">';
				} else {
					echo '</div>
																									<div id="' . $key . '' . $zeile['id'] . '" class="tab-pane">';
				}

				$counter2 = $counter2 + 1;
				foreach ($value as $key2 => $value2) {
					foreach ($value2 as $key3 => $value3) {


						$checkbox = '';
						if ((in_array($zeile['id'], $value3['rights'])) === true) {
							$checkbox = 'checked="checked"';
						}
						echo '

																											<div class="input-group mb-3">
																												<span class="input-group-prepend">
																													<span class="input-group-text">
																													<div class="switch switch-sm switch-primary">
																														<input name="checkbox_' . $key3 . '" type="checkbox" data-plugin-ios-switch ' . $checkbox . ' />
																													</div>
																													</span>
																												</span>
																												<input class="form-control" id="inputDisabled" type="text" placeholder="' . $value3['description'] . '" disabled="">
																											</div>

																										';
					}
				}
			}


			echo '

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
																						<button type="submit" id="rightsEditSubmit' . $zeile['id'] . '" name="rightsEditSubmit" value="' . $zeile['id'] . '" class="btn btn-default">Senden</button>
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


																<a class="mb-1 mt-1 mr-1 btn btn-xs btn-default" data-toggle="modal" data-target="#deleteModal' . $zeile['id'] . '" href="#"><i class="fas fa-trash-alt"></i>   Löschen</a>

																<div class="modal" id="deleteModal' . $zeile['id'] . '" tabindex="-1" role="dialog">
																	<div class="modal-dialog" role="document">
																		<div class="modal-content">
																			<div class="modal-header">
																				<h5 class="modal-title">Bist Du Dir sicher?!</h5>
																				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																					<span aria-hidden="true">&times;</span>
																				</button>
																			</div>
																			<div class="modal-body">
																				<p>Mövhtest Du die Gruppe "' . $zeile['group_name'] . '" wirklkiich löschen?!</p>
																			</div>
																			<div class="modal-footer">
																				<form method="post">
																					<input type="hidden" id="zeilenIDdelete' . $zeile['id'] . '" name="zeilenIDdelete" value=' . $zeile['id'] . '>
																					<button type="submit" id="deleteConfirm' . $zeile['id'] . '" name="deleteConfirm" class="btn btn-primary">Confirm</button>
																					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
																				</form>
																			</div>
																		</div>
																	</div>
																</div>
														</td>';
			echo "</tr>";
		}

		mysqli_free_result($db_erg);

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

<script>
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>

</body>

</html>