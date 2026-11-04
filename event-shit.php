<?php

include 'includes/checkOnline.php';
include 'includes/Config_vItems.php';
include 'includes/Weapons.php';
include 'includes/Attachments.php';
include 'includes/Uniform.php';

if ($_SESSION["rightsArr"]["event_maker"] == false) {
    header("Location: dashboard.php");
}

$options = "";
$magsOps = "";
foreach ($Weapons as $item => $value) {
    if ($value["Class"] != null) {
        if ($value["Magazines"] != null) {


            $options .= '<option id="' . $item . '" category="' . $item . '" value="' . $value["Class"] . '">' . $value["Name"] . '</option>';

            if ((isset($_POST["Weapons"])) && ($_POST["Weapons"] == $value["Class"])) {

                $Magazines = explode(",", $value["Magazines"]);
                foreach ($Magazines as $Mag => $MagVal) {
                    $magsOps .= '<option category="' . $item . '" value="' . $MagVal  . '">' . $MagVal . '</option>';
                }

                if (strpos($value["Accessories"], "muzzle")) {
                    $selected = $value["Accessories"];
                }
            }
        }
    }

    $item = null;
    $MagVal = null;
    $Magazines = null;
    $Schali = null;
}

$Schali = "";
$optic = "";
$Sonstiges = "";
foreach ($Attachments as $i => $v) {
    if ($v["Class"] != null) {


        if (strpos($v["Class"], "muzzle") !== false) {
            if (isset($selected)) {
                if (strpos($selected, $v["Class"])) {
                    $getting = "selected";
                }
            }
            $Schali .= '<option  value="' . $v["Class"] . '"' . $getting . '>' . $v["Name"]  . '</option>';
        } elseif (strpos($v["Class"], "optic") !== false) {
            $optic .= '<option  value="' . $v["Class"] . '">' . $v["Name"]  . '</option>';
        } else {
            $Sonstiges .= '<option value="' . $v["Class"] . '">' . $v["Name"]  . '</option>';
        }
    }

    $i = null;
    $v = null;
    $getting = "";
}


$Uniforms = "";
$Vests = "";
$hats = "";
$backpacks = "";
foreach ($Uniform as $i => $v) {
    if ($v["Class"] != null) {

        if (strpos($v["Class"], "U_") !== false) {
            $Uniforms .= '<option  value="' . $v["Class"] . '">' . $v["Name"]  . '</option>';
        } elseif (strpos($v["Class"], "V_") !== false) {
            $Vests .= '<option  value="' . $v["Class"] . '">' . $v["Name"]  . '</option>';
        } elseif (strpos($v["Class"], "H_") !== false) {
            $hats .= '<option value="' . $v["Class"] . '">' . $v["Name"]  . '</option>';
        }
    }

    $I = null;
    $V = null;
    $getting = "";
}


$backpacks .= '<option  value="B_Bergen_mcamo_F">Bergen (Unsichtbar)</option>';
$backpacks .= '<option  value="B_LegStrapBag_black_F">Leg Bag</option>';
$backpacks .= '<option  value="B_ViperHarness_blk_F">Viper Harness</option>';

$Loadouts = "";
$LoadoutsArr = array();
$stmt = $dbup->prepare("SELECT * FROM event_gear");
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $Loadouts .= '<option  value="' . $row["id"]  . '">' . $row["Name"] . '</option>';

    $LoadoutsArr[$row["id"]] = $row["gear_arr"];
}





