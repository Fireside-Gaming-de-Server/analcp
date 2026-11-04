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

	$permission_name = "createWhitelist";
	$stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	$groupID_check = $row[2];
   	$groupID_check = json_decode($groupID_check);

    $permissionArr = [];
    $permissionArr = ['adminMessageAllPlayers'];
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

	if (in_array($groupID, $groupID_check)) {
		//passt
	} else {
		header("Location: dashboard.php");
		exit();
	}
       
	$LogCategory = "SiteActivity";
	$LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat sich die Seite "adminMessageAll" angeschaut.';
	log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);

    if (isset($_POST["adminMessageAll"])) {
        if ($adminMessageAllPlayers) {
            $pid = '1234';
			$typeAcp = '"AdminMessageAll"';
			$valueAcp = '["' . $_POST['adminMessage'] . '"]';
			$stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value,source) VALUES (?,?,?,?)");
			$stmt->bind_param("ssss", $pid, $typeAcp, $valueAcp, $LogSupporterName);
			$stmt->execute();
            
            $LogCategory = "adminMessageAll";
            $LogText = '['.$LogSupporterGroup.'] '.$LogSupporterName.' hat eine Admin Nachricht an alle Spieler mit dem Inhalt: "' . $valueAcp . '" gesendet.';
            log_acp($LogCategory,$LogText,$LogSupporterPid,$LogSupporterName,$LogSupporterGroup,$LogUserPid,$LogUserName);
        }
    }
?>
<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
	<head>
		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Admin Nachricht</title>
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
			<?php include 'includes/header.php'; ?>
			<div class="inner-wrapper">
				<?php include 'includes/sideBar.php'; ?>
				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Admin Nachricht an alle Spieler</h2>
					</header>
						<!-- start: page -->
                        <div class="row">
                            <div class="col-lg-12">
                                <section class="card card-horizontal mb-4">
                                    <div class="card-body p-3">
                                        <form method="post">
                                            <h5 class="title">Nachricht</h5>
                                            <input type="text" name="adminMessage" id="adminMessage" placeholder="Nachricht an alle Spieler" class="form-control">
                                            <input type="text" name="supporterName" id="supporterName" value="<?php echo $LogSupporterName; ?>" class="form-control" hidden>
                                            <button type="button" name="clearFields" id="clearID" class="mb-1 mt-3 mr-1 btn btn-default btn-lg btn-block">Clear</button>
                                            <button type="submit" name="adminMessageAll" id="submitID" class="mb-1 mt-3 mr-1 btn btn-primary btn-lg btn-block">Abschicken</button>
                                        </form>
                                    </div>
                                </section>
                            </div>
                        </div>
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
                $("#clearID").click(function(){
                    $("#adminMessage").val("");
                });
            });
        </script>


	</body>
</html>