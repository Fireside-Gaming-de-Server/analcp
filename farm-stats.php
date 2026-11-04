<?php
include 'includes/mysql.php';
include 'includes/function.php';
/*
if (apcu_exists('data_farming')) {
    $dataFarming = apcu_fetch('data_farming');  
    $tableArr = $dataFarming['tableArr'];
    $countTableArr = $dataFarming['countTableArr'];  
} else {
*/
$pidArr = array();
$stmt = $dbm->prepare("SELECT DISTINCT pid  FROM farmstats LIMIT 50");
$stmt->execute();
$result = $stmt->get_result();
while ($row = mysqli_fetch_array($result)) {

    $pidArr[] =  $row["pid"];
}

$gatherArr = array();
foreach ($pidArr as $key => $value) {

    $stmt = $dbm->prepare("SELECT insert_time FROM farmstats WHERE pid = ? AND mode = 'GATHER' AND to_start != -1 ");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = mysqli_fetch_array($result)) {

        $gatherArr[] =  array(
            'pid' => $value,
            'time' => $row['insert_time']
        );
    }
}

$multiTourenArr = array(
    'schmuck',
    'joint',
    'kupferkabel',
    'sushi',
    'zigaretten',
    'faden',
    'zuckererdbeeren',
    'bier',
    'cocainebaron',
    'maische',
    'moonshine',
    'heisenberg',
    'streckdiesel',
    'streckbenzin'
);
$tableArr = array();
foreach ($gatherArr as $key => $value) {

    $foundGather = false;
    $foundProcess = false;
    $foundSell = false;
    $falseFound = false;
    $arr = array();

    $stmt = $dbm->prepare("SELECT * FROM farmstats WHERE pid = ? AND to_start != -1 AND TrunkSPace != -1 AND insert_time <= (? + INTERVAL 1 HOUR) AND insert_time >= ? ");
    $stmt->bind_param("sss", $value['pid'], $value['time'], $value['time']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = mysqli_fetch_array($result)) {

        if ($row['mode'] === "GATHER") {
            if ($foundGather === false) {
                $foundGather = true;

                $space = $row['BbSpace'] + $row['TrunkSpace'];
                $arr = array(
                    'pid' => $row['pid'],
                    'itemArr' => $row['item'],
                    'time' => $row['to_start'],
                    'vehicle' => $row['vehicle'],
                    'backPack' => $row['backpack'],
                    'space' => $space,
                    'marker' => $row['marker'],
                    'gatherPerk' => $row['gather_perk'],
                    'processPerk' => -1,
                    'tuning' => $row['vehicle_tuning'],
                    'amountItems' => 0,
                    'earning' => 0
                );
            } else {
                if ((strstr($row['item'], "process")) === false) {
                    $space = $row['BbSpace'] + $row['TrunkSpace'];
                    $arr = array(
                        'pid' => $row['pid'],
                        'itemArr' => $row['item'],
                        'time' => $row['to_start'],
                        'vehicle' => $row['vehicle'],
                        'backPack' => $row['backpack'],
                        'space' => $space,
                        'marker' => $row['marker'],
                        'gatherPerk' => $row['gather_perk'],
                        'processPerk' => -1,
                        'tuning' => $row['vehicle_tuning'],
                        'amountItems' => 0,
                        'earning' => 0
                    );
                }
            }
        } elseif ($row['mode'] === "PROCESS") {
            if ($foundProcess === false) {
                $foundProcess = true;

                $arr['processPerk'] = $row['process_perk'];
                $arr['itemArr'] = '' . $arr['itemArr'] . ',' . $row['item'] . '';

                if ($row['vehicle'] != $arr['vehicle']) {
                    $falseFound = true;
                }
            }
        } elseif ($row['mode'] === "SOLD") {
            if ($foundSell === false) {
                $foundSell = true;
                $time = round((($row['end'] - $arr['time']) / 60), 2);
                $arr['time'] = $time;
                $arr['amountItems'] = $row['amount'];
                $arr['earning'] = $row['total_earnings'];
                $arr['itemArr'] = '' . $arr['itemArr'] . ',' . $row['item'] . '';

                foreach ($multiTourenArr as $key2 => $value2) {
                    if ((strstr($row['item'], $value2)) != false) {
                        $falseFound = true;
                    }
                }
                if ($falseFound === false) {
                    $tableArr[] = $arr;
                }
            }
        }
    }
}

