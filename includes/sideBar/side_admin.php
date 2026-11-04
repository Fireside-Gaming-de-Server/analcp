<?php
    require ("includes/mysql.php");

	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $_COOKIE["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID = $row[5];

    $permission_name = "adminUserlist";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID_check = $row[2];
    $adminUserlist = json_decode($groupID_check);
  

    $permission_name = "adminUserGroups";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID_check = $row[2];
    $adminUserGroups = json_decode($groupID_check);
    

    $permission_name = "adminMessageAllPlayers";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID_check = $row[2];
    $adminUserGroups = json_decode($groupID_check);

/*
    if(false) {

        echo '
            <li class="nav-parent">
                <a class="nav-link" href="#">
                    <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                    <span>Admin-Panel</span>
                </a>
                <ul class="nav nav-children">
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            User und Rechte
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="userlist.php">
                                    Userliste
                                </a>
                            </li>
                            <li>
                                <a class="nav-link" href="usergroups.php">
                                    Usergruppen
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>';
    }

*/

    if ((in_array($groupID, $adminUserlist)) or (in_array($groupID, $adminUserGroups))) {
        echo ' 
            <li class="nav-parent">
                <a class="nav-link" href="#">
                    <i class="fa fa-cogs" aria-hidden="true"></i>
                    <span>Admin-Panel</span>
                </a>
                <ul class="nav nav-children">
                ';
    }

    if (in_array($groupID, $adminUserGroups)) { // Admin Message to All Players
        echo ' 
            <li>
                 <a class="nav-link" href="adminMessageAll.php">
                    Admin Nachricht [Alle]
                </a>
        </li>
               ';
    }
    
    if ((in_array($groupID, $adminUserlist)) or (in_array($groupID, $adminUserGroups))) {
        echo ' 
        <li class="nav-parent">
            <a class="nav-link" href="#">
                User und Rechte
            </a>
            <ul class="nav nav-children">
               ';
    }

    if (in_array($groupID, $adminUserlist)) {
        echo ' 
            <li>
                 <a class="nav-link" href="userlist.php">
                    Userliste
                </a>
        </li>
               ';
    }

    if (in_array($groupID, $adminUserGroups)) {
        echo ' 
        <li>
            <a class="nav-link" href="usergroups.php">
                Usergruppen
            </a>
        </li>
               ';
    }

    if ((in_array($groupID, $adminUserlist)) or (in_array($groupID, $adminUserGroups))) {
        echo '
            </ul> 
        </li>'
        ;
    }


    if ((in_array($groupID, $adminUserlist)) or (in_array($groupID, $adminUserGroups))) {
        echo '
                </ul>
            </li>';
    }
?>


<div id="adminMessageAllPlayers" class="modal-block modal-block-sm mfp-hide">
	<section class="card">
		<form method="post">
			<header class="card-header">
				<h2 class="card-title">Adminmessage an ALLE Spieler senden</h2>
			</header>
			<div class="card-body">
				<div class="form-group">
					<label class="control-label text-lg-right pt-2" for="adminMessageAllPlayersID">Message</label>
					<input class="form-control" name="adminMessageAllPlayersValue" id="adminMessageAllPlayersValueID" maxlength="155" type="text" value="">
				</div>
			</div>
			<footer class="card-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button type="submit" id="adminMessageAllPlayersSubmitID" name="adminMessageAllPlayersSubmit" class="btn btn-primary">Senden</button>
						<button class="btn btn-default modal-dismiss">Cancel</button>
					</div>
				</div>
			</footer>
		</form>
	</section>
</div>