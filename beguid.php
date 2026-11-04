<?php 
$pid = '76561198313482907';
$steamID = $pid;
$temp = '';

for ($i = 0; $i < 8; $i++) {
    $temp .= chr($steamID & 0xFF);
    $steamID >>= 8;
}

$return = md5('BE' . $temp);
$guid = $return;

echo $guid;