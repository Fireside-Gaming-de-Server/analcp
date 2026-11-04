<?php 
    include 'includes/checkOnline.php';
    include 'includes/mysql.php';
	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $_COOKIE["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	$groupID = $row[5];

	$permission_name = "devToDo";
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
    

    if (isset($_POST['newConfirm'])) {
		$stmt = $dbl->prepare("INSERT INTO acp_dev_todo (user, text, work_area, status ) VALUES (?,?,?,?)");
		$stmt->bind_param("ssss", $_COOKIE['username'], $_POST['newText'], $_POST['newWorkArea'],$_POST['newStatus']);
		$stmt->execute(); 
	}

	if (isset($_POST['deleteConfirm'])) {
		$stmt = $dbl->prepare("DELETE FROM acp_dev_todo WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDdelete']);
		$stmt->execute();	  
	}

	if (isset($_POST['ChangeConfirm'])) {
          
		$update = $dbl->prepare("UPDATE acp_dev_todo SET user=?, text=?, work_area=?, status=? WHERE id=?");
		$update->bind_param("ssssi", $_COOKIE['username'], $_POST['changeText'], $_POST['changeWorkArea'],$_POST['changeStatus'], $_POST['zeilenIDChange'] );
		$update->execute();
    }
    
    if (isset($_POST['deleteConfirmPatchnotes'])) {
		$stmt = $dbl->prepare("DELETE FROM acp_dev_todo WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDdeletePatchnotes']);
		$stmt->execute();	  
    }
    
    if (isset($_POST['deleteConfirmFertig'])) {
		$stmt = $dbl->prepare("DELETE FROM acp_dev_todo WHERE id=?");
		$stmt->bind_param("i", $_POST['zeilenIDdeleteFertig']);
		$stmt->execute();	  
	}
    

?>


<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Advanced Tables | Porto Admin - Responsive HTML5 Template 2.3.0</title>
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
						<h2>ToDo  -  Liste</h2>
					
					</header>

						<!-- start: page -->
					
						<div class="row">
							<div class="col">
								<section class="card">
									<header class="card-header">
										<div class="card-actions">
											<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
										</div> 
						
										<h2 class="card-title">ToDo  -  Liste</h2>
									</header>
										<div class="card-body">
                                            <div class="form-group">
													<a class="modal-with-form btn btn-default" href="#toDoAdd">+ Neuen Eintrag</a>
											</div> 
                                            <div id="toDoAdd" class="modal-block modal-block-primary mfp-hide">
                                                <section class="card">
                                                    <form class="form-horizontal form-bordered" method="post">
                                                        <header class="card-header">
                                                            <h2 class="card-title">Neuen Eintrag</h2>
                                                        </header>
                                                            <div class="card-body">
                                                                <div class="form-group row">
                                                                    <div class="col-sm-12 mb-6">
                                                                        <label>ToDo  -  Text</label> 
                                                                        <input name="newText" id="newText" type="text" class="form-control form-control-lg" />
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-sm-6 mb-3">
                                                                        <label>Status</label> 
                                                                        <select data-plugin-selectTwo class="form-control populate placeholder" id="newStatus" name="newStatus">
                                                                            <optgroup label="Status">
                                                                                <option value="Zukunftsgedanken">Zukunftsgedanken</option>
                                                                                <option value="Nix">Nix</option>
                                                                                <option value="Wird bearbeitet">Wird bearbeitet</option>
                                                                                <option value="Muss getestet werden">Muss getestet werden</option>
                                                                                <option value="Wird getestet">Wird getestet</option>
                                                                                <option value="Fertig">Fertig</option>
                                                                                <option value="FertigPatchnotes">FertigPatchnotes</option>
                                                                            </optgroup>  
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-sm-6 mb-3">
                                                                        <label>Arbeitsbereich</label>
                                                                        <select data-plugin-selectTwo class="form-control populate placeholder" id="newWorkArea" name="newWorkArea">
                                                                            <optgroup label="Arbeitsbereich">
                                                                                <option value="Arma Server">Arma Server</option>
                                                                                <option value="Arma Bug">Arma Bug</option>
                                                                                <option value="Arma Bug dringend">Arma Bug dringend</option>
                                                                                <option value="Forum">Forum</option>
                                                                                <option value="ACP">ACP</option>
                                                                                <option value="Teamspeak">Teamspeak</option>
                                                                            </optgroup>  
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </body>
                                                        <footer class="card-footer">
                                                            <div class="form-group">
                                                                <div class="row">
                                                                    <div class="col-md-12 text-right">
                                                                        <button type="submit" id="newConfirm" name="newConfirm" class="btn btn-default">Push It</button>
                                                                        <button class="btn btn-default modal-dismiss">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div> 
                                                        </footer>
                                                    </form>	
                                                </section>
                                            </div>	
										
											<table class="table table-responsive-md table-bordered mb-0" id="datatable-default1">
												<thead>
													<tr>
                                                       <th>Text</th>
                                                        <th>User</th>
                                                        <th>work-Area</th>
														<th>Status</th>
                                                        <th>Edit</th>
                                                        <th>Löschen</th>
													</tr>
												</thead>
												<tbody>
                                                    <?php
													    $query = "SELECT * FROM acp_dev_todo";
														
														$db_erg = mysqli_query( $dbl, $query );
														if ( ! $db_erg )
														{
														die('Ungültige Abfrage: ' . mysqli_error());
														}								
														while ($zeile = mysqli_fetch_array( $db_erg, MYSQLI_ASSOC))
													    {
                                                        
                                                        if (($zeile['status'] == "FertigPatchnotes") or ($zeile['status'] == "Fertig"))   {

                                                        }  else {

                                                            echo "<tr>";
                                                            echo "<td>" . $zeile['text'] . "</td>";
                                                            echo "<td>" . $zeile['user'] . "</td>";
                                                            echo "<td>" . $zeile['work_area'] . "</td>";
                                                            echo "<td>" . $zeile['status'] . "</td>";
                                                            echo '
                                                            <td>

                                                                <a class="modal-with-form mb-1 mt-1 mr-1 btn btn-xs btn-default" href="#changeModal'.$zeile['id'].'"><i class="fas fa-pencil-alt"></i>   Bearbeiten</a>	

                                                                <div id="changeModal'.$zeile['id'].'" class="modal-block modal-block-primary mfp-hide">
                                                                    <section class="card">
                                                                        <form class="form-horizontal form-bordered" method="post">
                                                                            <header class="card-header">
                                                                                <h2 class="card-title">ToDo bearbeiten</h2>
                                                                            </header>
                                                                                <div class="card-body">
                                                                                    <div class="form-group row">
                                                                                        <div class="col-sm-12 mb-6">
                                                                                            <label>ToDo  -  Text</label> 
                                                                                            <input name="changeText" id="changeText'.$zeile['id'].'" type="text" value="'.$zeile['text'].'" class="form-control form-control-lg" />
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group row">
                                                                                        <div class="col-sm-6 mb-3">
                                                                                            <label>Status</label> 
                                                                                            <select data-plugin-selectTwo class="form-control populate placeholder" id="changeStatus'.$zeile['id'].'" name="changeStatus">
                                                                                                <optgroup label="Status Derzeit">
                                                                                                    <option value="'.$zeile['status'].'"selected>'.$zeile['status'].'</option>
                                                                                                </optgroup>
                                                                                                <optgroup label="Status ändern">
                                                                                                    <option value="Zukunftsgedanken">Zukunftsgedanken</option>
                                                                                                    <option value="Nix">Nix</option>
                                                                                                    <option value="Wird bearbeitet">Wird bearbeitet</option>
                                                                                                    <option value="Muss getestet werden">Muss getestet werden</option>
                                                                                                    <option value="Wird getestet">Wird getestet</option>
                                                                                                    <option value="Fertig">Fertig</option>
                                                                                                    <option value="FertigPatchnotes">FertigPatchnotes</option>
                                                                                                </optgroup>  
                                                                                            </select>
                                                                                        </div>
                                                                                        <div class="col-sm-6 mb-3">
                                                                                            <label>Arbeitsbereich</label>
                                                                                            <select data-plugin-selectTwo class="form-control populate placeholder" id="changeWorkArea'.$zeile['id'].'" name="changeWorkArea">
                                                                                                <optgroup label="Arbeitsbereich Derzeit">
                                                                                                    <option value="'.$zeile['work_area'].'"selected>'.$zeile['work_area'].'</option>
                                                                                                </optgroup>
                                                                                                <optgroup label="Arbeitsbereich ändern">
                                                                                                    <option value="Arma Server">Arma Server</option>
                                                                                                    <option value="Arma Bug">Arma Bug</option>
                                                                                                    <option value="Arma Bug dringend">Arma Bug dringend</option>
                                                                                                    <option value="Forum">Forum</option>
                                                                                                    <option value="ACP">ACP</option>
                                                                                                    <option value="Teamspeak">Teamspeak</option>
                                                                                                </optgroup>  
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </body>
                                                                            <footer class="card-footer">
                                                                                <div class="form-group">
                                                                                    <div class="row">
                                                                                        <div class="col-md-12 text-right">
                                                                                            <input type="hidden" id="zeilenIDChange" name="zeilenIDChange" value='. $zeile['id'] .'> 
                                                                                            <button type="submit" id="ChangeConfirm" name="ChangeConfirm" class="btn btn-default">Senden</button>
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
                                                                                    <p>Du löschst gleich folgenden Beitrag: </p>
                                                                                    <p>"'. $zeile['text'] .'" </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <form method="post">
                                                                                        <input type="hidden" id="zeilenIDdelete" name="zeilenIDdelete" value='. $zeile['id'] .'> 
                                                                                        <button type="submit" id="deleteConfirm" name="deleteConfirm" class="btn btn-primary">Jupp Löschen</button>
                                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </td>';		
                                                            echo "</tr>";
                                                            }
                                                        }
							
														mysqli_free_result( $db_erg );
														
													?>
												</tbody>
											</table>
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
						
										<h2 class="card-title">Fertig - Patchnotes</h2>
									</header>
										<div class="card-body">
                                            
											<table class="table table-responsive-md table-bordered mb-0" id="datatable-default2">
												<thead>
													<tr>
                                                       <th>Text</th>
                                                        <th>User</th>
                                                        <th>work-Area</th>
														<th>Status</th>
                                                        <th>Löschen</th>
													</tr>
												</thead>
												<tbody>
                                                    <?php
													    $query = "SELECT * FROM acp_dev_todo";
														
														$db_erg = mysqli_query( $dbl, $query );
														if ( ! $db_erg )
														{
														die('Ungültige Abfrage: ' . mysqli_error());
														}								
														while ($zeile = mysqli_fetch_array( $db_erg, MYSQLI_ASSOC))
													    {
                                                        
                                                        if ($zeile['status'] == "FertigPatchnotes")   {
                                                            
                                                            echo "<tr>";
                                                            echo "<td>" . $zeile['text'] . "</td>";
                                                            echo "<td>" . $zeile['user'] . "</td>";
                                                            echo "<td>" . $zeile['work_area'] . "</td>";
                                                            echo "<td>" . $zeile['status'] . "</td>";
                                                            echo '
                                                            <td>
                                                                <a class="mb-1 mt-1 mr-1 btn btn-xs btn-default" data-toggle="modal" data-target="#deleteModalPatchnotes'.$zeile['id'].'" href="#"><i class="fas fa-trash-alt"></i>   Löschen</a>
                                                        
                                                                <div class="modal" id="deleteModalPatchnotes'.$zeile['id'].'" tabindex="-1" role="dialog">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Bist Du Dir Sicher?</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Du löschst gleich folgenden Beitrag: </p>
                                                                                <p>"'. $zeile['text'] .'" </p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form method="post">
                                                                                    <input type="hidden" id="zeilenIDdeletePatchnotes" name="zeilenIDdeletePatchnotes" value='. $zeile['id'] .'> 
                                                                                    <button type="submit" id="deleteConfirmPatchnotes" name="deleteConfirmPatchnotes" class="btn btn-primary">Jupp Löschen</button>
                                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>';		
                                                            echo "</tr>";
                                                        }  else {

                                                            }
                                                        }
							
														mysqli_free_result( $db_erg );
														
													?>
												</tbody>
											</table>
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
						
										<h2 class="card-title">Fertig - Sonstiges</h2>
									</header>
										<div class="card-body">
                                            
											<table class="table table-responsive-md table-bordered mb-0" id="datatable-default3">
												<thead>
													<tr>
                                                       <th>Text</th>
                                                        <th>User</th>
                                                        <th>work-Area</th>
														<th>Status</th>
                                                        <th>Löschen</th>
													</tr>
												</thead>
												<tbody>
                                                    <?php
													    $query = "SELECT * FROM acp_dev_todo";
														
														$db_erg = mysqli_query( $dbl, $query );
														if ( ! $db_erg )
														{
														die('Ungültige Abfrage: ' . mysqli_error());
														}								
														while ($zeile = mysqli_fetch_array( $db_erg, MYSQLI_ASSOC))
													    {
                                                        
                                                        if ($zeile['status'] == "Fertig")   {
                                                            
                                                            echo "<tr>";
                                                            echo "<td>" . $zeile['text'] . "</td>";
                                                            echo "<td>" . $zeile['user'] . "</td>";
                                                            echo "<td>" . $zeile['work_area'] . "</td>";
                                                            echo "<td>" . $zeile['status'] . "</td>";
                                                            echo '
                                                            <td>
                                                                <a class="mb-1 mt-1 mr-1 btn btn-xs btn-default" data-toggle="modal" data-target="#deleteModalFertig'.$zeile['id'].'" href="#"><i class="fas fa-trash-alt"></i>   Löschen</a>
                                                        
                                                                <div class="modal" id="deleteModalFertig'.$zeile['id'].'" tabindex="-1" role="dialog">
                                                                    <div class="modal-dialog" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title">Bist Du Dir Sicher?</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Du löschst gleich folgenden Beitrag: </p>
                                                                                <p>"'. $zeile['text'] .'" </p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <form method="post">
                                                                                    <input type="hidden" id="zeilenIDdeleteFertig" name="zeilenIDdeleteFertig" value='. $zeile['id'] .'> 
                                                                                    <button type="submit" id="deleteConfirmFertig" name="deleteConfirmFertig" class="btn btn-primary">Jupp Löschen</button>
                                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>';		
                                                            echo "</tr>";
                                                        }  else {

                                                            }
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
		<script src="js/examples/examples.datatables.defaultDevToDo.js"></script>
		<script src="js/examples/examples.datatables.row.with.details.js"></script>
		<script src="js/examples/examples.datatables.tabletools.js"></script>
		<script src="js/examples/examples.advanced.form.js"></script>


	</body>
</html>