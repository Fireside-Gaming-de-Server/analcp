<?php


$dbl = new mysqli("45.83.245.57", "acp_web_user", "7Uozi2%56", "analcp_database");
if ($dbl->connect_errno) {
    echo 1;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}

$dbpc = new mysqli("45.83.245.57", "acp_web_user", "7Uozi2%56", "playercheck");
if ($dbpc->connect_errno) {
    echo 2;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}

$dbup = new mysqli("45.83.245.57", "acp_web_user", "7Uozi2%56", "userpanel_database");
if ($dbup->connect_errno) {
    echo 3;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}

$dbs = new mysqli("45.83.245.57", "acp_web_user", "7Uozi2%56", "security");
if ($dbs->connect_errno) {
    echo 5;
    die("Verbindung fehlgeschlagen: " . $dbs->connect_error);
}

$dbm = new mysqli("37.221.94.20", "ACP", "8DbsBqjFRqWuG7kQtauN8es8c5JJbsnAemG9rE4d4PfHWnsGDHHHWc3uBP9P2mNH", "altislife");
if ($dbm->connect_errno) {
    echo 6;
    die("Verbindung fehlgeschlagen: " . $dbm->connect_error);
}



/*
$dbll = new mysqli("51.89.96.109", "ACP", "dhF634iVSvMdw827LJPA9Z", "logs");
if ($dbll->connect_errno) {
    echo 4;
    die("Verbindung fehlgeschlagen: " . $dbll->connect_error);
}

$dbs = new mysqli("51.89.96.109", "ACP", "dhF634iVSvMdw827LJPA9Z", "security");
if ($dbs->connect_errno) {
    echo 5;
    die("Verbindung fehlgeschlagen: " . $dbs->connect_error);
}

$dbm = new mysqli("51.89.96.109", "ACP", "dhF634iVSvMdw827LJPA9Z", "altislife");
if ($dbm->connect_errno) {
    echo 6;
    die("Verbindung fehlgeschlagen: " . $dbm->connect_error);
}
*/







/*
$dbl = new mysqli("45.83.245.57", "acp_web_user", "k3zxMd1NB6!17LPby", "acp_database");
if ($dbl->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}

$dbpc = new mysqli("45.83.245.57", "acp_web_user", "k3zxMd1NB6!17LPby", "playercheck");
if ($dbl->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}

$dbs = new mysqli("51.195.105.122", "ACP", "1m2AhUK0tILCxNtxNYaEPsfVlqVUBkEc", "security");
if ($dbs->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbs->connect_error);
}

$dbup = new mysqli("45.83.245.57", "acp_web_user", "k3zxMd1NB6!17LPby", "userpanel_database");
if ($dbup->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbl->connect_error);
}

$dbll = new mysqli("51.195.105.122", "ACP", "1m2AhUK0tILCxNtxNYaEPsfVlqVUBkEc", "logs");
if ($dbs->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbs->connect_error);
}


$dbm = new mysqli("51.195.105.122", "ACP", "1m2AhUK0tILCxNtxNYaEPsfVlqVUBkEc", "altislife");
if ($dbm->connect_errno) {
    echo 1234;
    die("Verbindung fehlgeschlagen: " . $dbm->connect_error);
}
*/

?>