<?php
    require ("includes/mysql.php");

    $stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
	$stmt->bind_param("s", $_COOKIE["username"]);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID = $row[5];

    $permission_name = "devToDO";
    $stmt = $dbl->prepare("SELECT * FROM acp_permissions WHERE permission_name = ?");
	$stmt->bind_param("s", $permission_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_row();
    $groupID_check = $row[2];
    $devToDO = json_decode($groupID_check);

/*
    if(false) {

        echo '
        <li class="nav-parent">
            <a class="nav-link" href="#">
                <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                <span>Development</span>
            </a>
            <ul class="nav nav-children">
                <li>
                    <a class="nav-link" href="">
                        ToDo-Liste
                    </a>
                </li>
             </ul>
        </li>
            ';
    }
*/

    if (in_array($groupID, $devToDO))  {
        echo ' 
            <li class="nav-parent">
                <a class="nav-link" href="#">
                    <i class="fa fa-code" aria-hidden="true"></i>
                    <span>Development</span>
                </a>
                ';
    }

    if (in_array($groupID, $devToDO))  {
        echo ' 
        <ul class="nav nav-children">
            <li>
                <a class="nav-link" href="devToDo.php">
                    ToDo-Liste
                </a>
            </li>
        </ul>
                ';
    }



    if (in_array($groupID, $devToDO))  {
        echo '
            </li>';
    }




?>

