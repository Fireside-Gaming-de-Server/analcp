<?php
include 'includes/checkOnline.php';

/*
if (apcu_exists('dashboardSomeData')) {
	$arrPlayerGesamt = apcu_fetch('dashboardSomeData');
	$usersComplete = $arrPlayerGesamt['usersComplete'];
	$copsInsgesamt = $arrPlayerGesamt['copsInsgesamt'];
	$medInsgesamt = $arrPlayerGesamt['medInsgesamt'];
	$bannedPlayer = $arrPlayerGesamt['bannedPlayer'];
	$vehicleInsgesamt = $arrPlayerGesamt['vehicleInsgesamt'];
	$housesInsgesamt = $arrPlayerGesamt['housesInsgesamt'];
	$moneyTotal = $arrPlayerGesamt['moneyTotal'];
	$gesamtVermögen = $arrPlayerGesamt['gesamtVermögen'];
	$blackJack = $arrPlayerGesamt['blackJack'];
	$jackPot = $arrPlayerGesamt['jackPot'];
} else {
*/

function ArrayToString($Array) {
    $ArrayJson = json_encode($Array);
    $String = str_replace('"', '`', $ArrayJson);
    return '"' . $String . '"';
}

function StringToArray($String) {
    $json = str_replace('`', '"', $String);
    $Array = json_decode(trim($json, '"'), true);
    return $Array;
}


$stmt = $dbm->prepare("SELECT * FROM rcon WHERE id=1");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_assoc($result)) {
	$onlineCount = count(StringToArray($row["onlinePlayers"]));
	$onlineCopsCount = $row["cops"];
	$onlineMedsCount = $row["medics"];
	$onlineCivsCount = $row["civs"];
}

$stmt = $dbm->prepare("SELECT count(uid) FROM players");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$usersComplete = $row[0];

$stmt = $dbm->prepare("SELECT count(uid) FROM players WHERE coplevel > '0'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$copsInsgesamt = $row[0];

$stmt = $dbm->prepare("SELECT count(uid) FROM players WHERE mediclevel > '0'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$medInsgesamt = $row[0];


$stmt = $dbm->prepare("SELECT count(uid) FROM players WHERE dojlevel > '0' AND adminlevel != 5");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$dojInsgesamt = $row[0];

$stmt = $dbm->prepare("SELECT count(uid) FROM players WHERE towlevel > '0' AND adminlevel != 5");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$towInsgesamt = $row[0];

$stmt = $dbm->prepare("SELECT count(uid) FROM players WHERE gcmlevel > '0' AND adminlevel != 5");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$gcmInsgesamt = $row[0];

$stmt = $dbm->prepare("SELECT count(uid) FROM players WHERE gcpatlevel > '0' AND adminlevel != 5");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$gcpatInsgesamt = $row[0];


/*
$stmt = $dbm->prepare("SELECT * FROM crate_stats");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$Crate_stats = number_format($row["money_gesamt"], 0, ',', '.');
}
*/

/*
$stmt = $dbl->prepare("SELECT bannedPlayerArrServer, bannedPlayerArrRcon FROM cache_bannedPlayer WHERE id=1");
$stmt->execute();
$result = $stmt->get_result();
$bannedPlayer = 0;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$count1 = count(json_decode($row['bannedPlayerArrServer']));
	$count2 = count(json_decode($row['bannedPlayerArrRcon']));
	$bannedPlayer = $count1 + $count2;
}
*/

$current_datetime = date('Y-m-d H:i:s');
$sql = "SELECT COUNT(*) AS activBannedPlayer FROM acp_bans WHERE ban_end > '$current_datetime'";
$result = mysqli_query($dbm, $sql);
if (!$result) {
    error_log('Abfragefehler "DASHBOARD > activBannedPlayer ' . mysqli_error($dbm));
}
$row = mysqli_fetch_assoc($result);
$activBannedPlayer = $row['activBannedPlayer'];


$stmt = $dbm->prepare("SELECT count(id) FROM vehicles");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$vehicleInsgesamt = number_format($row[0], 0, ',', '.');

$stmt = $dbm->prepare("SELECT count(id) FROM houses");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$housesInsgesamt = $row[0];

$stmt = $dbm->prepare("SELECT count(id) FROM entreprises");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$entreprisesInsgesamt = $row[0];

$stmt = $dbl->prepare("SELECT SUM(money) AS money, SUM(licenses) AS licenses, SUM(locker_upgrades) AS locker_upgrades, SUM(locker_items) AS locker_items, SUM(houses) AS houses, SUM(vehicles) AS vehicles, SUM(tuning) AS tuning, SUM(auktionshaus) AS auktionshaus FROM stats_player_actually");
$stmt->execute();
$result = $stmt->get_result();
$gesamtVermögen = 0;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$gesamtVermögen = $gesamtVermögen + $row['money'];
	$gesamtVermögen = $gesamtVermögen + $row['licenses'];
	$gesamtVermögen = $gesamtVermögen + $row['locker_upgrades'];
	$gesamtVermögen = $gesamtVermögen + $row['locker_items'];
	$gesamtVermögen = $gesamtVermögen + $row['houses'];
	$gesamtVermögen = $gesamtVermögen + $row['vehicles'];
	$gesamtVermögen = $gesamtVermögen + $row['tuning'];
	$gesamtVermögen = $gesamtVermögen + $row['auktionshaus'];
	$gesamtVermögen = number_format($gesamtVermögen, 0, ',', '.');
}

$stmt = $dbm->prepare("SELECT * FROM vehicles JOIN acp_vehicles ON vehicles.classname = acp_vehicles.classname");
$stmt->execute();
$result = $stmt->get_result();
$priceVehValue = 0;
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	if ($row['price'] != -1) {
		$priceVehValue = $priceVehValue + $row['price'];
	}
};

