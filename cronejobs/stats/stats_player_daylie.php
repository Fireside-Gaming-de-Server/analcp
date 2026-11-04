<?php 


require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/mysql.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/includes/function.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/cronejobs/stats/licensesArr.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/cronejobs/stats/housesArr.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/cronejobs/stats/itemsArr.php");
require ("/usr/local/lsws/trinity_web/analcp.trinity-life.de/html/cronejobs/stats/vehiclesArr.php");

$stmt = $dbm->prepare("SELECT pid, playtime, bankacc , cash, civ_licenses, paycheck_total, sold_total, exp_level FROM players WHERE last_seen > NOW() - INTERVAL 1 DAY");
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_array( $result, MYSQLI_ASSOC)) {

    //pid
    $pid = $row['pid'];
    //playTime
    $playTime = explode(",", str_replace("]", "", str_replace("[", "", str_replace("\"", "", $row['playtime']))));
    $playtime_civ = round($playTime[2] / 60);
    $playtime_cop = round($playTime[0] / 60);
    $playtime_med = round($playTime[1] / 60);

    //money
    $money = $row['bankacc'] + $row['cash'];

    
    //licenses
    $licenseWorth = 0;
    if ($row['civ_licenses'] !== '[]') {
        $rowArrLicenseCiv = json_decode($row['civ_licenses']);

        foreach ($rowArrLicenseCiv as $key => $value) {
            if ($value[1] == 1) {
                if (array_key_exists($value[0], $licensesArr)) {
                    $licensePrice = $licensesArr[$value[0]];
                    $licenseWorth = $licenseWorth + $licensePrice;
                }  
            }
        }
    } 
 

    //auktionshaus
 
    $auktionWorth = 0;
    $stmtAuktion = $dbm->prepare("SELECT item, amount FROM auktionshaus WHERE pid=?");
    $stmtAuktion->bind_param("s", $row['pid']);
    $stmtAuktion->execute();
    $resultAuktion = $stmtAuktion->get_result();
    while ($rowAuktion = mysqli_fetch_array( $resultAuktion, MYSQLI_ASSOC)) {
        if (array_key_exists($rowAuktion['item'], $itemsArr)) {
            $auktionWorth = $auktionWorth + ($itemsArr[''.$rowAuktion['item'].''] * $rowAuktion['amount']);
        }
    }


    //kills  tode  self_kills
    $stmtKD = $dbl->prepare("SELECT kills, tode, self_kills FROM stats_kd WHERE pid = ?");
    $stmtKD->bind_param("s", $row['pid']);
    $stmtKD->execute();
    $resultKD = $stmtKD->get_result();
    $Kills = 0;
    $Tode = 0;
    $SelfKillls = 0;
    while ($rowKD = mysqli_fetch_array( $resultKD, MYSQLI_ASSOC)) {
        $Kills = $rowKD['kills'];
        $Tode = $rowKD['tode'];
        $SelfKillls = $rowKD['self_kills'];
    }


    //locker_upgrades, locker_items,
    $lockerWorth = 0;
    $lockerItemsWorth = 0;
    $stmtLocker = $dbm->prepare("SELECT civ_locker, civ_level FROM locker WHERE playerid=?");
    $stmtLocker->bind_param("s", $pid);
    $stmtLocker->execute();
    $resultLocker = $stmtLocker->get_result();
    while ($rowLocker = mysqli_fetch_array( $resultLocker, MYSQLI_ASSOC)) {
        $civ_locker = $rowLocker['civ_locker']; 
        $civ_level = $rowLocker['civ_level'];
        
        $civ_locker = str_replace(']"', "]", str_replace('"[', "[", (translate2($civ_locker))));
        
        $civ_locker = json_decode($civ_locker);
        
        foreach ($civ_locker as $key => $value) {
            if (array_key_exists($value[1], $itemsArr)) {
                $lockerItemsWorth = $lockerItemsWorth + (($itemsArr[$value[1]]) * $value[2]);
            }
        }
    }
    
    if ($civ_level >= 14) {
        $lockerWorth = $lockerWorth + 1000000;
    }
    if ($civ_level >= 13) {
        $lockerWorth = $lockerWorth + 900000;
    }
    if ($civ_level >= 12) {
        $lockerWorth = $lockerWorth + 800000;
    }
    if ($civ_level >= 11) {
        $lockerWorth = $lockerWorth + 700000;
    }
    if ($civ_level >= 10) {
        $lockerWorth = $lockerWorth + 600000;
    }
    if ($civ_level >= 9) {
        $lockerWorth = $lockerWorth + 500000;
    }
    if ($civ_level >= 8) {
        $lockerWorth = $lockerWorth + 400000;
    }
    if ($civ_level >= 7) {
        $lockerWorth = $lockerWorth + 375000;
    }
    if ($civ_level >= 6) {
        $lockerWorth = $lockerWorth + 325000;
    }
    if ($civ_level >= 5) {
        $lockerWorth = $lockerWorth + 275000;
    }
    if ($civ_level >= 4) {
        $lockerWorth = $lockerWorth + 225000;
    }
    if ($civ_level >= 3) {
        $lockerWorth = $lockerWorth + 100000;
    }
    if ($civ_level >= 2) {
        $lockerWorth = $lockerWorth + 75000;
    }
    if ($civ_level >= 1) {
        $lockerWorth = $lockerWorth + 25000;
    }



    //houses, 
    $moneyWorth = 0;

    $stmtHouses = $dbm->prepare("SELECT class, bus, garageHeli, key_slots, garage  FROM houses WHERE pid=?");
    $stmtHouses->bind_param("s", $row['pid']);
    $stmtHouses->execute();
    $resultHouses = $stmtHouses->get_result();


    while ($rowHouses = mysqli_fetch_array( $resultHouses, MYSQLI_ASSOC)) {
        if (array_key_exists(''.$rowHouses['class'].'', $housesArray)) {
            $classPrice = $housesArray[$rowHouses['class']];
            $moneyWorth = $moneyWorth + $classPrice;

           if ($rowHouses['bus'] === 1) {
                $moneyWorth = $moneyWorth + 500000;
            }
            if ($rowHouses['garage'] === 1) {
                $moneyWorth = $moneyWorth + 750000;
            }
            if ($rowHouses['garageHeli'] === 1) {
                $moneyWorth = $moneyWorth + 1400000;
            }
            if ($rowHouses['key_slots'] > 0) {
                $moneyWorth = $moneyWorth + (100000 * $rowHouses['key_slots']);
            }   
        }
    }
    

    //vehicles, tuning,
    $vehicleWorth = 0;
    $tunigWorth = 0;

    $stmtVehicle = $dbm->prepare("SELECT classname, insure, material, motor_tuning, brake_tuning, trunk_tuning, horn_tuning, speed_tuning, chassy_tuning, sound_tuning FROM vehicles WHERE pid=? && side = 'civ' && alive = '1'");
    $stmtVehicle->bind_param("s", $pid);
    $stmtVehicle->execute();
    $resultVehicle = $stmtVehicle->get_result();
    while ($rowVehicle = mysqli_fetch_array( $resultVehicle, MYSQLI_ASSOC)) {
        if (array_key_exists(''.$rowVehicle['classname'].'', $VehicleArray)) {

            $select = $VehicleArray[''.$rowVehicle['classname'].''];
            //PurchasePrice
            $purchasePrice = $select['Purchase'];
            $vehicleWorth = $vehicleWorth + $purchasePrice;

            //InsurePrice
            if (($rowVehicle['insure']) === 1) {
                $insurePrice = $select['Insure'];
                $vehicleWorth = $vehicleWorth + $insurePrice;
            }

            //MaterialPrice
            if (($rowVehicle['material']) != '"[]"') {
                $material = str_replace(']"', "", str_replace('"[', "",translate555($rowVehicle['material'])));
                $materialArr = explode(",", $material);

                $materialPriceArr = $VehicleArray['Materials'];
                $materialPrice = 0;

                foreach ($materialArr as $key => $value) {
                    $value = str_replace('"',"",$value);
                    $materialPrice = $materialPrice + $materialPriceArr[''.$value.''];
                }

                $tunigWorth = $tunigWorth + $materialPrice;
            }   

            //motor_tuning
            if ($rowVehicle['motor_tuning'] != '[1.0]' && $rowVehicle['motor_tuning'] != '[0]' && $rowVehicle['motor_tuning'] != '[1]') {
                $motorTuningString = str_replace(']', "", str_replace('[', "",($rowVehicle['motor_tuning'])));
                $motorTuningString = 'Motor_'.$motorTuningString.'';
                $motorTuningPrice = $select[''.$motorTuningString.''];
                $tunigWorth = $tunigWorth + $motorTuningPrice;
            }

            //brake_tuning
            if (($rowVehicle['brake_tuning']) != '[0]') {
                $brakeTuningString = str_replace(']', "", str_replace('[', "",($rowVehicle['brake_tuning'])));
                $brakeTuningString = 'Brake_'.$brakeTuningString.'';
                $brakeTuningPrice = $select[''.$brakeTuningString.''];
                $tunigWorth = $tunigWorth + $brakeTuningPrice;
            }   

            //trunk_tuning
            if ($rowVehicle['trunk_tuning'] != '[1.0]' && $rowVehicle['trunk_tuning'] != '[0]' && $rowVehicle['motor_tuning'] != '[1]') {
                $trunkTuningString = str_replace(']', "", str_replace('[', "",($rowVehicle['trunk_tuning'])));
                $trunkTuningString = 'Trunk_'.$trunkTuningString.'';
                $trunkTuningPrice = $select[''.$trunkTuningString.''];
                $tunigWorth = $tunigWorth + $trunkTuningPrice;
            }

            //horn_tuning
            if (($rowVehicle['horn_tuning']) != '[]') {
                $tunigWorth = $tunigWorth + 100000;
            }

            //speed_tuning
            if ($rowVehicle['speed_tuning'] != '[1.0]' && $rowVehicle['speed_tuning'] != '[0]' && $rowVehicle['motor_tuning'] != '[1]') {
                $speedTuningString = str_replace(']', "", str_replace('[', "",($rowVehicle['speed_tuning'])));
                $speedTuningString = 'Speed_'.$speedTuningString.'';
                $speedTuningPrice = $select[''.$speedTuningString.''];
                $tunigWorth = $tunigWorth + $speedTuningPrice;
            }

            //chassy_tuning
            if ($rowVehicle['chassy_tuning'] != '[1.0]' && $rowVehicle['chassy_tuning'] != '[1]' && $rowVehicle['chassy_tuning'] != '[0]') {
                $ChassisTuningString = str_replace(']', "", str_replace('[', "",($rowVehicle['chassy_tuning'])));
                $ChassisTuningString = 'Chassis_'.$ChassisTuningString.'';
                $ChassisTuningPrice = $select[''.$ChassisTuningString.''];
                $tunigWorth = $tunigWorth + $ChassisTuningPrice;
            }

            //sound_tuning
            if (($rowVehicle['sound_tuning']) != '[""]') {
                $tunigWorth = $tunigWorth + 500000;
            }
        }
    }

    //quest_level, 
    $stmtquest_level = $dbm->prepare("SELECT quest_level FROM questsystem WHERE pid = ?");
    $stmtquest_level->bind_param("s", $row['pid']);
    $stmtquest_level->execute();
    $resultquest_level = $stmtquest_level->get_result();
    $questLevel = 0;
    while ($rowquest_level = mysqli_fetch_array( $resultquest_level, MYSQLI_ASSOC)) {
        $questLevel = $rowquest_level['quest_level']; 
    }

    //civ_points, cop_points, med_points
    $stmtPoints = $dbm->prepare("SELECT civ_points, cop_points, med_points FROM aktivitaetenshop WHERE pid = ?");
    $stmtPoints->bind_param("s", $row['pid']);
    $stmtPoints->execute();
    $resultPoints = $stmtPoints->get_result();
    $civ_points = 0;
    $cop_points = 0;
    $med_points = 0;
    while ($rowPoints = mysqli_fetch_array( $resultPoints, MYSQLI_ASSOC)) {
        $civ_points = $rowPoints['civ_points'];
        $cop_points = $rowPoints['cop_points'];
        $med_points = $rowPoints['med_points'];
    }


    $stmt = $dbl->prepare('INSERT INTO stats_player_daylie (pid, playtime_civ, playtime_cop, playtime_med, money, licenses, paycheck_total, sold_total, auktionshaus, kills, tode, self_kills, locker_upgrades, locker_items, houses, vehicles, tuning, exp_level, quest_level, civ_points, cop_points, med_points) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->bind_param("siiiiiiiiiiiiiiiiiiiii", $pid, $playtime_civ,$playtime_cop, $playtime_med, $money, $licenseWorth, $row['paycheck_total'],$row['sold_total'], $auktionWorth, $Kills, $Tode, $SelfKillls, $lockerWorth, $lockerItemsWorth, $moneyWorth, $vehicleWorth, $tunigWorth, $row['exp_level'], $questLevel, $civ_points , $cop_points, $med_points);
    $stmt->execute();

} 




?>