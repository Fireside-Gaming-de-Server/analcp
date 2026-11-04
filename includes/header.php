
<?php

$stmt = $dbl->prepare("SELECT steam_picture FROM acp_users WHERE username=?");
$stmt->bind_param("s",$_COOKIE['username']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$picture = $row[0];

?>
<!-- start: header -->
<header class="header">
    <div class="logo-container">
        <a href="dashboard.php" class="logo">
            <span class="fs-logo"><img src="img/fireside-logo.png" width="2048" height="2048" alt="Fireside Gaming"></span>
        </a>
        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <span class="separator"></span>
        <form class="search nav-form" method="post" autocomplete="off">
            <div class="input-group">
                <input type="text" class="form-control" name="TextSearch" id="TextSearchID" value="" placeholder="Spielername oder Steam-ID suchen …" aria-label="Spieler suchen">
                <span class="input-group-append">
                    <button class="btn btn-default" type="submit" aria-label="Suche starten"><i class="fas fa-search"></i></button>
                </span>
            </div>
        </form>
    <span class="separator"></span>
        <button type="button" id="non-blocking-dark2" aria-label="Suchhilfe anzeigen" title="Suchhilfe anzeigen" class="mb-1 mt-1 mr-1 btn btn-sm btn-default"><i class="fa fa-arrow-left"></i><i class="fa fa-question"></i></button>
    <div class="header-right">
        <span class="separator"></span>
        <div id="userbox" class="userbox">
            <a href="#" data-toggle="dropdown">
                <figure class="profile-picture">
                    <img src=<?php echo ''. $picture .''?> alt="Profilbild" class="rounded-circle" data-lock-picture="img/!logged-user.jpg" />
                </figure>
                <div class="profile-info" data-lock-name="John Doe" data-lock-email="johndoe@okler.com">
                    <span class="name"><?php echo ''. $_COOKIE['username'] .''?></span>
                    <span class="role"><?php echo ''. $_COOKIE['usergroup'] .''?></span>
                </div>
                <i class="fa custom-caret"></i>
            </a>
            <div class="dropdown-menu">
                <ul class="list-unstyled mb-2">
                    <li class="divider"></li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="logout.php"><i class="fas fa-power-off"></i> Abmelden</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    <!-- end: search & user box -->
</header>


<!-- end: header -->