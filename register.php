<!doctype html>
<html class="fixed dark">

	<?php
	require ('steamauth/steamauth.php');

	if (isset($_POST['submit'])) {
		if(!isset($_SESSION['steamid'])) {
		}  else {
			include ('steamauth/userInfo.php');
		  
			require ("includes/mysql.php");
			$username = $_POST['username'];
			$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_row();
			if ($row == 0) {
				$stmt2 = $dbl->prepare("SELECT * FROM acp_users WHERE steam_id=?");
				$stmt2->bind_param("s", $steamprofile['steamid']);
				$stmt2->execute();
				$result2 = $stmt2->get_result();
				$row2 = $result2->fetch_row();
				if ($row2 == 0) {
					$userGroupID =99;
					$SortID = 99;
					if ($_POST["pwd"] == $_POST["pwd_confirm"]) { 
						$hash = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
						$stmt = $dbl->prepare("INSERT INTO acp_users (username, password,steam_id,userGroupID,sort_id,steam_picture) VALUES (?,?,?,?,?,?)");
						$stmt->bind_param("sssiis", $username, $hash, $steamprofile['steamid'],$userGroupID,$SortID,$steamprofile['avatarmedium']);
						$stmt->execute();
						echo "Alles Gut";
						sleep(3);
						header("Location: logout.php");
					} else {
						echo "passwort stimmt nicht überein";
					}
				} else {
					sleep(3);
					header("Location: logout.php");
				}
			} else {
				echo "username schon vergeben";
			}
		}
	}

	?>

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

		<!-- Theme CSS -->
		<link rel="stylesheet" href="css/theme.css" />

		<!-- Skin CSS -->
		<link rel="stylesheet" href="css/skins/default.css" />

		<!-- Theme Custom CSS -->
		<link rel="stylesheet" href="css/custom.css">

		<!-- Head Libs -->
		<script src="vendor/modernizr/modernizr.js"></script>
		<html>

		<!-- start: page -->
		<section class="body-sign">
			<div class="center-sign">
				<a href="/" class="logo float-left">
					<span class="fs-logo"><img src="img/fireside-logo.png" width="2048" height="2048" alt="Fireside Gaming"></span>
				</a>

				<div class="panel card-sign">
					<div class="card-title-sign mt-3 text-right">
						<h2 class="title text-uppercase font-weight-bold m-0"><i class="fas fa-user mr-1"></i> Registrieren #2</h2>
					</div>
					<div class="card-body">
					<span class="mt-3 mb-3 line-thru text-center text-uppercase">
								<span>Third and last step</span>
							</span>
						<form action="register.php" method="post">
							<div class="form-group mb-3">
								<label>Username</label>
								<input type="text" name="username" class="form-control form-control-lg" />
							</div>

							<div class="form-group mb-0">
								<div class="row">
									<div class="col-sm-6 mb-3">
										<label>Password</label>
										<input name="pwd" type="password" class="form-control form-control-lg" />
									</div>
									<div class="col-sm-6 mb-3">
										<label>Password Confirmation</label>
										<input name="pwd_confirm" type="password" class="form-control form-control-lg" />
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-sm-1">
								</div>
								<div class="col-sm-10">
										<button type="submit" name="submit" class="mb-1 mt-1 mr-1 btn btn-primary btn-lg btn-block">Registrieren</button>
								</div>
								<div class="col-sm-1">
								</div>
							</div>

							<span class="mt-3 mb-3 line-thru text-center text-uppercase">
								<span>or</span>
							</span>

							<p class="text-center">Already have an account? <a href="index.php">Anmelden</a></p>

						</form>
					</div>
				</div>

				<p class="text-center text-muted mt-3 mb-3">&copy; Made with ♥  by Klausi</p>
			</div>
		</section>
		<!-- end: page -->


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
		
		<!-- Theme Base, Components and Settings -->
		<script src="js/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="js/custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="js/theme.init.js"></script>

	</body>

</html>