$playerMoneystmt = $dbm->prepare("SELECT cash,bankacc FROM players WHERE adminlevel <> '5'");
$playerMoneystmt->execute();
$playerMoneyresult = $playerMoneystmt->get_result();

$playerValue = 0;
$LicenseVal = 0;

while ($row = mysqli_fetch_assoc($playerMoneyresult)) {
    $playerValue += $row['cash'];
    $playerValue += $row['bankacc'];
}

$stmt = $dbm->prepare("SELECT civ_licenses,cop_licenses,med_licenses FROM perso ");
$stmt->execute();
$result = $stmt->get_result();

$licensestmt = $dbm->prepare("SELECT varname,price FROM acp_licenses");
$licensestmt->execute();
$licenseresult = $licensestmt->get_result();

while ($row = mysqli_fetch_assoc($result)) {
	$string = $row["civ_licenses"];
    $jsonlicenses = str_replace('`', '"', $string);
    $civlicenseArray = json_decode(trim($jsonlicenses, '"'), true);

    $string = $row["cop_licenses"];
    $jsonlicenses = str_replace('`', '"', $string);
    $coplicenseArray = json_decode(trim($jsonlicenses, '"'), true);

    $string = $row["med_licenses"];
    $jsonlicenses = str_replace('`', '"', $string);
    $medlicenseArray = json_decode(trim($jsonlicenses, '"'), true);

    $licenseArray = array_merge($civlicenseArray, $coplicenseArray, $medlicenseArray);
    if (!is_array($licenseArray)) {
        echo 'Das Array wurde nicht korrekt initialisiert.';
        exit;
    }

    foreach ($licenseArray as $licenseArrayElement) {
        while ($licenserow = mysqli_fetch_assoc($licenseresult)) {
            if ($licenseArrayElement[0] === $licenserow["varname"]) {
                if ($licenseArrayElement[1] === 1) {
                    if ($licenserow['price'] != -1) {
                        $LicenseVal += $licenserow['price'];
                    }
                }

				break; // No need to continue iterating once a match is found
            }
        }
        mysqli_data_seek($licenseresult, 0);
    }
}


$gesamtVermögen = number_format($priceVehValue + $playerValue + $LicenseVal, 0, ',', '.');
$priceVehValue = number_format($priceVehValue, 0, ',', '.');
$playerValue = number_format($playerValue, 0, ',', '.');
$LicenseVal = number_format($LicenseVal, 0, ',', '.');

$stmt = $dbm->prepare("SELECT SUM(bankacc + cash) FROM players WHERE adminlevel < 4");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$moneyTotal = number_format($row[0], 0, ',', '.');

