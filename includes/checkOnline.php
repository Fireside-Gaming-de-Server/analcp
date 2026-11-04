<?php
session_start();

$_SESSION["redirect"] = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if ((isset($_COOKIE["username"])) && (isset($_COOKIE["usergroup"])) && (isset($_SESSION["rightsArr"]))  && (isset($_COOKIE["identifier"])) && (isset($_COOKIE["securitytoken"]))) {

   include 'includes/function.php';

   $username = $_COOKIE['username'];

   require("includes/mysql.php");
   $stmt = $dbl->prepare("SELECT * FROM acp_users WHERE username=?");
   $stmt->bind_param("s", $username);
   $stmt->execute();
   $result = $stmt->get_result();
   $row = $result->fetch_row();
   if ($row == 0) {
      header("Location: logout.php");
   } else {
      if (($row[5]) == 65) {
         header("Location: logout.php");
      }
      $identifier = $_COOKIE['identifier'];
      $securitytoken = $_COOKIE['securitytoken'];
      $identifierRow = $row[3];
      $securitytokenRow = $row[4];
      $userPID = $row[7];
      $userName = $row[1];
      $_SESSION["Event_userPID"] = $userPID;
      $userGroupId = $row[5];
      
      $LogSupporterPid = $row[7];
      $LogSupporterName = $row[1];

      if ((sha1($securitytoken) !==  $securitytokenRow) or ($identifier !== $identifierRow)) {
         header("Location: logout.php");
      } else {

         $stmtGroups = $dbl->prepare("SELECT group_name FROM acp_usergroups WHERE id=?");
         $stmtGroups->bind_param("i", $userGroupId);
         $stmtGroups->execute();
         $resultGroups = $stmtGroups->get_result();
         $rowGroups = $resultGroups->fetch_row();
         $userGroupname = $rowGroups[0];
         $LogSupporterGroup = $userGroupname;
         if ($userGroupname != ($_COOKIE["usergroup"])) {
            header("Location: logout.php");
         } else {
            $neuer_securitytoke = random_string();
            $neuer_securitytoken = sha1($neuer_securitytoke);
            $update = $dbl->prepare("UPDATE acp_users SET securitytoken=? WHERE username=?");
            $update->bind_param("ss", $neuer_securitytoken, $username);
            $update->execute();
            setcookie("securitytoken", $securitytoken, time() - (3600)); //DestroyIt
            setcookie("securitytoken", $neuer_securitytoke, time() + (43200)); //12 Stunden gültig   
         }
      }
   }
   
} else {
   header("Location: logout.php");
}
