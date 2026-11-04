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

	$permission_name = "dbDoubleAcc";
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
	$LogText = '['.$LogSupporterGroup.'] '.$LogsupporterName.' hat sich die Seite "Datenbank DoppelAccount" angeschaut.';
	log_acp($LogCategory,$LogText,$LogSupporterPid,$LogsupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);


?>


<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Datenbank - DoubleAcc</title>
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
						<h2>Datenbank - DoubleAcc</h2>
					
					</header>

						<!-- start: page -->
							<section class="card">
								<header class="card-header">
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
									</div>
							
									<h2 class="card-title">Datenbank - DoubleAcc</h2>
								</header>
								<div class="card-body">
								    <form class="" method="post">
										<table class="table table-bordered table-striped" id="datatable-ajax" data-url="">
											<thead>
												<tr>
                                                <th width="10%">#MainAcc</th> 
                                                <th width="90%">DoubleAccs</th>
												</tr>
											</thead>
											<tbody>
                                                <?php
                                                
                                                    $query = 'SELECT playerid from players';
                                                            
                                                    $db_erg = mysqli_query( $dbpc, $query );
                                                    if ( ! $db_erg )
                                                    {
                                                    die('Ungültige Abfrage: ' . mysqli_error());
                                                    }								
                                                    while ($zeile = mysqli_fetch_array( $db_erg, MYSQLI_ASSOC))
                                                    {
                                                    
                                                        $rowArr = json_decode($zeile['playerid']);
                                                        
                                                        if (count($rowArr) >= 2) {
                                                            $mainAccCounter = 0;
                                                            $secondAccCounter = 1;
                                                            $endCounter = count($rowArr);
                                                            $secondAccZeile = "";

                                                            echo "<tr>";
                                                            foreach ($rowArr as $key => $value) {
                                                                if ($mainAccCounter < 1) {
                                                                    echo '<td> <a class="btn btn-xs btn-default" href="playerShow.php?id='.$value.'" >'.$value.'</a> </td>';
                                                                    $mainAccCounter = $mainAccCounter + 1;
                                                                } else {
                                                                    if ($secondAccCounter <= $endCounter) {
                                                                        $secondAccZeile = ''.$secondAccZeile.'<a class="btn btn-xs btn-default" href="playerShow.php?id='.$value.'" >'.$value.'</a> ';     
                                                                    }
                                                                    $secondAccCounter = $secondAccCounter + 1;
                                                                }
                                                            }
                                                            echo '<td>'.$secondAccZeile.'</td>';     
                                                            echo "</tr>";
                                                            
                                                        }
                                                    }
                        
                                                    mysqli_free_result( $db_erg );
                                                    
                                                ?>
											</tbody>
										</table>
									</form>	
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

		<script>
			$(document).ready(function(){
				$('#datatable-ajax').dataTable({
				"pageLength": 100,
				"lengthMenu": [[25, 50, 75, 100, 250, 500], [25, 50, 75, 100, 250, 500]],
				'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'
				});
			});
		</script>