if (isset($_POST["SubmitEventGear"])) {

    $exit = false;

    if ((!isset($_POST["Weapons"])) || ($_POST["Weapons"] == "")) {
        $exit = true;
    } else {
        $Weapon = $_POST["Weapons"];
    }


    if (isset($_POST["bipod"])) {
        $bipod = $_POST["bipod"];
    }
    if (isset($_POST["radio"])) {
        $ItemRadio = $_POST["radio"];
    }
    if (isset($_POST["Mags"])) {
        $Mag = $_POST["Mags"];
        $count = $_POST["MagsCount"];

        $magsCount = '["' . $Mag . '",' . $count . ', 1]';
    }
    if (isset($_POST["Schali"])) {
        $sil = $_POST["Schali"];
    }
    if (isset($_POST["VestSubmit"])  && $_POST["VestSubmit"] != "") {
        $vest = $_POST["VestSubmit"];
    } else {
        $vest = "V_PlateCarrier2_blk";
    }
    if (isset($_POST["backpackSubmit"]) && $_POST["backpackSubmit"] != "") {
        $backpack = $_POST["backpackSubmit"];
    } else {
        $backpack = "B_Bergen_mcamo_F";
    }
    if (isset($_POST["UniformsSubmit"])  && $_POST["UniformsSubmit"] != "") {
        $Uniform = $_POST["UniformsSubmit"];
    } else {
        $Uniform = "U_O_Wetsuit";
    }
    if (isset($_POST["HatsSubmit"])) {
        $helm = $_POST["HatsSubmit"];
    }
    if (isset($_POST["scopeSubmit"])) {
        $scope = $_POST["scopeSubmit"];
    }

    if ($exit == false) {
        $giveString = '[[["' . $Weapon . '","' . $sil . '","","' . $scope . '",[],[],"' . $bipod . '"],[],[],["' . $Uniform . '",[["FirstAidKit",1]]],["' . $vest . '",[]],["' . $backpack . '",[' . $magsCount . ']],"' . $helm . '","",[],["ItemMap","ItemGPS","' . $ItemRadio . '","ItemCompass","ItemWatch",""]],[["redgull",15],["tbacon",2],["toolkit",2],["verbandspaeckchen",15],["morphium",7]]]';
        $pid = $_SESSION["Event_userPID"];

        $typeAcp = '"GearErsetzen"';
        $stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value) VALUES (?,?,?)");
        $stmt->bind_param("sss", $pid, $typeAcp, $giveString);
        $stmt->execute();


        $Name = "Waffe:" . $Weapon . "<br>Uniform:" .  $Uniform . "<br>";


        $stmt = $dbup->prepare("INSERT INTO event_gear (Name,gear_arr,erstellt_von) VALUES (?,?,?)");
        $stmt->bind_param("sss", $Name, $giveString, $pid);
        $stmt->execute();
        print_r($stmt);
    }
}

if (isset($_POST["LoadoutsGiveSubmit"]) && isset($_POST["SubmitEventGear2"])) {

    $giveString  = $LoadoutsArr[$_POST["LoadoutsGiveSubmit"]];
    $pid = $_SESSION["Event_userPID"];

    $typeAcp = '"GearErsetzen"';
    $stmt = $dbm->prepare("INSERT INTO acpqueue (toPid,type,value) VALUES (?,?,?)");
    $stmt->bind_param("sss", $pid, $typeAcp, $giveString);
    $stmt->execute();
}




?>

<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">