/*
$stmt = $dbm->prepare("SELECT value FROM blackjackstats");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$blackJack = number_format($row[0], 0, ',', '.');

$stmt = $dbm->prepare("SELECT value FROM jackpotstats");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$jackPot = $row[0];
$jackPot = '- ' . number_format($jackPot, 0, ',', '.') . '';
*/

$arrPlayerGesamt = array(
	'usersComplete' => $usersComplete,
	'copsInsgesamt' => $copsInsgesamt,
	'medInsgesamt' => $medInsgesamt,
	'dojInsgesamt' => $dojInsgesamt,
	'towInsgesamt' => $towInsgesamt,
	'activBannedPlayer' => $activBannedPlayer,
	'vehicleInsgesamt' => $vehicleInsgesamt,
	'housesInsgesamt' => $housesInsgesamt,
	'moneyTotal' => $moneyTotal,
	'gesamtVermögen' => $gesamtVermögen,
	// 'blackJack' => $blackJack,
	// 'jackPot' => $jackPot
);

//apcu_add('dashboardSomeData', $arrPlayerGesamt,300);
//}

//ChartPlayerOnline

/*
if (apcu_exists('chartOnlinePlayer1')) {
	$fetch = apcu_fetch('chartOnlinePlayer1');
	$chartOnlinePlayer1 = $fetch['chartOnlinePlayer1'];
	$chartOnlinePlayer1MaxY = $fetch['chartOnlinePlayer1MaxY'];
} else {
*/
$chartOnlinePlayer1 = [];
$chartOnlinePlayer1MaxY = 0;
$stmt = $dbl->prepare("SELECT time_stamp, all_players, civ, cop, med FROM stats_player_online WHERE time_stamp > NOW() - INTERVAL 1 DAY");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$chartOnlinePlayer1[] = array(
		"y" => $row['time_stamp'],
		"a" => $row['all_players'],
		"b" => $row['civ'],
		"c" => $row['cop'],
		"d" => $row['med']

	);
	if ($row['all_players'] > $chartOnlinePlayer1MaxY) {
		$chartOnlinePlayer1MaxY = $row['all_players'];
	}
}
$chartOnlinePlayer1 = json_encode($chartOnlinePlayer1);

$arr = array(
	'chartOnlinePlayer1' => $chartOnlinePlayer1,
	'chartOnlinePlayer1MaxY' => $chartOnlinePlayer1MaxY
);

//apcu_add('chartOnlinePlayer1', $arr,300);
//}

/*
if (apcu_exists('chartOnlinePlayer7')) {
	$fetch = apcu_fetch('chartOnlinePlayer7');
	$chartOnlinePlayer7 = $fetch['chartOnlinePlayer7'];
	$chartOnlinePlayer7MaxY = $fetch['chartOnlinePlayer7MaxY'];
} else {
    $chartOnlinePlayer7 = [];
    $chartOnlinePlayer7MaxY = 0;
    $stmt = $dbl->prepare("SELECT time_stamp, all_players, civ, cop, med FROM stats_player_online WHERE time_stamp > NOW() - INTERVAL 7 DAY");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $chartOnlinePlayer7[] = array(
            "y" => $row['time_stamp'],
            "a" => $row['all_players'],
            "b" => $row['civ'],
            "c" => $row['cop'],
            "d" => $row['med']

        );
        if ($row['all_players'] > $chartOnlinePlayer7MaxY) {
            $chartOnlinePlayer7MaxY = $row['all_players'];
        }
    }
    $chartOnlinePlayer7 = json_encode($chartOnlinePlayer7);

    $arr = array(
        'chartOnlinePlayer7' => $chartOnlinePlayer7,
        'chartOnlinePlayer7MaxY' => $chartOnlinePlayer7MaxY
    );
    apcu_add('chartOnlinePlayer7', $arr,300);
}
*/


