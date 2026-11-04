<?php
include 'includes/checkOnline.php';



if (isset($_POST["PatchSend"])) {

    if ($_SESSION["rightsArr"]["edit_PatchNotest"] == true) {

        $content = $_POST["Infos"];

        $stmt = $dbl->prepare("INSERT INTO patch_notes (content) VALUES (?)");
        $stmt->bind_param("s", $content);
        $stmt->execute();

        // webhook("https://discord.com/api/webhooks/1029080181207470131/6ahVQQ4UBTcIuXorfp_GozsDRmEzfim9dlHG5lodguViBVkUtVhPKGMXqYisl93FFwbL");
        // #webhook("https://discord.com/api/webhooks/1025429542636114001/Nkw26_OHEW--f6lLWFm29Fz-gZA0yreYGHzGo4qaOSBJ78CA79qe4fcZfLOVOubLg3rH");
    }
}

function webhook($webhookurl)
{

    #Check 
    $thumbnail = "https://acp.fireside-gaming.de/img/PatchNotes.png";
    $thumbnail = str_ireplace("{width}x{height}", '1920x1080', $thumbnail);
    $timestamp = date("c", strtotime("now"));

    $json_data = json_encode([


        "tts" => false,
        "content" => "@everyone",
        "embeds" => [
            [
                // Embed Title
                "title" => "PatchNotes!",

                // Embed Type
                "type" => "rich",

                // Embed Description
                "description" => "Wieder was neues",

                // Timestamp of embed must be formatted as ISO8601
                "timestamp" => $timestamp,

                // Embed left border color in HEX
                "color" => hexdec("24db4b"),

                "image" => [
                    "url" => $thumbnail
                ],

                // Additional Fields array
                "fields" => [
                    [
                        "name" => "Ab zu den PatchNotes",
                        "value" => "https://fireside-gaming.de/PatchNotes.php",
                        "inline" => true
                    ]
                    // Etc..
                ]
            ]
        ]

    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


    $ch = curl_init($webhookurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);
    // If you need to debug, or find out why you can't send message uncomment line below, and execute script.
    // echo $response;
    curl_close($ch);
}



?>
<!doctype html>
<html class="fixed dark fixed sidebar-left-collapsed sidebar-left-with-menu">

<head>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Patch Notes - Editor </title>
    <meta name="keywords" content="Patch" />
    <meta name="description" content="Patch">
    <meta name="author" content="Klausi">

    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- Web Fonts  -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="vendor/animate/animate.compat.css">
    <link rel="stylesheet" href="vendor/font-awesome/css/all.css" />
    <link rel="stylesheet" href="vendor/boxicons/css/boxicons.min.css" />
    <link rel="stylesheet" href="vendor/magnific-popup/magnific-popup.css" />
    <link rel="stylesheet" href="vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css" />
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.theme.css" />
    <link rel="stylesheet" href="vendor/select2/css/select2.css" />
    <link rel="stylesheet" href="vendor/select2-bootstrap-theme/select2-bootstrap.min.css" />
    <link rel="stylesheet" href="vendor/bootstrap-multiselect/css/bootstrap-multiselect.css" />
    <link rel="stylesheet" href="vendor/bootstrap-tagsinput/bootstrap-tagsinput.css" />
    <link rel="stylesheet" href="vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css" />
    <link rel="stylesheet" href="vendor/bootstrap-timepicker/css/bootstrap-timepicker.css" />
    <link rel="stylesheet" href="vendor/dropzone/basic.css" />
    <link rel="stylesheet" href="vendor/dropzone/dropzone.css" />
    <link rel="stylesheet" href="vendor/bootstrap-markdown/css/bootstrap-markdown.min.css" />
    <link rel="stylesheet" href="vendor/summernote/summernote-bs4.css" />
    <link rel="stylesheet" href="vendor/codemirror/lib/codemirror.css" />
    <link rel="stylesheet" href="vendor/codemirror/theme/monokai.css" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="css/theme.css" />

    <!-- Skin CSS -->
    <link rel="stylesheet" href="css/skins/default.css" />

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="css/custom.css">

    <!-- Head Libs -->
    <script src="vendor/modernizr/modernizr.js"></script>


    <link rel="stylesheet" href="vendor/animate/animate.css">
    <link rel="stylesheet" href="vendor/morris/morris.css" />
    <link rel="stylesheet" href="vendor/datatables/media/css/dataTables.bootstrap4.css" />
    <link rel="stylesheet" href="vendor/pnotify/pnotify.custom.css" />


    <style>
        p {
            margin: 0px !important;
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

        <?php
        include 'includes/header.php';
        ?>

        <div class="inner-wrapper">

            <?php
            include 'includes/sideBar.php';
            ?>

            <section role="main" class="content-body">
                <header class="page-header">
                    <h2> Patch Notes - Editor </h2>

                </header>

                <!-- start: page -->


                <div class="row">
    <div class="col-lg-6 col-xl-12">
        <div class="row">

            <div class="col-lg-12 col-xl-6">
                <div class="row">
                    <div class="col-lg-12 col-xl-12">
                        <section class="card card-featured-left card-featured-primary mb-4">
                            <header class="card-header">
                                <h3 class="card-title text-dark">Editor</h3>
                            </header>
                            <div class="card-actions">
                                <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                            </div>
                            <div class="card-body">
                                <div class="tabs tabs-primary">
                                    <ul class="nav nav-tabs">
                                    </ul>
                                    <div class="tab-content">

                                        <!-- TimeLine -->

                                        <div id="timeline"></div>

                                        <div class="row">
                                            <div class="col-lg-12 col-xl-12">
                                                <button type="button" class="btn btn-success btn-sm mb-2" onclick="addTimeline('Added')">Added</button>
                                                <button type="button" class="btn btn-warning btn-sm mb-2" onclick="addTimeline('Edit')">Edit</button>
                                                <button type="button" class="btn btn-danger btn-sm mb-2" onclick="addTimeline('Removed')">Removed</button>
                                            </div>
                                        </div>

                                        <script>
                                            function addTimeline(action) {
                                                var timelineDiv = document.getElementById('timeline');
                                                var rowDiv = document.createElement('div');
                                                rowDiv.className = 'row';
                                                var colDiv = document.createElement('div');
                                                colDiv.className = 'col-lg-12 col-xl-12';
                                                var inputDiv = document.createElement('div');
                                                inputDiv.className = 'form-group';
                                                var textarea = document.createElement('textarea');
                                                textarea.className = 'form-control';
                                                textarea.name = 'timelineDescription';
                                                textarea.placeholder = 'Beschreiben Sie, was gemacht wurde';
                                                inputDiv.appendChild(textarea);
                                                colDiv.appendChild(inputDiv);
                                                rowDiv.appendChild(colDiv);
                                                timelineDiv.appendChild(rowDiv);
                                            }
                                        </script>

                                        <form method="post" name="FormPost" class="form-horizontal form-bordered">
                                            <input name="Infos" id="Infos" hidden>
                                            <?php
                                            if ($_SESSION["rightsArr"]["edit_PatchNotest"] == true) {
                                                echo '<button type="submit" id="PatchSend" name="PatchSend" class="btn btn-primary">Senden</button>';
                                            }
                                            ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






                            <div class="col-lg-12 col-xl-6">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12">
                                        <section class="card card-featured-left card-featured-primary mb-4">
                                            <header class="card-header">
                                                <h3 class="card-title text-dark">Vorschau</h3>
                                            </header>
                                            <div class="card-actions">
                                                <a href="#" class="card-action card-action-toggle" data-card-toggle></a>
                                            </div>
                                            <div class="card-body">
                                                <div class="tabs tabs-primary">
                                                    <ul class="nav nav-tabs">
                                                    </ul>
                                                    <div class="tab-content">


                                                        <!-- TimeLine -->


                                                        <?php


                                                        echo '<div class="timeline">
                                                                <div class="tm-body">';

                                                        echo '<ol class="tm-items">
                                                                <li>
                                                                    <div class="tm-info">
                                                                        <div class="tm-icon"><i class="' . "fas fa-info" . '"></i></div>
                                                                        <time class="tm-datetime" datetime="' . "Test <br>423" . '">
                                                                            <div class="tm-datetime-date">' . "Dummy" . '</div>
                                                                            <div class="tm-datetime-time">' . "Dummy" . '</div>
                                                                        </time>
                                                                    </div>
                                                                    <div class="tm-box appear-animation" data-appear-animation="fadeInRight" data-appear-animation-delay="' . "100" . '">
                                                                        <blockquote class="primary" id="dummy">
                                                                            ' . '
                                                                        </blockquote>
                                                                    </div>
                                                                </li>
                                                                </ol>';


                                                        $time = 50;
                                                        $stmt = $dbl->prepare("SELECT * FROM patch_notes ORDER BY insert_time DESC");
                                                        $stmt->execute();
                                                        $result = $stmt->get_result();
                                                        while ($row = mysqli_fetch_array($result)) {




                                                            $dateValue = strtotime($row["insert_time"]);
                                                            $date = date("m", $dateValue);
                                                            $Year = date("Y", $dateValue);
                                                            $Stunde = date("H:i", $dateValue);
                                                            $Datum = date("d.m.Y", $dateValue);

                                                            $dateObj   = DateTime::createFromFormat('!m', $date);
                                                            $monthName = $dateObj->format('F');


                                                            if ($Last != $monthName) {

                                                                echo '<div class="tm-title">
                                                                    <h5 class="m-0 pt-2 pb-2 text-dark font-weight-semibold text-uppercase">' . $monthName . "  " .  $Year . '</h5>
                                                                </div>';
                                                            }

                                                            echo '<ol class="tm-items">
                                                                    <li>
                                                                        <div class="tm-info">
                                                                            <div class="tm-icon"><i class="' . $row["icon"] . '"></i></div>
                                                                            <time class="tm-datetime" datetime="' . $row["insert_time"] . '">
                                                                                <div class="tm-datetime-date">' . $Datum . '</div>
                                                                                <div class="tm-datetime-time">' . $Stunde . '</div>
                                                                            </time>
                                                                        </div>
                                                                        <div class="tm-box appear-animation" data-appear-animation="fadeInRight" data-appear-animation-delay="' . $time . '">
                                                                            <blockquote class="primary">
                                                                                ' . $row["content"] . '
                                                                            </blockquote>
                                                                        </div>
                                                                    </li>
                                                                </ol>';


                                                            $Last = $monthName;
                                                        }


                                                        echo '</div>
                                                        </div>';


                                                        ?>




                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>








                <!-- end: page -->
            </section>
        </div>

    </section>

    <!-- Vendor -->
    <script src="vendor/jquery/jquery.js"></script>


    <script>
        $(document).ready(function() {


            setInterval(function() {
                info = $('.summernote').summernote('code');

                if (info != null) {
                    $("#dummy").html(info);
                }
            }, 2500);

            $("#PatchSend").click(function() {
                $("#Infos").val($('.summernote').summernote('code'));
            });


        });
    </script>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>


    <script src="vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
    <script src="vendor/popper/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="vendor/common/common.js"></script>
    <script src="vendor/nanoscroller/nanoscroller.js"></script>
    <script src="vendor/magnific-popup/jquery.magnific-popup.js"></script>
    <script src="vendor/jquery-placeholder/jquery.placeholder.js"></script>

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

    <script src="vendor/jquery-appear/jquery.appear.js"></script>

    <!-- Theme Base, Components and Settings -->
    <script src="js/theme.js"></script>

    <!-- Theme Custom -->
    <script src="js/custom.js"></script>

    <!-- Theme Initialization Files -->
    <script src="js/theme.init.js"></script>

    <!-- Examples -->

</body>

</html>