<head>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Event - Shit</title>
    <meta name="keywords" content="Event" />
    <meta name="description" content="Trinity Life">
    <meta name="author" content="klausi♥>

		<!-- Mobile Metas -->
		<meta name='viewport' content=" width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- Web Fonts  -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:100,300,400,600,800,900|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/animate/animate.css">

    <link rel="stylesheet" href="vendor/font-awesome/css/all.css" />
    <link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" />
    <link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />

    <!-- Specific Page Vendor CSS -->
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.theme.css" />
    <link rel="stylesheet" href="vendor/bootstrap-multiselect/css/bootstrap-multiselect.css" />
    <link rel="stylesheet" href="vendor/morris/morris.css" />
    <link rel="stylesheet" href="vendor/select2/css/select2.css" />
    <link rel="stylesheet" href="vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
    <link rel="stylesheet" href="vendor/datatables/media/css/dataTables.bootstrap4.css" />

    <link rel="stylesheet" href="vendor/pnotify/pnotify.custom.css" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="css/theme.css" />

    <!-- Skin CSS -->
    <link rel="stylesheet" href="css/skins/default.css" />

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">

    <!-- Head Libs -->
    <script src="vendor/modernizr/modernizr.js"></script>


    <style>
        #start {
            margin: 40px auto;
            text-align: center;
            color: #DAD6D6;
            background: #48633B;
            border: solid 2px #35492C;
            border-radius: 5px;
            padding: 16px 40px 16px;
            letter-spacing: 2px;
            cursor: pointer;
        }

        #stop {
            margin: 40px 5px auto;
            text-align: ;
            color: #DAD6D6;
            background: #721329;
            border: solid 2px #590F20;
            border-radius: 5px;
            padding: 16px 40px 16px;
            letter-spacing: 2px;
            cursor: pointer;
        }

        #clear {
            margin: 40px auto;
            text-align: center;
            color: #DAD6D6;
            background: #564E58;
            border: solid 2px #3E383F;
            border-radius: 5px;
            padding: 16px 40px 16px;
            letter-spacing: 2px;
            cursor: pointer;
        }

        h2 {
            font-size: 46px;
            letter-spacing: 2px;
        }
    </style>


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
        <?php
        include 'includes/header.php';
        ?>
        <!-- include Header-->
        <div class="inner-wrapper">
            <!-- include SideBar-->
            <?php
            include 'includes/sideBar.php';
            ?>
            <!-- include SideBar-->

            <section role="main" class="content-body card-margin">
                <header class="page-header">
                    <h2>Spieler Infos</h2>

                </header>
                <!-- start: page -->


                <div class="row">
                    <div class="col-lg-12 col-xl-12">
                        <section class="card">
                            <header class="card-header">
                                <div class="row">
                                    <div class="col-lg-6 col-xl-12">
                                        <div class="row">
                                            <?php print_r($giveString); ?>



                                            <?php

                                            if ($exit) {
                                                echo
                                                '<div class="col-lg-6 col-xl-12 alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Oh NO!</strong> Da hat was nicht so Funktioniert wie es sollte versuch es einfach nochmal.
                                                </div>';
                                            }


                                            ?>


                                        </div>
                                    </div>
                                </div>
                            </header>
                        </section>
                    </div>
                </div>








                <div class="row">
                    <div class="col-lg-6">
                        <section class="card card-featured-left card-featured-primary mb-4">
                            <header class="card-header">
                                <h3 class="card-title text-dark center">Information</h3>
                            </header>
                            <div class="card-actions">
                                <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                            </div>
                            <div class="card-body">
                                <div class="tabs tabs-vertical tabs-right tabs-primary">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item ">
                                            <a class="nav-link text-dark" href="#eventAktion" data-toggle="tab">Event Aktionen</a>
                                        </li>
                                        <li class="nav-item active">
                                            <a class="nav-link text-dark" href="#event_maker" data-toggle="tab">Event Leiter Stuff</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-dark" href="#Loadouts" data-toggle="tab">Loadouts</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="Loadouts" class="tab-pane">
                                            <form name="LoadoutsGive" id="LoadoutsGive" method="POST">
                                                <div class="col-lg-6 col-xl-12">
                                                    <div class="row">


                                                        <div class="col-sm-12 mb-6">
                                                            <label class="col-lg-6 control-label  pt-2">Loadouts</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="LoadoutsGiveSubmit" name="LoadoutsGiveSubmit">
                                                                <?php
                                                                echo '<option value="">Keine Auswahl</option>';
                                                                echo $Loadouts
                                                                ?>
                                                            </select>
                                                        </div>


                                                        <div class="col-md-12 text-right">
                                                            <button style="margin-top: 10px;" type="submit" id="SubmitEventGear2" name="SubmitEventGear2" class="btn btn-primary">Gear geben</button>
                                                        </div>


                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div id="eventAktion" class="tab-pane">
                                            <div class="col-lg-6 col-xl-12">
                                                <div class="row">
                                                    <button id="start">START</button>
                                                    <form method="POST">

                                                        <button id="stop" type="submit">STOP</button>
                                                    </form>

                                                    <button id="clear">RESET</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="event_maker" class="tab-pane active">
                                            <form name="addWep" id="addWep" method="POST">
                                                <div class="col-lg-6 col-xl-12">
                                                    <div class="row">


                                                        <!-- Tab2 Infos -->



                                                        <div class="col-sm-12 mb-6">
                                                            <label class="col-lg-6 control-label  pt-2">Weapons</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="Weapons" name="Weapons">
                                                                <?php
                                                                echo '<option value="">Keine Auswahl</option>';
                                                                if ($_POST["Weapons"]) {
                                                                    echo '<option value="' . $_POST["Weapons"] . '"selected>' . $_POST["Weapons"] . '</option>';
                                                                }
                                                                echo $options ?>
                                                            </select>
                                                        </div>


                                                        <div class="col-sm-12 mb-6">

                                                            <label class="col-lg-6 control-label  pt-2">Mags</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="Mags" name="Mags">
                                                                <?php echo $magsOps
                                                                ?>
                                                            </select>


                                                            <label class="col-lg-6 control-label  pt-2">Mags Count</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="MagsCount" name="MagsCount">
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="5">5</option>
                                                                <option value="6">6</option>
                                                                <option value="7" selected>7</option>
                                                                <option value="8">8</option>
                                                                <option value="9">9</option>
                                                                <option value="10">10</option>
                                                                <option value="11">11</option>
                                                                <option value="12">12</option>
                                                                <option value="13">13</option>
                                                                <option value="14">14</option>
                                                                <option value="15">15</option>
                                                            </select>

                                                        </div>



                                                        <div class="col-sm-6 mb-3">
                                                            <label class="col-lg-6 control-label  pt-2">Schalldämpfer</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="Schali" name="Schali">
                                                                <?php
                                                                echo '<option value="" selected>Keine Auswahl</option>';
                                                                echo $Schali ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-6 mb-3">
                                                            <label class="col-lg-6 control-label  pt-2">Scopes</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="scopeSubmit" name="scopeSubmit">
                                                                <?php
                                                                echo '<option value="" selected>Keine Auswahl</option>';
                                                                echo $optic ?>
                                                            </select>
                                                        </div>
                                                        <br>




                                                        <div class="col-sm-6 mb-3">
                                                            <label class="col-lg-6 control-label  pt-2">Uniforms</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="UniformsSubmit" name="UniformsSubmit">
                                                                <?php
                                                                echo '<option value="" selected>Keine Auswahl</option>';
                                                                echo $Uniforms ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-6 mb-3">
                                                            <label class="col-lg-6 control-label  pt-2">Backpacks</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="backpackSubmit" name="backpackSubmit">
                                                                <?php
                                                                echo '<option value="" selected>Keine Auswahl</option>';
                                                                echo $backpacks ?>
                                                            </select>
                                                        </div>





                                                        <div class="col-sm-6 mb-3">
                                                            <label class="col-lg-6 control-label  pt-2">Hats</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="HatsSubmit" name="HatsSubmit">
                                                                <?php
                                                                echo '<option value="" selected>Keine Auswahl</option>';
                                                                echo $hats ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-sm-6 mb-3">
                                                            <label class="col-lg-6 control-label  pt-2">Vesten</label>
                                                            <select data-plugin-selectTwo class="form-control populate placeholder input-rounded" id="VestSubmit" name="VestSubmit">
                                                                <?php
                                                                echo '<option value="" selected>Keine Auswahl</option>';
                                                                echo $Vests ?>
                                                            </select>
                                                        </div>


















                                                        <div class="col-sm-9" style="margin-top: 10px;">
                                                            <div class="checkbox-custom chekbox-primary">
                                                                <input id="for-project" value="bipod_02_F_hex" type="checkbox" name="bipod">
                                                                <label for="for-project">Zweibein(Bipod(Hex)CSAT)</label>
                                                            </div>
                                                            <div class="checkbox-custom chekbox-primary">
                                                                <input id="for-website" value="ItemRadio" type="checkbox" name="radio">
                                                                <label for="for-website">Mit Radio für Hexagons?</label>
                                                            </div>
                                                        </div>
                                                        <br>

                                                        <div class="col-md-12 text-right">
                                                            <button style="margin-top: 10px;" type="submit" id="SubmitEventGear" name="SubmitEventGear" class="btn btn-primary">Speichern & Gear geben</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>



                    <div class="col-lg-12 col-xl-6">
                        <div class="row">
                            <div class="col-lg-12 col-xl-12">
                                <section class="card card-featured-left card-featured-primary mb-4">
                                    <header class="card-header">
                                        <h3 class="card-title text-dark">Stopwatch</h3>
                                    </header>
                                    <div class="card-actions">
                                        <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                                    </div>
                                    <div class="card-body">
                                        <div class="tabs tabs-primary">
                                            <ul class="nav nav-tabs">
                                                <li class="nav-item active">
                                                    <a class="nav-link text-dark" href="#somethingImportant" data-toggle="tab">Schaumal Hier</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div id="somethingImportant" class="tab-pane active">
                                                    <div class="col-lg-6 col-xl-12">
                                                        <h2 style="text-align: center;"><time>00:00:00</time></h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </section>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="row">
                    <div class="col-lg-12">
                        <section class="card">
                            <header class="card-header">
                                <div class="card-actions">
                                    <a id="ListenOnExample" href="#" class="card-action fas fa-sync" ic-trigger-on="load" ic-get-from="includes/ranking.php" ic-poll="60s" ic-target="#ExampleRefreshTarget"></a>
                                    <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                                </div>

                                <h2 class="card-title">Ranking</h2>
                            </header>
                            <div id="ExampleRefreshTarget" class="card-body" data-loading-overlay data-loading-overlay-options='{ "listenOn": "#ListenOnExample" }' style="min-height: 150px;">
                            </div>
                        </section>
                    </div>
                </div>




        </div>
    </section>


    <!-- end: page -->

    <!-- Vendor -->
    <script src="vendor/jquery/jquery.js"></script>
    <script src="vendor/pnotify/pnotify.custom.js"></script>
    <script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
    <script src="vendor/popper/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.js"></script>
    <script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="vendor/common/common.js"></script>
    <script src="vendor/nanoscroller/nanoscroller.js"></script>
    <script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
    <script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>
    <script src="vendor/intercooler-js/intercooler.js"></script>

    <!-- Specific Page Vendor -->
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="vendor/jqueryui-touch-punch/jquery.ui.touch-punch.js"></script>
    <script src="vendor/jquery-appear/jquery.appear.js"></script>
    <script src="vendor/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
    <script src="vendor/jquery.easy-pie-chart/jquery.easypiechart.js"></script>
    <script src="vendor/flot/jquery.flot.js"></script>
    <script src="vendor/flot.tooltip/jquery.flot.tooltip.js"></script>
    <script src="vendor/flot/jquery.flot.pie.js"></script>
    <script src="vendor/flot/jquery.flot.categories.js"></script>
    <script src="vendor/flot/jquery.flot.resize.js"></script>
    <script src="vendor/jquery-sparkline/jquery.sparkline.js"></script>
    <script src="vendor/raphael/raphael.js"></script>
    <script src="vendor/morris/morris.js"></script>
    <script src="vendor/gauge/gauge.js"></script>
    <script src="vendor/snap.svg/snap.svg.js"></script>
    <script src="vendor/liquid-meter/liquid.meter.js"></script>
    <script src="vendor/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/media/js/dataTables.bootstrap4.min.js"></script>
    <script src="vendor/ios7-switch/ios7-switch.js"></script>

    <script src="vendor/select2/js/select2.js"></script>
    <script src="vendor/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
    <script src="vendor/jquery-mockjax/jquery.mockjax.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Theme Base, Components and Settings -->
    <script src="js/theme.js"></script>
    <!-- Theme Custom -->
    <script src="js/custom.js"></script>

    <!-- Theme Initialization Files -->
    <script src="js/theme.init.js"></script>


    <!-- Examples -->

    <script src="js/examples/examples.header.menu.js"></script>
    <script src="js/examples/examples.dashboard.js"></script>
    <script src="js/examples/examples.notifications.js"></script>
    <script src="js/examples/examples.ajax.made.easy.js"></script>
    <script src="js/examples/examples.modals.js"></script>











    <script>
        $(document).ready(function() {
            $(' #datatable-Player').dataTable({
                "pageLength": 15,
                "lengthMenu": [
                    [10, 15, 25, 50],
                    [10, 15, 25, 50]
                ],
                'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $('#datatable-Note').dataTable({
                "pageLength": 25,
                "lengthMenu": [
                    [10, 25, 50, 100, 150],
                    [10, 25, 50, 100, 150]
                ],
                'dom': '<"row"<"col-lg-6"l><"col-lg-6"f>><"table-responsive"t>p'
            });
        });
    </script>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <script>
        var h1 = document.getElementsByTagName('time')[0],
            start = document.getElementById('start'),
            stop = document.getElementById('stop'),
            clear = document.getElementById('clear'),
            seconds = <?php if (isset($_COOKIE["seconds"])) {
                            echo $_COOKIE["seconds"];
                        } else {
                            echo "0";
                        } ?>,
            minutes = <?php if (isset($_COOKIE["minutes"])) {
                            echo $_COOKIE["minutes"];
                        } else {
                            echo "0";
                        } ?>,
            hours = <?php if (isset($_COOKIE["hours"])) {
                        echo $_COOKIE["hours"];
                    } else {
                        echo "0";
                    } ?>,
            t;

        document.cookie = "seconds=" + seconds
        document.cookie = "minutes=" + minutes
        document.cookie = "hours=" + hours

        <?php if (isset($_COOKIE["seconds"]) && $_COOKIE["seconds"] != 0) {
            echo "timer();";
        }
        ?>


        function add() {
            $("#start").prop("disabled", true);
            seconds++;
            if (seconds >= 60) {
                seconds = 0;
                minutes++;
                if (minutes >= 60) {
                    minutes = 0;
                    hours++;
                }
            }

            h1.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);

            timer();
            document.cookie = "seconds=" + seconds
            document.cookie = "minutes=" + minutes
            document.cookie = "hours=" + hours

        }



        function timer() {
            t = setTimeout(add, 1000);
        }


        /* Start button */
        start.onclick = function() {
            document.cookie = "StartEvent=" + (new Date().toISOString());
            timer();
        }

        /* Stop button */
        stop.onclick = function() {
            clearTimeout(t);
            document.cookie = "StopEvent=" + (new Date().toISOString());
            document.cookie = "seconds=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
            document.cookie = "minutes=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
            document.cookie = "hours=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";

        }

        /* Clear button */
        clear.onclick = function() {
            h1.textContent = "00:00:00";
            seconds = 0;
            minutes = 0;
            hours = 0;

            document.cookie = "seconds=" + seconds
            document.cookie = "minutes=" + minutes
            document.cookie = "hours=" + hours

            document.cookie = "StartEvent=" + (new Date().toISOString());
            document.cookie = "StopEvent=" + "0";

        }
        $('#Weapons').on('change', function() {
            $("#addWep").submit();
        });
    </script>

</body>

</html>

<?php
$dbm->close();
?>