/*
if (apcu_exists('chartOnlinePlayer14')) {
	$fetch = apcu_fetch('chartOnlinePlayer14');
	$chartOnlinePlayer14 = $fetch['chartOnlinePlayer14'];
	$chartOnlinePlayer14MaxY = $fetch['chartOnlinePlayer14MaxY'];
} else {
    $chartOnlinePlayer14 = [];
    $chartOnlinePlayer14MaxY = 0;
    $stmt = $dbl->prepare("SELECT time_stamp, all_players, civ, cop, med FROM stats_player_online WHERE time_stamp > NOW() - INTERVAL 14 DAY");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $chartOnlinePlayer14[] = array(
            "y" => $row['time_stamp'],
            "a" => $row['all_players'],
            "b" => $row['civ'],
            "c" => $row['cop'],
            "d" => $row['med']

        );
        if ($row['all_players'] > $chartOnlinePlayer14MaxY) {
            $chartOnlinePlayer14MaxY = $row['all_players'];
        }
    }
    $chartOnlinePlayer14 = json_encode($chartOnlinePlayer14);

    $arr = array(
        'chartOnlinePlayer14' => $chartOnlinePlayer14,
        'chartOnlinePlayer14MaxY' => $chartOnlinePlayer14MaxY
    );

    apcu_add('chartOnlinePlayer14', $arr,300);
}
*/

/*
// chartUniquePlayers
$chartUniquePlayers7 = [];
$chartUniquePlayers7maxY = 0;

if (apcu_exists('chartUniquePlayers7')) {
	$fetch = apcu_fetch('chartUniquePlayers7');
	$chartUniquePlayers7 = $fetch['chartUniquePlayers7'];
	$chartUniquePlayers7maxY = $fetch['chartUniquePlayers7maxY'];
} else {
    $chartUniquePlayers7 = [];
    $chartUniquePlayers7maxY = 0;
    $stmt = $dbl->prepare("SELECT uninque_players, new_players, time_stamp FROM stats_new_player_daylie WHERE time_stamp > NOW() - INTERVAL 8 DAY");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $chartUniquePlayers7[] = array(
            "y" => $row['time_stamp'],
            "a" => $row['uninque_players'],
            "b" => $row['new_players']
        );
        if ($row['uninque_players'] > $chartUniquePlayers7maxY) {
            $chartUniquePlayers7maxY = $row['uninque_players'];
        }
    }
    $chartUniquePlayers7 = json_encode($chartUniquePlayers7);

    $arr = array(
        'chartUniquePlayers7' => $chartUniquePlayers7,
        'chartUniquePlayers7maxY' => $chartUniquePlayers7maxY
    );

    apcu_add('chartUniquePlayers7', $arr,300);
}
*/

/*
$chartUniquePlayers14 = [];
$chartUniquePlayers14maxY = 0;
/*
if (apcu_exists('chartUniquePlayers14')) {
	$fetch = apcu_fetch('chartUniquePlayers14');
	$chartUniquePlayers14 = $fetch['chartUniquePlayers14'];
	$chartUniquePlayers14maxY = $fetch['chartUniquePlayers14maxY'];
} else {
    $chartUniquePlayers14 = [];
    $chartUniquePlayers14maxY = 0;
    $stmt = $dbl->prepare("SELECT uninque_players, new_players, time_stamp FROM stats_new_player_daylie WHERE time_stamp > NOW() - INTERVAL 15 DAY");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_row();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $chartUniquePlayers14[] = array(
            "y" => $row['time_stamp'],
            "a" => $row['uninque_players'],
            "b" => $row['new_players']
        );
        if ($row['uninque_players'] > $chartUniquePlayers14maxY) {
            $chartUniquePlayers14maxY = $row['uninque_players'];
        }
    }
    $chartUniquePlayers14 = json_encode($chartUniquePlayers14);

    $arr = array(
        'chartUniquePlayers14' => $chartUniquePlayers14,
        'chartUniquePlayers14maxY' => $chartUniquePlayers14maxY
    );

    apcu_add('chartUniquePlayers14', $arr,300);
}
*/
?>

<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
<html>