$countTableArr = array();
foreach ($tableArr as $key => $value) {
    if (array_key_exists($value['itemArr'], $countTableArr)) {
        $countTableArr[$value['itemArr']] = $countTableArr[$value['itemArr']] + 1;
    } else {
        $countTableArr[$value['itemArr']] = 1;
    }
}

$dataArr = array(
    'tableArr' => $tableArr,
    'countTableArr' => $countTableArr,
);
/*
    apcu_add('data_farming', $dataArr,300);

}*/
?>


<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">
<html>

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
    <link rel="stylesheet" href="vendor/datatables/media/css/dataTables.bootstrap4.css" />

    <!-- Specific Page Vendor CSS -->
    <link rel="stylesheet" href="vendor/pnotify/pnotify.custom.css" />
    <link rel="stylesheet" href="vendor/select2/css/select2.css" />
    <link rel="stylesheet" href="vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
    <!-- Specific Page Vendor CSS -->
    <link rel="stylesheet" href="vendor/morris/morris.css" />
    <link rel="stylesheet" href="vendor/chartist/chartist.min.css" />




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


    <section class="body">
        <!-- include Header-->
        <div class="inner-wrapper">
            <!-- include SideBar-->
            <header class="header">
                <div class="logo-container">
                    <a href="farm-stats.php" class="logo">
                        <img src="img/logo.png" width="75" height="35" alt="Porto Admin" />
                    </a>
                    <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
                        <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
                    </div>
                </div>

            </header>
            <!-- include SideBar-->
            <!-- start: sidebar -->
            <aside id="sidebar-left" class="sidebar-left">

                <div class="sidebar-header">
                    <div class="sidebar-title">
                        Navigation
                    </div>
                    <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
                        <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
                    </div>
                </div>

                <div class="nano">
                    <div class="nano-content">
                        <nav id="menu" class="nav-main" role="navigation">

                            <ul class="nav nav-main">
                                <li>
                                    <a class="nav-link" href="farm-stats.php">
                                        <i class="fas fa-home" aria-hidden="true"></i>
                                        <span>Farm-Stats</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        <hr class="separator" />

                    </div>

                    <script>
                        // Maintain Scroll Position
                        if (typeof localStorage !== 'undefined') {
                            if (localStorage.getItem('sidebar-left-position') !== null) {
                                var initialPosition = localStorage.getItem('sidebar-left-position'),
                                    sidebarLeft = document.querySelector('#sidebar-left .nano-content');

                                sidebarLeft.scrollTop = initialPosition;
                            }
                        }
                    </script>


                </div>

            </aside>
            <!-- end: sidebar -->
            <!-- include SideBar-->
            <section role="main" class="content-body card-margin">
                <header class="page-header">
                    <h2>Farm -Stats</h2>
                </header>
                <!-- start: page -->
                <div class="row">
                    <div class="col">
                        <div class="row">
                            <div class="col-lg-6">
                                <section class="card card-featured-left card-featured-primary mb-4">
                                    <div class="card-body">
                                        <div class="col-lg-12">
                                            <div class="toggle toggle-primary" data-plugin-toggle>
                                                <section class="toggle">
                                                    <label>Erstmal Danke!</label>
                                                    <div class="toggle-content">
                                                        <p>Vorerst einmal Danke an Jeden, der weiterhin an unsere Künste glaubt und uns auch noch unterstützen möchte. Das gesamte Balancing einmal ordentlich zu berechnen bedarf wirklich viel Arbeit und ich wüssste nicht, wie der Kram laufen sollte, wenn Ihr nicht da währt. Vielen, vielen Dank! Und an Alle, die hier den ganzen Tag im TS sitzen und noch keine Tour gefarmt habt. Selbstredend könnt Ihr euch auch melden und das Passwort abholen, um ne Runde zu farmen. Bei dem neuen Farming-System könnte das ein wenig SPaß bereiten :) #MakeEvoGreatAgain</p>
                                                    </div>
                                                </section>
                                                <section class="toggle">
                                                    <label>Funktionsweise - Wie kann ich helfen?</label>
                                                    <div class="toggle-content">
                                                        <p>Wir brauchen Zeiten und weitere Daten für die gesamten Touren. Diese werden automatisiert erhoben. Du musst lediglich auf den Public-Test Server connecten, Dir ein Fahrzeug schnappen und farmen gehen. Wichtig hierbei ist, dass Du zum start jeder Tour dein Auto neu ausparkst, da wir selbstverständlich auch wissen müssen, wie lang man bis zum Feld braucht. Also immer neu ausparken :) </p>
                                                    </div>
                                                </section>
                                                <section class="toggle">
                                                    <label>Welche Touren müssen noch gefarmt werden? Welches Auto</label>
                                                    <div class="toggle-content">
                                                        <p>Ihr seht ja in den Tabellen was schon wie oft gefarmt wurde. Im PRinzip wäre es optimal, wenn wir von jeder Tour mindestens 3 gefarmte Touren mit kleinem, mittleren und großen LKW gefarmt haben. Außerdem ist es natürlich interessant für uns, wie sich das Tuning, so wie die Perks auswirken. Versucht einfach ein wenig abwechslung reinzubringen. </p>
                                                    </div>
                                                </section>
                                                <section class="toggle">
                                                    <label>Helis und Boote noch nicht</label>
                                                    <div class="toggle-content">
                                                        <p>Wie schon in der Überschrift beschrieben sind Helis und Boote vorerst mal egal. Wir haben die Kofferraumplätze dieser Vehicles einfach noch nicht richtig angepasst. Gebt uns ein wenig Zeit, wir werden Euch wissen lassen, wann auch mal ne Helitour gemacht werden kann :)</p>
                                                    </div>
                                                </section>
                                                <section class="toggle">
                                                    <label>Multiprocessing vorerst Egal!</label>
                                                    <div class="toggle-content">
                                                        <p>Wie schon in der Überschrift beschrieben ist Multiprocessing erst mal egal. Es gibt einfach zu viele Faktoren (zu viele verschiedene Möglichkeiten wie man die Route farmt), um jetzt auf die Schnelle eine automatisierte Berechnung zu erstellen. Mister Ted Bundy hat schon die ein oder Andere Multi-Tour gegen gefarmt und wird uns vorerst die Daten der Multi- Routen liefern. </p>
                                                    </div>
                                                </section>
                                                <section class="toggle">
                                                    <label>Bugs reporten</label>
                                                    <div class="toggle-content">
                                                        <p>Selbstredend ist euere Farming-Aktivität nicht nur zur Datenanalyse important. Ebenso wollen wir die Bugs ausmerzen. Vorerst geht es wirklich nur um bugs, die das Farming betreffen. Nicht leer gehende Pflanzen, Nicht funktionierendes farmen usw... Falls Ihr der überzeugung seid einen Bug gefudnen zui haben, der das Farming betrifft, so nutzt bitte einfach den Ingame-Bugtracker. HDGDL </p>
                                                    </div>
                                                </section>
                                                <section class="toggle">
                                                    <label>Daten-Caching</label>
                                                    <div class="toggle-content">
                                                        <p>Die Datenanalyse auf dieser Seite wird für 5 Minuten gecached. Nach beenden eurer Tour müsst ihr also bis zu 5 Minuten warten, bis Ihr seht, ob ihr alles richtig gemacht habt. </p>
                                                    </div>
                                                </section>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div class="col-lg-6">
                                <section class="card">
                                    <header class="card-header">
                                        <div class="card-actions">
                                            <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                                        </div>

                                        <h2 class="card-title">Farm Stats - Counter</h2>
                                    </header>
                                    <div class="card-body">
                                        <table class="table table-responsive-md table-bordered mb-0" id="datatable-userlist2">
                                            <thead>
                                                <tr>
                                                    <th>tour</th>
                                                    <th>counter</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $id = 0;
                                                foreach ($countTableArr as $key => $value) {
                                                    $id = $id + 1;
                                                    echo "<tr>";
                                                    echo "<td>" . $key . "</td>";
                                                    echo "<td>" . $value . "</td>";
                                                    echo "</tr>";
                                                }

                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <section class="card">
                            <header class="card-header">
                                <div class="card-actions">
                                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                                </div>

                                <h2 class="card-title">Farm Stats - List Einfache Routen</h2>
                            </header>
                            <div class="card-body">

                                <table class="table table-responsive-md table-bordered mb-0" id="datatable-userlist">
                                    <thead>
                                        <tr>
                                            <th>pid</th>
                                            <th>itemArr</th>
                                            <th>time</th>
                                            <th>vehicle</th>
                                            <th>backPack</th>
                                            <th>space</th>
                                            <th>marker</th>
                                            <th>gatherPerk</th>
                                            <th>processPerk</th>
                                            <th>amount</th>
                                            <th>earning</th>
                                            <th>tuning</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $id = 0;
                                        foreach ($tableArr as $key => $value) {
                                            $id = $id + 1;
                                            echo "<tr>";
                                            echo "<td>" . $value['pid'] . "</td>";
                                            echo "<td>" . $value['itemArr'] . "</td>";
                                            echo "<td>" . $value['time'] . "</td>";
                                            echo "<td>" . translate($value['vehicle']) . "</td>";
                                            echo "<td>" . translate($value['backPack']) . "</td>";
                                            echo "<td>" . $value['space'] . "</td>";
                                            echo "<td>" . $value['marker'] . "</td>";
                                            echo "<td>" . $value['gatherPerk'] . "</td>";
                                            echo "<td>" . $value['processPerk'] . "</td>";
                                            echo "<td>" . $value['amountItems'] . "</td>";
                                            echo "<td>" . $value['earning'] . "</td>";
                                            echo '<td> 
                                            <a class="modal-with-form mb-1 mt-1 mr-1 btn btn-xs btn-default" href="#MoreInfos' . $id . '">Tuning</a>
                                            
                                            <div id="MoreInfos' . $id . '" class="modal-block modal-block-primary mfp-hide">
                                                <section class="card">
                                                        <div class="card-body text-center">
                                                            <div class="modal-wrapper">
                                                                <div class="modal-icon center">
                                                                </div>
                                                                <div class="modal-text">
                                                                    <p>' . $value["tuning"] . '</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <footer class="card-footer">
                                                            <div class="row">
                                                                <div class="col-md-12 text-right">
                                                                    <button class="btn btn-default modal-dismiss">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </footer>
                                                </section>
                                            </div>
                                            </td>';
                                            echo "</tr>";
                                        }

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


    <!-- Specific Page Vendor -->

    <script src="vendor/jquery/jquery.js"></script>

    <script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
    <script src="vendor/popper/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.js"></script>
    <script src="vendor/nanoscroller/nanoscroller.js"></script>
    <script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
    <script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>


    <!-- Specific Page Vendor -->
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js"></script>
    <script src="vendor/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
    <script src="vendor/jquery-maskedinput/jquery.maskedinput.js"></script>
    <script src="vendor/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
    <script src="vendor/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
    <script src="vendor/fuelux/js/spinner.js"></script>
    <script src="vendor/dropzone/dropzone.js"></script>
    <script src="vendor/select2/js/select2.js"></script>
    <script src="vendor/pnotify/pnotify.custom.js"></script>
    <script src="js/examples/examples.notifications.js"></script>
    <script src="js/examples/examples.modals.js"></script>

    <!-- Theme Base, Components and Settings -->
    <script src="js/theme.js"></script>

    <!-- Theme Custom -->
    <script src="js/custom.js?t=2"></script>

    <!-- Theme Initialization Files -->
    <script src="js/theme.init.js"></script>

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

    <script src="js/examples/examples.datatables.default.js"></script>
    <script src="js/examples/examples.datatables.row.with.details.js"></script>
    <script src="js/examples/examples.datatables.tabletools.js"></script>



    <!-- Vendor -->




    <script>
        $(document).ready(function() {
            $('#datatable-userlist2').dataTable({
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                'dom': '<"row"<"col-lg-5"l><"col-lg-6"f>><"table-responsive"t>p'

            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#datatable-userlist').dataTable({
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                'dom': '<"row"<"col-lg-5"l><"col-lg-6"f>><"table-responsive"t>p'

            });
        });
    </script>


</body>

</body>

</html>