<?php
    require ("includes/mysql.php");

	$stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $_COOKIE["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID = $row[5];

    $permission_name = "logsAdmin";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID_check = $row[2];
    $logsAdmin = json_decode($groupID_check);

    $permission_name = "logsAcp";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID_check = $row[2];
    $logsAcp = json_decode($groupID_check);
 

    $permission_name = "logsA3";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID_check = $row[2];
    $logsA3 = json_decode($groupID_check);
 
    

    /*
    if(false) {

        echo '
        <li class="nav-parent">
        <a>
            <i class="fa fa-database" aria-hidden="true"></i>
            <span>Datenbanken</span>
        </a>
        <ul class="nav nav-children">
            <li class="nav-parent">
                <a>
                    Spieler
                </a>
                <ul class="nav nav-children">
                    <li>
                        <a class="nav-link" href="datenbankSpielerLevelId.php">
                            <span>Level + IDs</span>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="datenbankenSpielerMoneyTime.php">
                            <span>Money + Playtime</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="nav-link" href="dbVehicles.php">
                    <span>Vehicles</span>
                </a>
            </li>
         </ul>
    </li>
            ';
    }

    */
 
    
    if ((in_array($groupID, $logsAdmin)) or (in_array($groupID, $logsAcp)) or (in_array($groupID, $logsA3))) {
        echo '
        <li class="nav-parent">
            <a>
                <i class="fa fa-binoculars" aria-hidden="true"></i>
                <span>Logs</span>
            </a>
            <ul class="nav nav-children">
     ';
    }

    if (in_array($groupID, $logsAcp)) {
        echo '
        <li class="nav-parent">
            <a>
                ACP
            </a>
            <ul class="nav nav-children">
                <li>
                    <a class="nav-link" href="logsAcpSmall.php">
                        Small
                    </a>
                </li>
                <li>
                    <a class="nav-link" href="logsAcpBig.php">
                        Big
                    </a>
                </li>
            </ul>
        ';
    }


   
    if (in_array($groupID, $logsAdmin)) {
        echo '
        <li>
            <a class="nav-link" href="logsAdmin.php">
                Admin
            </a>
        </li>
     ';
    }

    if (in_array($groupID, $logsA3)) {
        echo '
        <li class="nav-parent">
        <a>
            Arma 3 Life
        </a>
        <ul class="nav nav-children">
            <li>
                <a class="nav-link" href="logsA3All.php">
                All-Logs
                </a>
            </li>
            <li>
                <a class="nav-link" href=" logsA3Life_Support.php">
                Life-Support Logs
                </a>
            </li>     
        </ul>
     ';
    }
    

    if ((in_array($groupID, $logsAdmin)) or (in_array($groupID, $logsAcp)) or (in_array($groupID, $logsA3))) {
        echo '
            </ul>
        </li>
        ';
    }


?>