<head>
	<!-- Basic -->
	<title>Dashboard</title>
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

	<!-- Specific Page Vendor CSS -->
	<link rel="stylesheet" href="vendor/select2/css/select2.css" />
	<link rel="stylesheet" href="vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
	<!-- Specific Page Vendor CSS -->
	<link rel="stylesheet" href="vendor/morris/morris.css" />
	<link rel="stylesheet" href="vendor/chartist/chartist.min.css" />
	<link rel="stylesheet" href="vendor/pnotify/pnotify.custom.css" />


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
		<!-- include Header-->
		<?php include 'includes/header.php'; ?>
		<!-- include Header-->
		<div class="inner-wrapper">
			<!-- include SideBar-->
			<?php include 'includes/sideBar.php'; ?>
			<!-- include SideBar-->
			<section role="main" class="content-body card-margin">
				<header class="page-header">
					<h2>Dashboard</h2>
				</header>
				<!-- start: page -->
                <section class="fs-welcome" aria-labelledby="welcome-title">
                    <div>
                        <p class="fs-eyebrow">FIRESIDE GAMING · TEAM AREA</p>
                        <h1 id="welcome-title">Deine Community im Überblick.</h1>
                        <p>Spieler, Aktivität und Verwaltung – alles an einem Ort.</p>
                    </div>
                    <div class="fs-shortcuts">
                        <?php if (!empty($_SESSION["rightsArr"]["visitPlayerOnline"])): ?>
                        <a class="btn btn-default" href="onlinePlayers.php"><i class="fas fa-users" aria-hidden="true"></i> Spieler online</a>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
                $dashboardGroups = array(
                    array('label' => 'Cops', 'count' => max(0, (int) $copsInsgesamt), 'color' => '#ff1f1f'),
                    array('label' => 'Medics', 'count' => max(0, (int) $medInsgesamt), 'color' => '#f97373'),
                    array('label' => 'DOJ', 'count' => max(0, (int) $dojInsgesamt), 'color' => '#a78bfa'),
                    array('label' => 'RATS', 'count' => max(0, (int) $towInsgesamt), 'color' => '#fbbf24'),
                    array('label' => 'GCM', 'count' => max(0, (int) $gcmInsgesamt), 'color' => '#38bdf8')
                );
                $dashboardGroupTotal = array_sum(array_column($dashboardGroups, 'count'));
                $dashboardStops = array();
                $dashboardPosition = 0;
                foreach ($dashboardGroups as $group) {
                    if ($group['count'] === 0 || $dashboardGroupTotal === 0) continue;
                    $dashboardEnd = $dashboardPosition + $group['count'] / $dashboardGroupTotal * 100;
                    $dashboardStops[] = $group['color'] . ' ' . number_format($dashboardPosition, 4, '.', '') . '% ' . number_format($dashboardEnd, 4, '.', '') . '%';
                    $dashboardPosition = $dashboardEnd;
                }
                $dashboardGradient = $dashboardGroupTotal > 0 ? 'conic-gradient(' . implode(', ', $dashboardStops) . ')' : '#30353e';
                ?>
                <section class="card fs-community-card mb-4" aria-labelledby="community-chart-title">
                    <header class="card-header">
                        <h2 class="card-title" id="community-chart-title">Spieler &amp; Gruppenzugehörigkeiten</h2>
                    </header>
                    <div class="card-body fs-community-layout">
                        <div class="fs-community-ring" style="background: <?php echo $dashboardGradient; ?>;" role="img" aria-label="Gruppenverteilung. <?php echo (int) $usersComplete; ?> Spieler insgesamt. Gruppenanzahlen stehen in der Legende.">
                            <div class="fs-community-center">
                                <strong><?php echo number_format((int) $usersComplete, 0, ',', '.'); ?></strong>
                                <span>Spieler insgesamt</span>
                            </div>
                        </div>
                        <div class="fs-community-details">
                            <ul class="fs-community-legend" aria-label="Gruppenanzahlen">
                                <?php foreach ($dashboardGroups as $group): ?>
                                <li>
                                    <span class="fs-community-dot" style="background: <?php echo $group['color']; ?>;" aria-hidden="true"></span>
                                    <span><?php echo $group['label']; ?></span>
                                    <strong><?php echo number_format($group['count'], 0, ',', '.'); ?></strong>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <p class="fs-community-note"><?php echo $dashboardGroupTotal === 0 ? 'Noch keine Gruppenzugehörigkeiten vorhanden. ' : ''; ?>Die Segmente zeigen die Verteilung der aufgeführten Gruppenzugehörigkeiten. Spieler können mehreren Gruppen angehören.</p>
                        </div>
                    </div>
                </section>

				<div class="row">
					<div class="col-lg-4 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
									<div class="summary-icon bg-primary">
										<i class="fa-solid fa-user"></i>
									</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">CIVS ONLINE</h5>
											<div class="info">
												<strong class="amount"><?php echo $onlineCivsCount; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="col-lg-4 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fa-solid fa-user-police"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">COPS ONLINE</h5>
											<div class="info">
												<strong class="amount"><?php echo $onlineCopsCount; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>


					<div class="col-lg-4 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fa-solid fa-user-doctor"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">MEDICS ONLINE</h5>
											<div class="info">
												<strong class="amount"><?php echo $onlineMedsCount; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fa-solid fa-block-question"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">GC Paint and Tuning INSGESAMT</h5>
											<div class="info">
												<strong class="amount"><?php echo $gcpatInsgesamt; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-dollar-sign"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">GELD IM UMLAUF</h5>
											<div class="info">
												<strong class="amount"><?php echo $moneyTotal; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-car"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">AUTOS INSGESAMT</h5>
											<div class="info">
												<strong class="amount"><?php echo $vehicleInsgesamt; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-home"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">HÄUSER INSGESAMT</h5>
											<div class="info">
												<strong class="amount"><?php echo $housesInsgesamt; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-building"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">FIRMEN INSGESAMT</h5>
											<div class="info">
												<strong class="amount"><?php echo $entreprisesInsgesamt; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-money-check-alt"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">GARAGEN GESAMTVERMÖGEN</h5>
											<div class="info">
												<strong class="amount"><?php echo $priceVehValue; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-money-check-alt"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">SPIELER GESAMTVERMÖGEN</h5>
											<div class="info">
												<strong class="amount"><?php echo $playerValue; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-money-check-alt"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">LIZENZ GESAMTVERMÖGEN</h5>
											<div class="info">
												<strong class="amount"><?php echo $LicenseVal; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<div class="col-lg-2 col-xl-2">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-money-check-alt"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">GESAMTVERMÖGEN SUMME</h5>
											<div class="info">
												<strong class="amount"><?php echo $gesamtVermögen; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
				</div>



				<div class="row">
					<!-- <div class="col-lg-4 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-people-carry"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">JACKPOT UMSATZ</h5>
											<div class="info">
												<strong class="amount"><?php /* echo $jackPot;  "IN ARBEIT"; */ ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div> -->
					<!-- <div class="col-lg-4 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-hand-holding-usd"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">BLACKJACK UMSATZ</h5>
											<div class="info">
												<strong class="amount"><?php // echo /* $blackJack; */ "IN ARBEIT"; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div> -->
					<div class="col-lg-2 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-ban"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">AKTUELL GEBANNTE SPIELER</h5>
											<div class="info">
												<strong class="amount"><?php echo $activBannedPlayer; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div>
					<!-- <div class="col-lg-4 col-xl-4">
						<section class="card card-featured-left card-featured-primary mb-4">
							<div class="card-body">
								<div class="widget-summary widget-summary-md">
									<div class="widget-summary-col widget-summary-col-icon">
										<div class="summary-icon bg-primary">
											<i class="fas fa-people-carry"></i>
										</div>
									</div>
									<div class="widget-summary-col">
										<div class="summary">
											<h5 class="title">CRATE STATS</h5>
											<div class="info">
												<strong class="amount">- <?php // echo /* $Crate_stats; */ "IN ARBEIT"; ?></strong>
											</div>
										</div>
									</div>
								</div>
							</div>
						</section>
					</div> -->
				</div>

				<!-- <div class="row">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-lg-12 col-xl-12">
								<section class="card card-featured-left card-featured-primary">
									<header class="card-header">
										<h3 class="card-title text-dark">Spieler Online Stats</h3>
									</header>
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
									</div>
									<div class="card-body">
										<div class="tabs tabs-primary">
											<ul class="nav nav-tabs">
												<li class="nav-item active">
													<a class="nav-link text-dark" href="#stunden" data-toggle="tab">24 Stunden</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-dark" href="#sieben" data-toggle="tab">7 Tage</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-dark" href="#vierzehn" data-toggle="tab">14 Tage</a>
												</li>
											</ul>
											<div class="tab-content">
												<div id="stunden" class="tab-pane active">
													<div class="chart chart-md" id="dashboardPlayerOnline24"></div>
													<?php
                                        /*
													echo '
																		<script type="text/javascript">

																		var chartOnlinePlayer1 = ' . $chartOnlinePlayer1 . ';
																		var chartOnlinePlayer1MaxY = ' . $chartOnlinePlayer1MaxY . ';


																		</script>
																		';

													?>

												</div>
												<div id="sieben" class="tab-pane">
													<div class="chart chart-md" id="dashboardPlayerOnline7"></div>
													<?php
													echo '
																	<script type="text/javascript">

																	var chartOnlinePlayer7 = ' . $chartOnlinePlayer7 . ';
																	var chartOnlinePlayer7MaxY = ' . $chartOnlinePlayer7MaxY . ';


																	</script>
																	';

													?>

												</div>
												<div id="vierzehn" class="tab-pane">
													<div class="chart chart-md" id="dashboardPlayerOnline14"></div>
													<?php
													echo '
																	<script type="text/javascript">

																	var chartOnlinePlayer14 = ' . $chartOnlinePlayer14 . ';
																	var chartOnlinePlayer14MaxY = ' . $chartOnlinePlayer14MaxY . ';


																	</script>
																	';
                                        */
													?>

												</div>
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
					</div>
				</div> -->

				<!-- <div class="row">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-lg-12 col-xl-12">
								<section class="card card-featured-left card-featured-primary">
									<header class="card-header">
										<h3 class="card-title text-dark">Daylie Player Stats</h3>
									</header>
									<div class="card-actions">
										<a href="#" class="card-action card-action-toggle" data-card-toggle></a>
									</div>
									<div class="card-body">
										<div class="tabs tabs-primary">
											<ul class="nav nav-tabs">
												<li class="nav-item active">
													<a class="nav-link text-dark" href="#barSieben" data-toggle="tab">7 Tage</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-dark" href="#barVierzehn" data-toggle="tab">14 Tage</a>
												</li>
											</ul>
											<div class="tab-content">
												<div id="barSieben" class="tab-pane active">
													<div class="chart chart-md" id="dashBoardBar7"></div>
													<?php
										/*
                                                    echo '
																	<script type="text/javascript">

																	var chartUniquePlayers7 = ' . $chartUniquePlayers7 . ';
																	var chartUniquePlayers7MaxY = ' . $chartUniquePlayers7maxY . ';


																	</script>
																	';

													?>

												</div>
												<div id="barVierzehn" class="tab-pane">
													<div class="chart chart-md" id="dashBoardBar14"></div>
													<?php
													echo '
																	<script type="text/javascript">

																	var chartUniquePlayers14 = ' . $chartUniquePlayers14 . ';
																	var chartUniquePlayers14MaxY = ' . $chartUniquePlayers14maxY . ';


																	</script>
																	';
                                        */
													?>

												</div>
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
					</div>
				</div> -->

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
			var urlParams = new URLSearchParams(window.location.search);
			$(document).ready(function(){
				$('#datatable-ajax').dataTable({
					"oSearch": {"sSearch": urlParams.has('search') ? urlParams.get('search') : '' },
					'bProcessing': true,
					'serverSide': true,
					'serverMethod': 'post',
					"pageLength": 100,
					"lengthMenu": [[25, 50, 75, 100, 250, 500], [25, 50, 75, 100, 250, 500]],
					'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p',
					'ajax': {
						'url':'scripts/ajaxTable/dbSpielerLevelID.php'
					},
					'columns': [
						{
							data: 'name'
						},
						{
							data: 'side',
							render: function(data, type, row) {
								switch (data) {
									case 'WEST':
										return 'COP';
									case 'GUER':
										return 'MED';
									case 'CIV':
										return 'CIV';
									default:
										return data;
								}
							}
						},
						{ data: 'pid', "orderable": false },
						{ data: 'guid', "orderable": false },
						{ data: 'money', "orderable": false  },
						{
							data: 'bankacc',
							render: function(data, type, row) {
								var formattedMoney = parseFloat(data).toLocaleString('de-DE')  + ' $';
								return formattedMoney;
							}
						},
						{
							data: 'cash',
							render: function(data, type, row) {
								var formattedMoney = parseFloat(data).toLocaleString('de-DE')  + ' $';
								return formattedMoney;
							}
						},
						{ data: 'coplevel' },
						{ data: 'mediclevel' },
						{ data: 'dojlevel' },
						{ data: 'towlevel' },
						{ data: 'adminlevel' },
						{
							data: 'last_seen'
						},
						{ data: 'button', "orderable": false }
					]
				});
			});
		</script>


	</body>
</html>