<?php
require('steamauth/steamauth.php');
require("includes/mysql.php");
if ((isset($_COOKIE["username"])) && (isset($_COOKIE["usergroup"])) && (isset($_COOKIE["identifier"])) && (isset($_COOKIE["securitytoken"]))) {
	header("Location: dashboard.php");
}

include 'includes/function.php';
$loginError = "";



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
		$loginError = "Benutzername oder Passwort ist nicht korrekt.";
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
			$loginError = "Benutzername oder Passwort ist nicht korrekt.";
		}

	}
}
?>

<!doctype html>
<html lang="de">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Der Team-Zugang zum Fireside Gaming Adminpanel.">
	<title>Anmelden · Fireside Gaming</title>
	<link rel="icon" href="img/favicon.ico">
	<link rel="stylesheet" href="vendor/font-awesome/css/all.css">
	<link rel="stylesheet" href="css/login.css">
</head>
<body class="login-page">
	<div class="login-shell">
		<header class="site-header">
			<a class="brand" href="index.php" aria-label="Fireside Gaming Startseite">
				<span class="brand-icon" aria-hidden="true"><i class="fas fa-fire"></i></span>
				<span>FIRESIDE<span class="brand-subtitle">GAMING COMMUNITY</span></span>
			</a>
			<span class="header-label">ADMIN CONTROL PANEL</span>
		</header>

		<main class="login-layout">
			<section class="intro" aria-labelledby="intro-title">
				<p class="eyebrow"><span></span> DER PLATZ FÜR DEIN TEAM</p>
				<h1 id="intro-title">Deine Community.<br>Alles <span>im Blick.</span></h1>
				<p class="intro-copy">Gemeinsam für ein besseres Spielerlebnis. Verwalte Spieler, unterstütze deine Community und behalte den Überblick.</p>
				<div class="feature-list">
					<div class="feature"><span class="feature-icon"><i class="fas fa-users" aria-hidden="true"></i></span><div><h2>Spieler & Community</h2><p>Alle wichtigen Informationen an einem Ort.</p></div></div>
					<div class="feature"><span class="feature-icon"><i class="fas fa-shield-alt" aria-hidden="true"></i></span><div><h2>Support & Verwaltung</h2><p>Deine Werkzeuge für den Teamalltag.</p></div></div>
					<div class="feature"><span class="feature-icon"><i class="fas fa-chart-line" aria-hidden="true"></i></span><div><h2>Aktivität & Überblick</h2><p>Behalte das Geschehen auf dem Server im Blick.</p></div></div>
				</div>
				<div class="intro-note"><span class="note-line"></span> FIRESIDE GAMING · TEAM AREA</div>
			</section>

			<section class="login-card" aria-labelledby="login-title">
				<div class="card-icon" aria-hidden="true"><i class="fas fa-sign-in-alt"></i></div>
				<p class="eyebrow card-eyebrow">WILLKOMMEN ZURÜCK</p>
				<h2 id="login-title">Anmelden</h2>
				<p class="card-copy">Melde dich mit deinem Team-Account an.</p>
				<?php if ($loginError !== ""): ?>
					<div class="login-error" role="alert"><?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?></div>
				<?php endif; ?>
				<form action="index.php" method="post">
					<div class="field">
						<label for="username">Benutzername</label>
						<div class="input-wrap"><i class="fas fa-user" aria-hidden="true"></i><input id="username" name="username" type="text" autocomplete="username" placeholder="Dein Benutzername" required></div>
					</div>
					<div class="field">
						<label for="password">Passwort</label>
						<div class="input-wrap"><i class="fas fa-lock" aria-hidden="true"></i><input id="password" name="pwd" type="password" autocomplete="current-password" placeholder="Dein Passwort" required></div>
					</div>
					<button class="login-button" type="submit" name="submit">Anmelden <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
				</form>
				<div class="divider"><span>oder mit Steam fortfahren</span></div>
				<a class="steam-button" href="?login"><i class="fab fa-steam" aria-hidden="true"></i> Mit Steam anmelden</a>
				<p class="access-note"><i class="fas fa-lock" aria-hidden="true"></i> Zugang nur für berechtigte Teammitglieder.</p>
			</section>
		</main>

		<footer class="site-footer"><span>© Fireside Gaming</span><span>Made with <span class="heart">♥</span> by Klausi · Edit by Fireside-Gaming.de</span></footer>
	</div>
</body>
</html>
