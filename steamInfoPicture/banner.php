<?php
require ("../includes/mysql.php");


//Set the Content Type
header('Content-type: image/png');

// Create Image From Existing File

$jpg_image = imagecreatefrompng('banner.png');

// Allocate A Color For The Text
$white = imagecolorallocate($jpg_image, 205, 205, 205);
$black = imagecolorallocate($jpg_image, 255, 0, 0);

// Set Path to Font File
$font_path = dirname(__FILE__) . '/SkinSans.ttf';

// Set Text to Be Printed On Image

$stmt = $dbl->prepare("SELECT civs, cops, medics FROM cache_onlinePlayers WHERE id=1");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_row();
$copCount = $row[1];
$medCount = $row[2];
$civCount = $row[0];
$allCount = $copCount + $medCount + $civCount;

// Set Text to Be Printed On Image

$copText = $copCount;
if ($copCount > 2) {
    $copText = '3+';
}
if ($copCount >= 5) {
    $copText = '5+';
}
if ($copCount >= 8) {
    $copText = '8+';
}
if ($copCount >= 10) {
    $copText = '10+';
}
if ($copCount >= 15) {
    $copText = '15+';
}
if ($copCount >= 20) {
    $copText = '20+';
}



$all_x = 315;
$cop_x = 450;
$med_x = 570;
$civ_x = 700;

$allg_y = 34;

//All NUM
imagettftext($jpg_image, 20, 0, $all_x, $allg_y, $white, $font_path, $allCount );

//COP NUM
imagettftext($jpg_image, 20, 0, $cop_x, $allg_y, $white, $font_path, $copText );

//Med Num
imagettftext($jpg_image, 20, 0, $med_x, $allg_y, $white, $font_path, $medCount);

//CIV NUM
imagettftext($jpg_image, 20, 0, $civ_x, $allg_y, $white, $font_path, $civCount);


// Send Image to Browser
imagejpeg($jpg_image, NULL, 100); 

// Clear Memory
imagedestroy($jpg_image);

?>