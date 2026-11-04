<?php 
require('includes/checkOnline.php');
require('includes/function.php');

function StringToArray($String) {
    $json = str_replace('`', '"', $String);
    $Array = json_decode(trim($json, '"'), true);
    return $Array;
}


$stmt = $dbm->prepare("SELECT * FROM players WHERE pid= 76561198253962451");
$stmt->execute();
$result = $stmt->get_result();

while ($row = mysqli_fetch_assoc($result)) {
	$GearArray		= StringToArray($row["civ_gear"]);
}

echo "<pre>";
print_r($GearArray);
echo "</pre>";

echo "**************************************************************";

// function DisplayGear($GearArray) {		
		$OutputGearArray = array();
		$inventory = $GearArray[0] ?? "";
		$i = 0;
		$OutputGearArray["primary"] = array(
			"clasname" => $inventory[$i][0] ?? "",
			"supressor" => $inventory[$i][1] ?? "",
			"pointer" => $inventory[$i][2] ?? "",
			"optics" => $inventory[$i][3] ?? "",
			"mainmag" => array(
				"classname" => $inventory[$i][4][0] ?? "",
				"bulletcount" => $inventory[$i][4][1] ?? 0,
			),
			"secondarymag" => array(
				"classname" => $inventory[$i][5][0] ?? "",
				"bulletcount" => $inventory[$i][5][1] ?? 0,
			),
			"bipod" => $inventory[$i][6] ?? "",
		);
		$i = 1;
		$OutputGearArray["launcher"] = array(
			"clasname" => $inventory[$i][0] ?? "",
			"supressor" => $inventory[$i][1] ?? "",
			"pointer" => $inventory[$i][2] ?? "",
			"optics" => $inventory[$i][3] ?? "",
			"mainmag" => array(
				"classname" => $inventory[$i][4][0] ?? "",
				"bulletcount" => $inventory[$i][4][1] ?? 0,
			),
			"secondarymag" => array(
				"classname" => $inventory[$i][5][0] ?? "",
				"bulletcount" => $inventory[$i][5][1] ?? 0,
			),
			"bipod" => $inventory[$i][6] ?? "",
		);
		$i = 2;
		$OutputGearArray["handgun"] = array(
			"clasname" => $inventory[$i][0] ?? "",
			"supressor" => $inventory[$i][1] ?? "",
			"pointer" => $inventory[$i][2] ?? "",
			"optics" => $inventory[$i][3] ?? "",
			"mainmag" => array(
				"classname" => $inventory[$i][4][0] ?? "",
				"bulletcount" => $inventory[$i][4][1] ?? 0,
			),
			"secondarymag" => array(
				"classname" => $inventory[$i][5][0] ?? "",
				"bulletcount" => $inventory[$i][5][1] ?? 0,
			),
			"bipod" => $inventory[$i][6] ?? "",
		);
		$i = 3;
		$OutputGearArray["uniform"] = array(
			"clasname" => $inventory[$i][0] ?? ""
		);
		if (is_array($inventory[$i][1] ?? "")) {
			foreach ($inventory[$i][1] as $key => $value) {
				if (is_array($value[0])) { //is ne Waffel [[`aka_speedgun`,``,``,``,[`aka_speedgun_mag`,1e+007],[],``],1]
					$OutputGearArray["uniform"]["items"][] = array(
						"clasname" => $value[0][0] ?? "",
						"supressor" => $value[0][1] ?? "",
						"pointer" => $value[0][2] ?? "",
						"optics" => $value[0][3] ?? "",
						"mainmag" => array(
							"classname" => $value[0][4][0] ?? "",
							"bulletcount" => $value[0][4][1] ?? 0,
						),
						"secondarymag" => array(
							"classname" => $value[0][5][0] ?? "",
							"bulletcount" => $value[0][5][1] ?? 0,
						),
						"bipod" => $value[0][6] ?? "",
						"count" => $value[1][0] ?? 0,
					);
				} elseif (is_bool($value[0])) { //Nen Container
					$OutputGearArray["uniform"]["items"][] = array(
						"classname" => $value[0],
						"type" => $value[1],
					);
				} elseif (count($value) == 3) { //Nen Mag
					$OutputGearArray["uniform"]["items"][] = array(
						"classname" => $value[0],
						"count" => $value[1],
						"bulletcount" => $value[2],
					);
				} else { //Nen Container
					$OutputGearArray["uniform"]["items"][] = array(
						"classname" => $value[0],
						"count" => $value[1]
					);
				}
			}
		} else {
			$OutputGearArray["uniform"]["items"] = array();
		}
		
		$i = 4;
		$OutputGearArray["vest"] = array(
			"clasname" => $inventory[$i][0] ?? ""
		);
		if (is_array($inventory[$i][1] ?? "")) {
			foreach ($inventory[$i][1] as $key => $value) {
				if (is_array($value[0])) { //is ne Waffel [[`aka_speedgun`,``,``,``,[`aka_speedgun_mag`,1e+007],[],``],1]
					$OutputGearArray["vest"]["items"][] = array(
						"clasname" => $value[0][0] ?? "",
						"supressor" => $value[0][1] ?? "",
						"pointer" => $value[0][2] ?? "",
						"optics" => $value[0][3] ?? "",
						"mainmag" => array(
							"classname" => $value[0][4][0] ?? "",
							"bulletcount" => $value[0][4][1] ?? 0,
						),
						"secondarymag" => array(
							"classname" => $value[0][5][0] ?? "",
							"bulletcount" => $value[0][5][1] ?? 0,
						),
						"bipod" => $value[0][6] ?? "",
						"count" => $value[1][0] ?? 0,
					);
				} elseif (is_bool($value[0])) { //Nen Container
					$OutputGearArray["vest"]["items"][] = array(
						"classname" => $value[0],
						"type" => $value[1],
					);
				} elseif (count($value) == 3) { //Nen Mag
					$OutputGearArray["vest"]["items"][] = array(
						"classname" => $value[0],
						"count" => $value[1],
						"bulletcount" => $value[2],
					);
				} else { //Nen Container
					$OutputGearArray["vest"]["items"][] = array(
						"classname" => $value[0],
						"count" => $value[1]
					);
				}
			}
		} else {
			$OutputGearArray["vest"]["items"] = array();
		}
		
		$i = 5;
		$OutputGearArray["backpack"] = array(
			"clasname" => $inventory[$i][0] ?? ""
		);
		if (is_array($inventory[$i][1] ?? "")) {
			foreach ($inventory[$i][1] as $key => $value) {
				if (is_array($value[0])) { //is ne Waffel [[`aka_speedgun`,``,``,``,[`aka_speedgun_mag`,1e+007],[],``],1]
					$OutputGearArray["backpack"]["items"][] = array(
						"clasname" => $value[0][0] ?? "",
						"supressor" => $value[0][1] ?? "",
						"pointer" => $value[0][2] ?? "",
						"optics" => $value[0][3] ?? "",
						"mainmag" => array(
							"classname" => $value[0][4][0] ?? "",
							"bulletcount" => $value[0][4][1] ?? 0,
						),
						"secondarymag" => array(
							"classname" => $value[0][5][0] ?? "",
							"bulletcount" => $value[0][5][1] ?? 0,
						),
						"bipod" => $value[0][6] ?? "",
						"count" => $value[1][0] ?? 0,
					);
				} elseif (is_bool($value[0])) { //Nen Container
					$OutputGearArray["backpack"]["items"][] = array(
						"classname" => $value[0],
						"type" => $value[1],
					);
				} elseif (count($value) == 3) { //Nen Mag
					$OutputGearArray["backpack"]["items"][] = array(
						"classname" => $value[0],
						"count" => $value[1],
						"bulletcount" => $value[2],
					);
				} else { //Nen Container
					$OutputGearArray["backpack"]["items"][] = array(
						"classname" => $value[0],
						"count" => $value[1]
					);
				}
			}
		} else {
			$OutputGearArray["backpack"]["items"] = array();
		}
		
		$i = 6;
		$OutputGearArray["binoculars"] = array(
			"clasname" => $inventory[$i][0] ?? "",
			"supressor" => $inventory[$i][1] ?? "",
			"pointer" => $inventory[$i][2] ?? "",
			"optics" => $inventory[$i][3] ?? "",
			"mainmag" => array(
				"classname" => $inventory[$i][4][0] ?? "",
				"bulletcount" => $inventory[$i][4][1] ?? 0,
			),
			"secondarymag" => array(
				"classname" => $inventory[$i][5][0] ?? "",
				"bulletcount" => $inventory[$i][5][1] ?? 0,
			),
			"bipod" => $inventory[$i][6] ?? "",
		);
		
		$i = 7;
		$OutputGearArray["map"] = $inventory[$i][0] ?? "";
		$OutputGearArray["gps"] = $inventory[$i][1] ?? "";
		$OutputGearArray["radio"] = $inventory[$i][2] ?? "";
		$OutputGearArray["compass"] = $inventory[$i][3] ?? "";
		$OutputGearArray["watch"] = $inventory[$i][4] ?? "";
		$OutputGearArray["nvg"] = $inventory[$i][5] ?? "";



		echo "<pre>";
		print_r($OutputGearArray);
		echo "</pre>";

    // foreach ($inventory[3][1] as $key => $value) {
		
    // }
    
// }
?>