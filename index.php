<?php
require('steamauth/steamauth.php');
require("includes/mysql.php");
if ((isset($_COOKIE["username"])) && (isset($_COOKIE["usergroup"])) && (isset($_COOKIE["identifier"])) && (isset($_COOKIE["securitytoken"]))) {
	header("Location: dashboard.php");
}

include 'includes/function.php';



if (isset($_SESSION['steamid'])) {

	include('steamauth/userInfo.php');

	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE steam_id=?");
	$stmt->bind_param("s", $steamprofile['steamid']);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	if (($row[7]) == $steamprofile['steamid']) {
		$identifier = random_string();
		$securitytoken = random_string();
		$insert = $dbl->prepare("UPDATE acp_users SET identifier=?, securitytoken=?, steam_picture=? WHERE steam_id=?");
		$insert->bind_param("ssss", $identifier, sha1($securitytoken), $steamprofile['avatarmedium'], $steamprofile['steamid']);
		$insert->execute();
		$username = $row[1];
		$usergroup = $row[5];
		$usergroupID = $usergroup;
		if ($usergroup >= 1) {
			$stmt = $dbl->prepare("SELECT group_name  FROM acp_usergroups WHERE id=?");
			$stmt->bind_param("i", $usergroup);
			$stmt->execute();
			$result = $stmt->get_result();
			$row = $result->fetch_row();
			$usergroup = $row[0];
		}



		//rights arr machen
		$arr = array();
		$stmt = $dbl->prepare("SELECT `group_ids`, `permission_name` FROM acp_permissions");
		$stmt->execute();
		$result = $stmt->get_result();
		while ($row = mysqli_fetch_array($result)) {
			$key = $row['permission_name'];

			$group_id = $usergroupID;

			$rightsArr = json_decode($row['group_ids']);

			if (in_array($group_id, $rightsArr)) {
				$arr = $arr + array($key => true);
			} else {
				$arr = $arr + array($key => false);
			}
		}

		$_SESSION['rightsArr'] = $arr;


		setcookie("username", $username, time() - (3600)); ////DestroyIt
		setcookie("usergroup", $usergroup, time() - (3600)); ////DestroyIt
		setcookie("identifier", $identifier, time() - (3600)); //DestroyIt
		setcookie("securitytoken", $securitytoken, time() - (3600)); //DestroyIt
		setcookie("username", $username, time() + (43200)); //12 Stunden gültig
		setcookie("usergroup", $usergroup, time() + (43200)); //12 Stunden gültig
		setcookie("identifier", $identifier, time() + (43200)); //12 Stunden gültig
		setcookie("securitytoken", $securitytoken, time() + (43200)); //12 Stunden gültig
		header("location: dashboard.php");
		exit;
	}
}

if (isset($_POST['submit'])) {

	$username = $_POST['username'];
	$password = $_POST['pwd'];
	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
	if ($row == 0) {
		echo "Username nicht vergeben";
	} else {
		if (password_verify($password, $row[2])) {
			$identifier = random_string();
			$securitytoken = random_string();
			$insert = $dbl->prepare("UPDATE acp_users SET identifier=?, securitytoken=? WHERE username=?");
			$insert->bind_param("sss", $identifier, sha1($securitytoken), $username);
			$insert->execute();
			$usergroupID = $row[5];
			if ($usergroupID >= 1) {
				$stmt = $dbl->prepare("SELECT group_name FROM acp_usergroups WHERE id=?");
				$stmt->bind_param("i", $usergroupID);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_row();
				$usergroup = $row[0];
			}

			//rights arr machen
			$arr = array();
			$stmt = $dbl->prepare("SELECT `group_ids`, `permission_name` FROM acp_permissions");
			$stmt->execute();
			$result = $stmt->get_result();
			while ($row = mysqli_fetch_array($result)) {
				$key = $row['permission_name'];

				$group_id = $usergroupID;

				$rightsArr = json_decode($row['group_ids']);

				if (in_array($group_id, $rightsArr)) {
					$arr = $arr + array($key => true);
				} else {
					$arr = $arr + array($key => false);
				}
			}

			$_SESSION['rightsArr'] = $arr;


			setcookie("username", $username, time() - (3600)); ////DestroyIt
			setcookie("usergroup", $usergroup, time() - (3600)); ////DestroyIt
			setcookie("identifier", $identifier, time() - (3600)); //DestroyIt
			setcookie("securitytoken", $securitytoken, time() - (3600)); //DestroyIt
			setcookie("username", $username, time() + (43200)); //12 Stunden gültig
			setcookie("usergroup", $usergroup, time() + (43200)); //12 Stunden gültig
			setcookie("identifier", $identifier, time() + (43200)); //12 Stunden gültig
			setcookie("securitytoken", $securitytoken, time() + (43200)); //12 Stunden gültig
			header("location: dashboard");
		} else {
			echo "Password Falsch";
		}
		echo "Username Richtig";
	}
}
?>

<!doctype html>
<html class="fixed dark">

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

</head>

<body class="loading-overlay-showing" data-loading-overlay>
	<div class="loading-overlay">
		<div class="bounce-loader">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>
	<!-- start: page -->
	<section class="body-sign">
		<div class="center-sign">
			<a href="/" class="logo float-left">
				<img src="img/logo.png" height="54" alt="Porto Admin" />
			</a>

			<div class="panel card-sign">
				<div class="card-title-sign mt-3 text-right">
					<h2 class="title text-uppercase font-weight-bold m-0"><i class="fas fa-user mr-1"></i> Anmelden</h2>
				</div>
				<div class="card-body">
					<form action="index.php" method="post">
						<div class="form-group mb-3">
							<label>Username</label>
							<div class="input-group">
								<input name="username" type="text" class="form-control form-control-lg" />
								<span class="input-group-append">
									<span class="input-group-text">
										<i class="fas fa-user"></i>
									</span>
								</span>
							</div>
						</div>

						<div class="form-group mb-3">
							<div class="clearfix">
								<label class="float-left">Password</label>
							</div>
							<div class="input-group">
								<input name="pwd" type="password" class="form-control form-control-lg" />
								<span class="input-group-append">
									<span class="input-group-text">
										<i class="fas fa-lock"></i>
									</span>
								</span>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-1">
							</div>
							<div class="col-sm-10">
								<button type="submit" name="submit" class="mb-1 mt-1 mr-1 btn btn-primary btn-lg btn-block">ANMELDEN</button>
							</div>
							<div class="col-sm-1">
							</div>
						</div>

						<span class="mt-3 mb-3 line-thru text-center text-uppercase">
							<span>or</span>
						</span>

						<div class="mb-1 text-center">
							<?php loginbutton(); //login button ?>
						</div>

						<!-- <p class="text-center">Don't have an account yet? <a href="registerSteam.php">Registrieren!</a></p> -->

					</form>
				</div>
			</div>

			<p class="text-center text-muted mt-3 mb-3">&copy; Made with ♥ by Klausi edit by Fireside-Gaming.de</p>
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